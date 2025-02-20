jQuery(document).ready(function ($) {
    function loadData(params = {}) {
        $.ajax({
            url: 'single.php',
            method: 'POST',
            data: {
                action: 'get_data',
                ...params,
            },
            dataType: 'json',
            beforeSend: function () {
                $('#results tobdy').html('<p>Lädt...</p>');
            },
            success: function (response) {
                console.log(response);
                if (response && response.success === true) {
                    $('#results tbody').html(response.html);
                } else {
                    $('#results tbody').html('<p>Keine Daten gefunden.</p>');
                }
            },
            error: function () {
                $('#results tbody').html('<p>Fehler beim Abrufen der Daten.</p>');
            }            
        });
    }

    let sessionIDs = document.querySelectorAll('.sessionID');
    sessionIDs.forEach(
        element =>{
            element.onclick=function(){
                console.log(element.textContent.trim());
                loadData({
                    sessionID: element.textContent.trim()
                });
            }
        }
    )
});