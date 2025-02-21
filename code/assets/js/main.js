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

    $(document).on('click', '.sessionID', function () {
        console.log("clicked on session");
        let sessionID = $(this).val().trim();
        loadData(sessionID);
    });
    

    $('#closePopup').on('click', function () {
        $('#sessionDetailPopup').fadeOut();
    });

    $(window).on('click', function (event) {
        if (event.target.id === 'sessionDetailPopup') {
            $('#sessionDetailPopup').fadeOut();
        }
    });



    function loadPage(page) {
        $.ajax({
            url: 'pagination.php',
            method: 'GET',
            data: { page: page },
            dataType: 'json',
            beforeSend: function () {
                $('#results tbody').html('<tr><td colspan="4" class="text-center py-4">Lädt...</td><td></td><td></td></tr>');
            },
            success: function (response) {
                if (response.success) {
                    $('#results tbody').html(response.html);
                    $('.pagination-controls').html(response.pagination);
                } else {
                    $('#results tbody').html('<tr><td colspan="4" class="text-center py-4">Keine Daten gefunden.</td></tr>');
                }
            },
            error: function () {
                $('#results tbody').html('<tr><td colspan="4" class="text-center py-4 text-red-500">Fehler beim Laden der Daten.</td></tr>');
            }
        });
    }

    // Pagination Buttons Event-Listener
    $(document).on('click', '.pagination-btn', function () {
        let page = $(this).data('page');
        loadPage(page);
    });

    // Lade die erste Seite beim intitialen Laden der Website
    loadPage(1);


    $(document).on('click', '#switcher_section li a', function (event) {
        event.preventDefault(); //
    
        let selectedButton = $(this);
        let selectedButtonID = selectedButton.attr("id");
        console.log("Ausgewählt: " + selectedButtonID);
    
        let elementSection = "#" + selectedButtonID + "_section";
        let elementSectionHTML = $(elementSection);
    

        if (!selectedButton.hasClass("active")) {
            $('#switcher_section li a').removeClass("active text-blue-600 bg-gray-100 dark:bg-gray-800 dark:text-blue-500")
            $('#switcher_section li a').addClass("hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:text-gray-300");
    
            selectedButton.addClass("active text-blue-600 bg-gray-100 dark:bg-gray-800 dark:text-blue-500")
            selectedButton.removeClass("hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:text-gray-300");
    
            $(".general_data").addClass("hidden").removeClass("active");
    
            elementSectionHTML.removeClass("hidden").addClass("active");
        }
    });
    
});


