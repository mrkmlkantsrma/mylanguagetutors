$(document).ready(function() {
    "use strict";

    // Function to handle errors
    const handleError = (errorThrown, defaultMsg) => {
        console.log('Error:', errorThrown);
        $('#toast-message')
            .toast('show');
        $('#toast-text').text(defaultMsg || 'An error occurred, Please try again: ' + errorThrown);
    };

    // Add 'site-link small' classes to the button
    $('#save-availability').addClass('site-link small');

// Handle the Save Availability button click
$('#save-availability').on('click', function() {
    let availability = {};
    $('.time.selected').each(function() {
        let day = $(this).data('day');
        let hour = $(this).data('hour');
        if (day !== null && hour !== null) {
            if (!availability.hasOwnProperty(day)) {
                availability[day] = [];
            }
            availability[day].push(hour);
        }
    });

    // Change the button text to "Saving..."
    $(this).text('Saving...');

    // Make the AJAX request
    $.ajax({
        url: '../../controllers/BookingController.php',
        method: 'POST',
        dataType: 'json',
        data: {
            action: 'manageAvailability',
            availability: availability
        },
        success: function(response) {
            // Restore the original button text
            $('#save-availability').text('Save Availability');

            console.log('Server response:', response);
            if (response.status === 'success') {
                $('#toast-message').toast('show');
                $('#toast-text').text(response.message);
            } else {
                handleError(response.message, 'An error occurred while updating the availability');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            // Restore the original button text
            $('#save-availability').text('Save Availability');

            handleError(errorThrown, 'An error occurred while making the request.');
        }
    });
});

    // Handle the Edit button click for timezone
    $('#timezone-edit-button').on('click', function() {
        let selectedTimezone = $('#timezone-select').val();

        // Make the AJAX request
        $.ajax({
            url: '../../controllers/BookingController.php',
            method: 'POST',
            dataType: 'json',
            data: {
                action: 'updateTimezone',
                timezone: selectedTimezone
            },
            success: function(response) {
                console.log('Server response:', response);
                if (response.status === 'success') {
                    $('#toast-message')
                        .toast('show');
                    $('#toast-text').text(response.message);
                } else {
                    handleError(response.message, 'An error occurred while updating the timezone');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                handleError(errorThrown, 'An error occurred while making the request.');
            }
        });
    });

    // Fetch the current timezone from the server when the page loads
    $.ajax({
        url: '../../controllers/BookingController.php',
        method: 'POST',
        dataType: 'json',
        data: {
            action: 'getCurrentTimezone'
        },
        success: function(response) {
            console.log('Server response:', response);
            if (response.status === 'success') {
                let currentTimezone = response.timezone;
                // Set the current timezone as the selected option
                $('#timezone-select').val(currentTimezone);
            } else {
                handleError(response.message, 'An error occurred while fetching the timezone');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            //handleError(errorThrown, 'Update your availability timezone.');
        }
    });
});
