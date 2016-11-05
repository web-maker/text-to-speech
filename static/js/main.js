$(document).ready(function () {
    var form = $('#text-form'),
        result = $('#result'),
        player = $('#audio-player'),
        progressBar = $('.progress');

    form.submit(function(){
        var text = $('#text').val().trim();
        if (text) {
            result.text('');
            sendRequest(form);
        } else {
            result.text("Empty input");
        }
        return false;
    });

    function sendRequest(form) {
        $.ajax({
            method: "POST",
            url: form.attr('action'),
            data: form.serialize(),
            beforeSend: function() {
                progressBar.removeClass('hidden');
            },
            complete: function() {
                progressBar.addClass('hidden');
            }
        }).done(function(response) {
            result.html('Saved as ' + response);
            player.find('source').attr('src', response);
            player[0].load();
            player[0].play();
        }).fail(function (response) {
            result.html(response.responseText);
        });
    }
});