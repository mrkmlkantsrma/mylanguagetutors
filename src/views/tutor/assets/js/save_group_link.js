function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            var preview = document.getElementById('cover-image-preview');
            preview.src = e.target.result;
        }

        reader.readAsDataURL(input.files[0]); // read the data as URL
    }
}

function fetchAndUpdateMeetingsTable(classId) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '../../controllers/groupClassController.php?action=get_zoom_meetings&class_id=' + classId, true);

    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            try {
                var meetings = JSON.parse(xhr.responseText);
                var tableBody = document.querySelector('.table-area .table tbody');
                tableBody.innerHTML = ''; // Clear existing rows
                meetings.forEach(function(meeting) {
                    var row = `
                        <tr>
                            <td>${meeting.title}</td>
                            <td>${meeting.scheduled_date}</td>
                            <td>${meeting.scheduled_time}</td>
                            <td>
                                <button class="copy-btn site-link small" data-link="${meeting.zoom_link}">Copy</button>
                            </td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            } catch (e) {
                alert('Error parsing JSON: ' + e.toString());
            }
        } else {
            alert('Failed to fetch meetings. Server responded with status: ' + xhr.status);
        }
    };

    xhr.send();
}

function getQueryParam(param) {
    var searchParams = new URLSearchParams(window.location.search);
    return searchParams.get(param);
}

function isValidZoomLink(zoomLink) {
    return zoomLink.includes('teams.microsoft.com');
}

function isValidDate(date) {
    return /^\d{4}-\d{2}-\d{2}$/.test(date);
}

function isValidTime(time) {
    return /^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/.test(time);
}

function saveMeetingData(classId, date, time) {
// var zoomLink = document.getElementById('zoom-link').value;
var zoomLink = '';
var baseUrl = window.location.origin;

if (!isValidDate(date)) {
    alert('Please enter a valid date.');
    return;
}

if (!isValidTime(time)) {
    alert('Please enter a valid time.');
    return;
}

jQuery.ajax({
    type: 'POST',
    url: baseUrl +'/teams.php',
    dataType: 'json', // Assuming you expect JSON response
    data: {
        date: date,
        time: time
    },
    success: function(response) {
        // Handle successful response here
        zoomLink = response.meeting_link;

        if (!isValidZoomLink(zoomLink)) {
            alert('Please try again, link not generated.');
            return;
        }

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '../../controllers/groupClassController.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.status === 'success') {
                        fetchAndUpdateMeetingsTable(classId);
                        resetZoomForm(); // Reset the form if successful.
                    } else {
                        // Handle non-success status (e.g., "error")
                        alert(`Error saving meeting: ${response.message}`);
                    }
                } catch (e) {
                    // Handle errors in parsing JSON
                    alert(`Error parsing JSON: ${e}`);
                }
            } else {
                // Handle HTTP errors
                alert(`Failed to save meeting. Server responded with status: ${xhr.status}`);
            }
        };

        xhr.send(`action=save_zoom_meeting&class_id=${encodeURIComponent(classId)}&zoom_link=${encodeURIComponent(zoomLink)}&scheduled_date=${encodeURIComponent(date)}&scheduled_time=${encodeURIComponent(time)}`);

    },
    error: function(xhr, status, error) {
        // Handle errors here
        console.error(error);

        if (!isValidZoomLink(zoomLink)) {
            alert('Please try again, link not generated.');
            return;
        }
    }
});

}

function resetZoomForm() {
var zoomContainer = document.getElementById('zoom-container');
var buttonHTML = '<button id="generate-zoom-meeting" class="btn btn-primary">Generate Meeting Link</button>';
zoomContainer.innerHTML = buttonHTML;

// Re-attach event listener to the new button
document.getElementById('generate-zoom-meeting').addEventListener('click', function() {
var classId = getQueryParam('classId');
if (classId) {
    replaceButtonWithForm(classId);
} else {
    alert('Class ID is missing in the URL.');
}
});
}

function replaceButtonWithForm(classId) {
    var zoomContainer = document.getElementById('zoom-container');
    zoomContainer.innerHTML = '';

    var formHTML = `
        <input type="date" id="zoom-date" class="form-control mb-2" />
        <input type="time" id="zoom-time" class="form-control mb-2" />
        <button id="save-zoom-meeting" class="btn btn-success">Generate & Save</button>
    `;
    zoomContainer.innerHTML = formHTML;

    document.getElementById('save-zoom-meeting').addEventListener('click', function() {
        var date = document.getElementById('zoom-date').value;
        var time = document.getElementById('zoom-time').value;
        saveMeetingData(classId, date, time);
    });
}

document.getElementById('generate-zoom-meeting').addEventListener('click', function() {
    var classId = getQueryParam('classId');
    if (classId) {
        replaceButtonWithForm(classId);
    } else {
        alert('Class ID is missing in the URL.');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    var classId = getQueryParam('classId');
    if (classId) {
        fetchAndUpdateMeetingsTable(classId);
    }
});

// Event listener for dynamic content
document.addEventListener('click', function(event) {
    if (event.target.matches('.copy-btn')) {
        var copyText = event.target.getAttribute('data-link');
        var textarea = document.createElement('textarea');
        textarea.value = copyText;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        alert('Link copied to clipboard!');
    }
});

function fetchAndUpdateStudentsTable(classId) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '../../controllers/groupClassController.php?action=get_enrolled_students&class_id=' + classId, true);

    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            try {
                var students = JSON.parse(xhr.responseText);
                if(students.status === 'success') {
                    var tableBody = document.getElementById('students-table-body');
                    tableBody.innerHTML = ''; // Clear existing rows
                    students.data.forEach(function(student) {
                        var row = `
                            <tr>
                                <td>${student.full_name}</td>
                                <td>${student.username}</td>
                                <td>${student.email}</td>
                                <td>${student.country}</td>
                            </tr>
                        `;
                        tableBody.innerHTML += row;
                    });
                } else {
                    alert('Error fetching students: ' + students.message);
                }
            } catch (e) {
                alert('Error parsing JSON: ' + e.toString());
            }
        } else {
            alert('Failed to fetch students. Server responded with status: ' + xhr.status);
        }
    };

    xhr.onerror = function() {
        alert('Request failed. Please check your connection.');
    };

    xhr.send();
}

document.getElementById('add-new-student').addEventListener('click', function() {
    var studentSearch = document.getElementById('studentSearch');
    var studentList = document.getElementById('studentList');
    
    // Toggle visibility
    studentSearch.style.display = studentSearch.style.display === 'none' ? 'block' : 'none';
    studentList.style.display = studentList.style.display === 'none' ? 'block' : 'none';

    // Fetch students when showing the list
    if (studentList.style.display === 'block') {
        fetchStudents();
    }
});


document.addEventListener('DOMContentLoaded', function() {
    fetchStudents();

    document.getElementById('studentSearch').addEventListener('keyup', function(event) {
        var searchValue = event.target.value.toLowerCase();
        var rows = document.getElementById('student-list-table-body').getElementsByTagName('tr');

        Array.from(rows).forEach(function(row) {
            var username = row.cells[0].textContent.toLowerCase();
            var email = row.cells[1].textContent.toLowerCase();
            if (username.includes(searchValue) || email.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    document.getElementById('add-new-student').addEventListener('click', function() {
        var studentListSection = document.getElementById('studentListSection');
        studentListSection.style.display = studentListSection.style.display === 'none' ? 'block' : 'none';
        if (studentListSection.style.display === 'block') {
            fetchStudents();
        }
    });
});

function fetchStudents() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '../../controllers/groupClassController.php?action=get_all_students', true);

    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            try {
                if (xhr.responseText) {
                    var students = JSON.parse(xhr.responseText);
                    var tableBody = document.getElementById('student-list-table-body');
                    tableBody.innerHTML = '';

                    students.forEach(function(student, index) {
                        var row = `
                            <tr>
                                <td>${student.username}</td>
                                <td>${student.email}</td>
                                <td>
                                    <button class="select-student-btn site-link small" data-student-id="${student.student_id}">Select</button>
                                </td>
                            </tr>
                        `;
                        tableBody.innerHTML += row;
                    });

                    var buttons = document.querySelectorAll('.select-student-btn');
                    buttons.forEach(function(button, index) {
                        button.addEventListener('click', function() {
                            var studentId = this.getAttribute('data-student-id');
                            enrollStudent(studentId);
                        });
                    });
                } else {
                    console.error('Empty response received');
                }
            } catch (e) {
                console.error('Error parsing JSON: ' + e.toString());
            }
        } else {
            alert('Failed to fetch students. Server responded with status: ' + xhr.status);
        }
    };

    xhr.send();
}

function enrollStudent(studentId) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../../controllers/groupClassController.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    var classId = new URLSearchParams(window.location.search).get('classId');
    var postData = 'action=enroll_student_manual' +
        '&student_id=' + encodeURIComponent(studentId) +
        '&class_id=' + encodeURIComponent(classId) +
        '&date_enrolled=' + encodeURIComponent(new Date().toISOString().split('T')[0]) +
        '&amount_paid=0&status=Paid';

    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            try {
                if (xhr.responseText) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.status === 'success') {
                        // Reload the page instead of showing an alert
                        window.location.reload();
                    } else {
                        console.error('Failed to enroll student: ' + response.message);
                    }
                } else {
                    console.error('Empty response received');
                }
            } catch (e) {
                console.error('Error processing request: ' + e.toString());
            }
        } else {
            console.error('Failed to send request. Server responded with status: ' + xhr.status);
        }
    };

    xhr.send(postData);
}


document.addEventListener('DOMContentLoaded', function() {
    var classId = new URLSearchParams(window.location.search).get('classId');
    if (classId) {
        // Assume fetchAndUpdateStudentsTable & fetchAndUpdateMeetingsTable are defined
        fetchAndUpdateStudentsTable(classId);
        fetchAndUpdateMeetingsTable(classId);
    }
});
