$(document).ready(async function() {
    "use strict";

    const days = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
    const SELECTED_COLOR = '#4CAF50'; // Consistent color for selected time slots

    function getURLParameter(name) {
        return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20')) || null;
    }
    

    async function getAvailability() {
        let username = getURLParameter('username');
        return $.ajax({
            url: '../../controllers/BookingController.php',
            method: 'GET',
            data: {
                action: 'manageAvailability',
                username: username
            },
            success: function(response) {
                if (response.status === 'success') {
                    if (response.availability === null || Object.keys(response.availability).length === 0) {
                        // Display a toast message
                        $('#toast-message')
                            .toast('show');
                        $('#toast-text').text('Please update your availability.');
                        throw new Error('No availability data found.');
                    }
                } else {
                    // Display a toast message
                    $('#toast-message')
                            .toast('show');
                        $('#toast-text').text(response.message || 'An error occurred while fetching the availability');
                    throw new Error(response.message || 'An error occurred while fetching the availability');
                }
            }
        });
    }

    function createSlots(availability) {
        let tbody = $('#availability');
    
        for (let hour = 0; hour < 24; hour++) {
            let tr = $('<tr></tr>');
            for (let day = 0; day < 7; day++) {
                let td = $('<td></td>');
                if (day === 6) td.addClass('week-end');
                let span = $('<span class="time clickable"></span>');
                span.data('day', days[day]);
                span.data('hour', hour.toString()); // store hour as string to match server format
                span.text((hour < 10 ? '0' : '') + hour + ':00');
    
                // Check if the timeslot is in the availability object
                if (availability[days[day]] && availability[days[day]].includes(hour.toString())) {
                    span.addClass('selected');
                    span.css('background-color', SELECTED_COLOR);
                }
    
                span.on('click', function() {
                    $(this).toggleClass('selected');
                    $(this).css('background-color', $(this).hasClass('selected') ? SELECTED_COLOR : '');
                });
    
                td.append(span);
                tr.append(td);
            }
            tbody.append(tr);
        }
    }
    

    // Main logic
    try {
        let response = await getAvailability();
        if (response.status === 'success') {
            createSlots(response.availability);
        }
    } catch (error) {
        console.log('jqXHR:', error.jqXHR);
        console.log('textStatus:', error.textStatus);
        console.log('errorThrown:', error.errorThrown);
        // Display a toast message
        $('#toast-message')
            .toast('show');
        $('#toast-text').text('An error occurred while making the request: ' + error.errorThrown);
    }
});
