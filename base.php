<?php
define("API_URL", "https://stream.watsonplatform.net/text-to-speech/api/v1/");
define("USERNAME", "");
define("PASSWORD", "");

if (!isset($_POST['text']) || empty(trim($_POST['text']))) {
    echo "Empty request";
} else {
    try {
        sendTextToWatson($_POST);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . ", line " . $e->getLine();
    }
}

/**
 * @param $data
 */
function sendTextToWatson($data) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, API_URL . "synthesize?voice=" . $_POST['voice']);
    curl_setopt($ch, CURLOPT_USERPWD, USERNAME . ":" . PASSWORD);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array("text" => $data['text'])));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Accept: audio/ogg'
    ));
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);

    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status != 200) {
        $response = json_decode($response, true);
        http_response_code($status);
        exit( $response['error'] . "<br />" . $response['description'] );
    }

    $filename = "output/";
    if (isset($_POST['filename']) && !empty($_POST['filename'])) {
        if (file_exists(__DIR__ . '/output/' . $_POST['filename'] . '.ogg' )) {
            $filename .= $_POST['filename'] . '_' . time() . '.ogg';
        } else {
            $filename .= $_POST['filename'] . '.ogg';
        }
    } else {
        $filename .= 'audio' . time() . '.ogg';
    }

    if (!file_exists(__DIR__ . '/output/')) {
        mkdir(__DIR__ . '/output/', 0777);
    }

    $file = fopen($filename, 'w');
    fwrite($file, $response);
    fclose($file);

    echo $filename;
}