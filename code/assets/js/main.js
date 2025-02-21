jQuery(document).ready(function ($) {
    function loadData(sessionID) {
        $.ajax({
            url: 'single.php',
            method: 'POST',
            data: {
                action: 'get_data',
                sessionID: sessionID
            },
            dataType: 'json',
            beforeSend: function () {
                $('#sessionData').html('<p>Lädt...</p>');
                $('#sessionDetailPopup').fadeIn();
                $('#sessionDetailPopup').css("display", "flex");
            },
            success: function (response) {
                console.log(response);
                if (response && response.success === true) {
                    $('#sessionData').html(response.html);
                } else {
                    $('#sessionData').html('<p>Keine Daten gefunden.</p>');
                }
            },
            error: function () {
                $('#sessionData').html('<p>Fehler beim Abrufen der Daten.</p>');
            }            
        });
    }

    $('.sessionID').on('click', function () {
        let sessionID = $(this).val().trim();
        loadData(sessionID);
    });

    // Schließen des Popups beim Klicken auf das "x"
    $('#closePopup').on('click', function () {
        $('#sessionDetailPopup').fadeOut();
    });

    // Schließen des Popups beim Klick außerhalb des Inhalts
    $(window).on('click', function (event) {
        if (event.target.id === 'sessionDetailPopup') {
            $('#sessionDetailPopup').fadeOut();
        }
    });
});