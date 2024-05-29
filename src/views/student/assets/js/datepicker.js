const SELECTED_COLOR = "#4CAF50"; // Consistent color for selected time slots
const BOOKED_COLOR = "#fdb777";
const daysOfWeek = ["sun", "mon", "tue", "wed", "thu", "fri", "sat"];

function getURLParameter(name) {
    return decodeURIComponent((new RegExp("[?|&]" + name + "=" + "([^&;]+?)(&|#|;|$)").exec(location.search) || [, ""])[1].replace(/\+/g, "%20")) || null;
}

async function getTimezone() {
    let username = getURLParameter("username");
    return $.ajax({
        url: "../../controllers/BookingController.php",
        method: "GET",
        data: {
            action: "getTutorTimezoneByUsername",
            username: username,
        },
        dataType: "json",
    });
}

async function getAvailability() {
    let username = getURLParameter("username");
    return $.ajax({
        url: "../../controllers/BookingController.php",
        method: "GET",
        data: {
            action: "getTutorAvailabilityByUsername",
            username: username,
        },
        dataType: "json",
    });
}

function getBookedSlots(tutorUsername) {
    return fetch(`../../controllers/BookingController.php?action=getBookedSlots&tutor_username=${tutorUsername}`, {
        method: "GET",
        credentials: "same-origin",
    })
    .then((response) => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then((data) => {
        if (data.status === "success") {
            return data;
        } else {
            throw new Error(`Error fetching booked slots: ${data.message}`);
        }
    })
    .catch((e) => {
        console.log("There was an error in fetching booked slots: ", e);
    });
}

function slotOverlapsWithBookedSlot(slotTime, bookedSlots) {
    return bookedSlots.some(bookedSlot => {
        let date = new Date(bookedSlot);
        return date.getUTCHours() === parseInt(slotTime);
    });
}



function createEvents(availability, bookedSlots) {
    let events = [];
    for (let day in availability) {
        let dayIndex = daysOfWeek.indexOf(day);
        for (let hour of availability[day]) {
            // If the slot overlaps with a booked slot, continue to the next iteration
            if (slotOverlapsWithBookedSlot(hour, bookedSlots)) {
                continue;
            }
            let event = {
                title: "Available",
                daysOfWeek: [dayIndex.toString()],
                startTime: `${hour}:00:00`,
                endTime: `${parseInt(hour) + 1}:00:00`,
                color: SELECTED_COLOR,
                classNames: ["available-slot"],
            };
            events.push(event);
        }
    }

    for (let bookedSlot of bookedSlots) {
        let date = new Date(bookedSlot);
        if (!isNaN(date)) {
            let hours = date.getUTCHours().toString().padStart(2, '0'); // No need to add 1 hour
            let minutes = date.getUTCMinutes().toString().padStart(2, '0');
            let seconds = date.getUTCSeconds().toString().padStart(2, '0');
    
            let startDateTime = new Date(date);
            startDateTime.setHours(hours, minutes, seconds);
    
            let endDateTime = new Date(date);
            endDateTime.setHours((parseInt(hours) + 1) % 24, minutes, seconds);
    
            let event = {
                title: "Booked",
                start: startDateTime.toISOString(),
                end: endDateTime.toISOString(),
                color: BOOKED_COLOR,
                classNames: ["booked-slot"],
                editable: false // making the booked slot non-clickable
            };
            events.push(event);
        }
    }
    
    

    return events;
}



document.addEventListener("DOMContentLoaded", async function () {
    var continueButton = document.getElementById("continue-button");

    if (continueButton) {
        setupContinueButton();
    } else {
        await setupTutorAvailability();
    }
});

function setupContinueButton() {
    var continueButton = document.getElementById("continue-button");
    continueButton.addEventListener("click", function (e) {
        e.preventDefault();

        var tutorUsername = getURLParameter("username");
        var tutorLanguages = getURLParameter("languages");
        window.location.href = "availability" + "?username=" + tutorUsername + "&languages=" + tutorLanguages;
    });
}


async function setupTutorAvailability() {
    var tutorUsername = getURLParameter("username");
    var tutorLanguages = getURLParameter("languages");

    // Fetch the availability data
    let availabilityData = await getAvailability();
    // Fetch the booked slots data
    let bookedSlotsData = await getBookedSlots(tutorUsername); // Fetching bookedSlotsData here
    // Fetch the timezone data
    let timezoneData = await getTimezone();
    // Fetch plan details
    let planDetails = await fetch("../../controllers/PlanDetailsController.php", {
        method: "POST",
        credentials: "same-origin",
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .catch((e) => console.log("There was an error in your fetch operation: ", e));

    // Initialize the confirmation modal
    let confirmModal = new bootstrap.Modal(document.getElementById("confirm-modal"));
    let confirmModalBody = document.getElementById("confirm-modal-body");
    let confirmButton = document.getElementById("confirm-button");

    // Initialize the success modal
    let successModal = new bootstrap.Modal(document.getElementById("success-modal"));
    let successModalBody = document.getElementById("success-modal-body");

    function showSpinner() {
        document.getElementById('loadingSpinner').style.display = 'block';
    }
    
    function hideSpinner() {
        document.getElementById('loadingSpinner').style.display = 'none';
    }

    // Check if the availability data is fetched successfully
    if (availabilityData && availabilityData.status === "success" && bookedSlotsData && bookedSlotsData.status === "success" && timezoneData && timezoneData.status === "success") {
        // Display the timezone
        document.getElementById("tutor-timezone").innerText = "Tutor's Timezone: " + timezoneData.timezone;
    
        // Initialize the calendar
        let calendarEl = document.getElementById("calendar");
        let calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: "timeGridWeek",
            events: createEvents(availabilityData.availability, bookedSlotsData.bookedSlots),
            validRange: {
                start: new Date(),   // set the start date to today
            },
            eventClick: function (info) {
                let selectedDateTime = info.event.start;
                selectedDateTime.setHours(selectedDateTime.getHours() + 1);  // Add 1 hour
    
                console.log(`Booked at ${selectedDateTime.toISOString()}`);
    
                if (planDetails.classesUsed >= planDetails.numberOfClasses) {
                    // Show a modal informing the user that they need to purchase a plan
                    confirmModalBody.textContent = "You have used all your classes. Please purchase a plan to continue.";
                    confirmButton.style.display = "none";
                    confirmModal.show();
                } else {
                    confirmModalBody.textContent = "Do you want to book this slot?";
                    confirmButton.style.display = "inline-block";
                    confirmModal.show();
    
                    confirmButton.addEventListener("click", function () {
                        showSpinner();  // Display the spinner
    
                        $.ajax({
                            url: "../../controllers/ZoomController.php",
                            method: "POST",
                            data: {
                                action: "createMeeting",
                                username: getURLParameter("username"),
                                dateTime: selectedDateTime.toISOString(),
                                timezone: timezoneData.timezone,
                            },
                            dataType: "json",
                            success: function (response) {
                                if (response && response.status === "success") {
                                    let meetingURL = response.meetingURL;

                                    // Book the slot and save the Zoom link in the database
                                    $.ajax({
                                        url: "../../controllers/BookingController.php",
                                        method: "POST",
                                        data: {
                                            action: "bookSlot",
                                            username: getURLParameter("username"),
                                            tutorUsername: tutorUsername,
                                            dateTime: selectedDateTime.toISOString(),
                                            meetingURL: meetingURL,
                                            language: getURLParameter("languages"),
                                        },
                                        dataType: "json",
                                        success: function (response) {
                                            hideSpinner();
                                            if (response && response.status === "success") {
                                                // The booking was successfully saved
                                                // Show the success modal
                                                successModalBody.textContent = "The booking was successful!";
                                                successModal.show();
                                            } else {
                                                // The booking was not saved successfully
                                                console.error("Error in booking: " + response.error);
                                                alert("There was an error in booking the slot. Please try again later.");
                                            }
                                        },
                                        error: function (jqXHR, textStatus, errorThrown) {
                                            console.error("AJAX error in booking: " + textStatus + " : " + errorThrown);
                                            alert("There was an error in booking the slot. Please try again later.");
                                        },
                                    });
                                } else {
                                    // The Zoom meeting was not created successfully
                                    console.error("Error in Zoom meeting creation: " + response.error);
                                    alert("There was an error in creating the Zoom meeting. Please try again later.");
                                }
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                console.error("AJAX error in Zoom meeting creation: " + textStatus + " : " + errorThrown);
                                alert("There was an error in creating the Zoom meeting. Please try again later.");
                            },
                        });
                    });
                }
            },
        });
        calendar.render();
    }
}