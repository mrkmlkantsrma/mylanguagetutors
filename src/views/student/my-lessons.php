<?php


if(session_status() == PHP_SESSION_NONE) {
    session_start();
}


// if(empty($_SESSION['username']) || $_SESSION['role'] !== 'Student') {
//     // Store the initially requested page in the session
//     $_SESSION['requested_page'] = $_SERVER['REQUEST_URI'];
    
//     header('Location: ../../../login.php');
//     exit();
// }


require_once __DIR__ . '/../../controllers/UserController.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../controllers/LessonController.php';

$lessonController->displayStudentBookings();
$lessonController->displayStudentLessonHistory();

// get all tutors data
$language = $_GET['language'] ?? null;
$tutors = $user->getTutorsData($language);

// Userdata
$username = $_SESSION['username'];
$userData = $user->getUserData($username);
$_SESSION['user_data'] = $userData;

// check if user data is set in session
if (isset($_SESSION['user_data'])) {
    $userData = $_SESSION['user_data'];
    $username = $userData['username'];
    $firstName = $userData['first_name'];
    $lastName = $userData['last_name'];
    $email = $userData['email'];
    $mobileNo = $userData['mobile_no'];
    $country = $userData['country'];
    $languagesSpoken = $userData['languages_spoken'];
    $languageAndEducationLevel = $userData['language_and_education_level'];
    $profilePicture = $userData['profile_photo_filepath'];
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, shrink-to-fit=no">
    <link rel="shortcut icon" href="assets/images/favicon.png">
    <title>MyLanguageTutor : My Lessons</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <script type="text/javascript">
    function googleTranslateElementInit() {
      new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
    }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <style>
        .disabled {
            pointer-events: none;
            color: #ccc;
            opacity: 0.5 !important; /* This fades the button */
        }
        
        .rating {
            float:left;
            direction: rtl;  /* Right to Left */
        }
        
        .rating > input {
            display: none;
        }
        
        .rating > label:before {
            margin: 5px;
            font-size: 1.25em;
            font-family: FontAwesome;
            display: inline-block;
            content: "\f005";
        }
        
        .rating > label {
            color: #ddd;
        }
        
        .rating > input:checked ~ label,
        .rating:not(:checked) > label:hover,
        .rating:not(:checked) > label:hover ~ label {
            color: #FFD700;
        }
        
        .fa-star{
            color: #ffbc03;
        }
        
    </style>
</head>

<body>

    <div class="site-wrapper">

        <div class="site-header">
            <div class="logo"><img src="./assets/images/logo.png" alt=""></div>
            <div class="site-header-right">
                <div class="site-title">
                    <span class="collapse-nav"><img src="./assets/images/collapse.png" alt=""></span>
                    <h1>My Lessons</h1>
                </div>
                <div class="login-head-right">
                    
                    <div class="profile-dropdown">
                        <div class="dropdown">
                            <div class="dropdown-toggle" data-bs-toggle="dropdown">
                                <span class="profile-dropdown-img">
                                    <?php
                                    if(isset($profilePicture) && !empty($profilePicture)) {
                                        $substring_to_check = "https://lh3.googleusercontent.com";

                                        if (substr($profilePicture, 0, strlen($substring_to_check)) === $substring_to_check) {
                                            $profilePictureUrl = $profilePicture;
                                        } else {
                                            $profilePicture = str_replace('../../', '', $profilePicture);
                                            $profilePictureUrl = '/my-language-tutor/' . $profilePicture;
                                        }
                                        echo "<img src='" . $profilePictureUrl . "' alt='Profile Picture' />";
                                    } else {
                                        echo "<img src='https://images.pexels.com/photos/415829/pexels-photo-415829.jpeg' />";
                                    }
                                    ?>
                                </span>
                                <span class="btn-txt"><?php echo $username = $userData['username']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-wrap">
            <div class="side-nav">
                <ul>
                    <li><a class="active" href="my-lessons"><i class="fa-regular fa-newspaper"></i> <span>My lessons</span></a></li>
                    <li><a href="find-a-tutor"><i class="fa-solid fa-user-tie"></i> <span>Find a tutor</span></a></li>
                    <li><a href="group-classes"><i class="fa-solid fa-user-group"></i><span>Group Classes</span></a></li>
                    <li><a href="plans-payment"><i class="fa-regular fa-credit-card"></i> <span>Plans & Payment</span></a></li>
                    <li><a href="profile"><i class="fa-solid fa-user-astronaut"></i> <span>My profile</span></a></li>
                    <li><a href="help-support"><i class="fa-solid fa-circle-info"></i> <span>Help & support</span></a></li>
                    <li>
                        <form action="../../controllers/LoginController.php" method="post" style="display: inline;">
                            <input type="hidden" name="logout" value="1">
                            <a href="#" onclick="this.closest('form').submit(); return false;">
                                <i class="fa-solid fa-arrow-right-to-bracket"></i>
                                <span>Logout</span>
                            </a>
                        </form>
                    </li>
                </ul>
            </div>
            <div class="main-container">

            <?php
                if (isset($_SESSION['message'])) {
                    echo '<div class="alert alert-success">' . $_SESSION['message'] . '</div>';
                    unset($_SESSION['message']); // remove the message from the session after displaying it
                }

                if (isset($_SESSION['error_message'])) {
                    echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
                    unset($_SESSION['error_message']); // remove the error message from the session after displaying it
                }
                ?>


                <div class="page-title-mob">
                    <h1>My Lessons</h1>
                </div>

                <h2 class="title">Next Lesson</h2>
                <div class="row">
                    <div class="col-sm-6 col-lg-3">
                        <div class="info-box">
                            <span class="info-icon"><i class="fa-solid fa-flag"></i></span>
                            <h3>Language</h3>
                            <h4><?php echo isset($GLOBALS['bookings'][0]['language']) ? $GLOBALS['bookings'][0]['language'] : 'N/A'; ?></h4>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="info-box">
                            <span class="info-icon"><i class="fa-solid fa-user-tie"></i></span>
                            <h3>Tutor</h3>
                            <h4><?php echo isset($GLOBALS['bookings'][0]['tutor_username']) ? $GLOBALS['bookings'][0]['tutor_username'] : 'N/A'; ?></h4>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="info-box">
                            <span class="info-icon"><i class="fa-solid fa-hourglass-half"></i></span>
                            <h3>Start In</h3>
                            <h4 id="countdown">Loading...</h4>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="info-box">
                            <span class="info-icon"><i class="fa-solid fa-video"></i></span>
                            <h3>Zoom</h3>
                            <?php if (isset($GLOBALS['bookings'][0]['zoom_link'])): ?>
                            <h4><a id="zoom_link" href="<?php echo $GLOBALS['bookings'][0]['zoom_link']; ?>" target="_blank" style="pointer-events: none; color: gray;">Join Now</a></h4>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <h2 class="title pt-5">Upcoming Lessons</h2>
                <div id="message"></div>
                <div class="table-area">
                    <div class="table-responsive">
                        <table class="table theme-table">
                            <thead>
                                <tr>
                                    <th>Language <span><img src="./assets/images/filter.png" alt=""></span></th>
                                    <th>Tutor <span><img src="./assets/images/filter.png" alt=""></span></th>
                                    <th>Date & Time <span><img src="./assets/images/filter.png" alt=""></span></th>
                                    <th>Duration <span><img src="./assets/images/filter.png" alt=""></span></th>
                                    <th>Action <span><img src="./assets/images/filter.png" alt=""></span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($GLOBALS['bookings'] as $booking) { ?>
                                <tr>
                                    <td><?php echo $booking['language']; ?></td>
                                    <td><?php echo $booking['tutor_username']; ?></td>
                                    <td><?php echo $booking['class_date_time']; ?></td>
                                    <td><?php echo $booking['duration']; ?> min</td>
                                    <td>
                                        <!-- Displaying the appropriate button or label -->
                                        <?php 
                                                if ($booking['latest_request_status'] === 'pending') {
                                                    if ($booking['requested_by'] !== $_SESSION['username']) {
                                                        echo '<a href="lesson-activities.php?booking_id='.$booking['id'].'">Review request</a>';
                                                    } else {
                                                        echo '<p>Pending Resolution</p>';
                                                        echo '<a href="lesson-activities.php?booking_id='.$booking['id'].'">Activities</a>';
                                                    }
                                                } else {
                                                    echo '<button type="button" class="site-link small" data-bs-toggle="modal" data-bs-target="#extensionModal" data-booking-id="'.$booking['id'].'">Request extension</button>';
                                                    echo '<br>';
                                                    echo '<a href="lesson-activities.php?booking_id='.$booking['id'].'">Activities</a>';
                                                }
                                            ?>
                                    </td>

                                </tr>
                                <?php } ?>
                            </tbody>

                            <tfoot>
                                <tr>
                                    <td colspan="6">
                                        <span>Showing <?php echo ($GLOBALS['currentPage'] - 1) * $lessonsPerPage + 1; ?>-<?php echo min($GLOBALS['currentPage'] * $lessonsPerPage, $GLOBALS['totalBookings']); ?> of Total <?php echo $GLOBALS['totalBookings']; ?></span>
                                        <span class="table-nav">
                                            <a href="<?php echo "?page=".max(1, $GLOBALS['currentPage'] - 1); ?>"><i class="fa-solid fa-arrow-left"></i></a>
                                            <span><?php echo $GLOBALS['currentPage']; ?></span>
                                            <a href="<?php echo "?page=".min($GLOBALS['currentPage'] + 1, $GLOBALS['totalPages']); ?>"><i class="fa-solid fa-arrow-right"></i></a>
                                        </span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>

                <!-- Request Extension modal -->

                <div class="modal fade" id="extensionModal" tabindex="-1" aria-labelledby="extensionModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="extensionModalLabel">Request Extension</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="../../controllers/LessonController.php" method="post">
                                    <div class="mb-3">
                                        <label for="new_date_time_request" class="form-label">New Date and Time:</label>
                                        <input type="datetime-local" 
                                        class="form-control" 
                                        id="new_date_time_request" 
                                        name="new_date_time_request" 
                                        required 
                                        onfocus="clearDefaultDateTime(this);" 
                                        onblur="setDefaultDateTime(this);" 
                                        value="<?php echo date('Y-m-d\TH:i', strtotime('+1 minutes')); ?>" 
                                        min="<?php echo date('Y-m-d\TH:i'); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_date_time_request_reason" class="form-label">Reason for Request:</label>
                                        <textarea class="form-control" id="new_date_time_request_reason" name="new_date_time_request_reason" rows="3" required></textarea>
                                    </div>
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                    <input type="hidden" name="action" value="handleRequestExtension">

                                    <button type="submit" class="site-link small">Submit Request</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


                <h2 class="title pt-5">Lesson History</h2>
                <div class="table-area">
                    <div class="table-responsive">
                        <table class="table theme-table">
                            <thead>
                                <tr>
                                    <th>Language <span><img src="./assets/images/filter.png" alt=""></span></th>
                                    <th>Tutor <span><img src="./assets/images/filter.png" alt=""></span></th>
                                    <th>Status <span><img src="./assets/images/filter.png" alt=""></span></th>
                                    <th>Action <span><img src="./assets/images/filter.png" alt=""></span></th>
                                    <th>Rating<span><img src="./assets/images/filter.png" alt=""></span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($lessonHistory as $lesson): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($lesson["language"]); ?></td>
                                    <td><?php echo htmlspecialchars($lesson["tutor_username"]); ?></td>
                                    <td><span class="table-stat"><?php echo htmlspecialchars($lesson["status"]); ?></span></td>
                                    <td>
                                        <?php if($lesson['status'] === 'SUBMITTED'): ?>
                                        <!-- The "Approve" button -->
                                        <a class="site-link small" href="#" data-bs-toggle="modal" data-bs-target="#confirmApprovalModal<?php echo $lesson['id']; ?>">Approve</a>

                                        <!-- The "Approve" modal -->
                                        <div class="modal fade" id="confirmApprovalModal<?php echo $lesson['id']; ?>" tabindex="-1" aria-labelledby="confirmApprovalModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="confirmApprovalModalLabel">Confirm Approval</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Do you really want to approve the lesson?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <a href="../../controllers/LessonController.php?action=approve&lesson_id=<?php echo htmlspecialchars($lesson["id"]); ?>&tutor_username=<?php echo urlencode($lesson["tutor_username"]); ?>" class="btn btn-primary">Approve</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <?php elseif($lesson['status'] === 'COMPLETED'): ?>
                                        <!-- The "Review" button -->
                                        <a class="site-link small grey <?php echo $lesson['star_rating'] !== null ? 'disabled' : ''; ?>" href="#" data-bs-toggle="modal" data-bs-target="#reviewModal<?php echo $lesson['id']; ?>">Review Tutor</a>

                                        <!-- The "Review" modal -->
                                        <div class="modal fade" id="reviewModal<?php echo $lesson['id']; ?>" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="reviewModalLabel">Review Tutor</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="../../controllers/LessonController.php" method="post">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="lesson_id" value="<?php echo htmlspecialchars($lesson["id"]); ?>">
                                                            <input type="hidden" name="action" value="review">
                                                            <div class="form-group">
                                                                <label for="star_rating">Rating & Review</label>
                                                                <br>
                                                                <div class="rating">
                                                                    <input type="radio" id="star5<?php echo $lesson['id']; ?>" name="star_rating" value="5" /><label for="star5<?php echo $lesson['id']; ?>" title="Awesome - 5 stars"></label>
                                                                    <input type="radio" id="star4<?php echo $lesson['id']; ?>" name="star_rating" value="4" /><label for="star4<?php echo $lesson['id']; ?>" title="Good - 4 stars"></label>
                                                                    <input type="radio" id="star3<?php echo $lesson['id']; ?>" name="star_rating" value="3" /><label for="star3<?php echo $lesson['id']; ?>" title="Average - 3 stars"></label>
                                                                    <input type="radio" id="star2<?php echo $lesson['id']; ?>" name="star_rating" value="2" /><label for="star2<?php echo $lesson['id']; ?>" title="Not that bad - 2 stars"></label>
                                                                    <input type="radio" id="star1<?php echo $lesson['id']; ?>" name="star_rating" value="1" /><label for="star1<?php echo $lesson['id']; ?>" title="Very bad - 1 star"></label>
                                                                </div>

                                                            </div>



                                                            <div class="form-group">
                                                                <label for="review"></label>
                                                                <textarea name="review" id="review" class="form-control"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="site-link small">Submit Review</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                            if ($lesson["star_rating"] === null) {
                                                echo "Not Rated";
                                            } else {
                                                echo htmlspecialchars($lesson["star_rating"]); 
                                                echo " <i class='fa-solid fa-star'></i>";
                                            }
                                        ?>
                                    </td>

                                </tr>
                                <?php endforeach; ?>
                            </tbody>

                            <tfoot>
                                <tr>
                                    <td colspan="6">
                                        <span>Showing <?php echo $lessonsPerPage*($currentHistoryPage-1)+1; ?>-<?php echo min($lessonsPerPage*$currentHistoryPage, $totalHistoryLessons); ?> of Total <?php echo $totalHistoryLessons; ?></span>
                                        <span class="table-nav">
                                            <a href="studentHistory.php?history_page=<?php echo max($currentHistoryPage-1, 1); ?>"><i class="fa-solid fa-arrow-left"></i></a>
                                            <span><?php echo $currentHistoryPage; ?></span>
                                            <a href="studentHistory.php?history_page=<?php echo min($currentHistoryPage+1, $totalHistoryPages); ?>"><i class="fa-solid fa-arrow-right"></i></a>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
    // Check if there are any bookings before trying to access them
    if ( <?php echo count($GLOBALS['bookings']) > 0 ? 'true' : 'false'; ?> ) {
        // Set the date we're counting down to
        var countDownDate = new Date("<?php echo $GLOBALS['bookings'][0]['class_date_time']; ?>").getTime();

        // Get the zoom link
        var zoomLink = document.getElementById("zoom_link");

        // Update the count down every 1 second
        var countdown = setInterval(function() {
            var now = new Date().getTime();
            var distance = countDownDate - now;

            // Time calculations for days, hours, minutes and seconds
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Display the result in the element with id="countdown"
            document.getElementById("countdown").innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";

            // Enable the Zoom link when the countdown reaches 10 minutes
            if (days == 0 && hours == 0 && minutes <= 10) {
                zoomLink.style.pointerEvents = "auto";
                zoomLink.style.color = "green";
            }

            // If the count down is finished, write some text and keep Zoom link active
            if (distance < 0) {
                clearInterval(countdown);
                document.getElementById("countdown").innerHTML = "Lesson Started!";
                zoomLink.style.pointerEvents = "auto";
                zoomLink.style.color = "green";
            }
        }, 1000);
    }
</script>

    <script>
        document.querySelectorAll('a.disabled').forEach((link) => {
            link.addEventListener('click', (event) => {
                event.preventDefault();
            });
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/custom.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.rating input').on('click', function() {
                var rating = $(this).val();

                // Uncheck all input and set their color to default
                $('.rating input').each(function() {
                    $(this).prop('checked', false);
                    $(this).next('label').css('color', '#ddd');
                });

                // Check the selected input and color all the stars that represent values equal to and less than the clicked star rating
                $('.rating input').each(function() {
                    if ($(this).val() <= rating) {
                        $(this).next('label').css('color', '#FFD700');
                    }
                });

                // Check the selected star rating
                $(this).prop('checked', true);
            });
        });
    </script>

<script>
    $(document).ready(function() {
        $('#extensionModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var bookingId = button.data('booking-id'); // Extract the booking-id from the data-* attribute
            
            // Update the modal's hidden input with the bookingId value
            $(this).find('input[name="booking_id"]').val(bookingId);
        });
    });
</script>

<script>
    function clearDefaultDateTime(input) {
        const defaultValue = "<?php echo date('Y-m-d\TH:i', strtotime('+1 minutes')); ?>";
        if (input.value === defaultValue) {
            input.value = '';
        }
    }

    function setDefaultDateTime(input) {
        if (!input.value) {
            input.value = "<?php echo date('Y-m-d\TH:i', strtotime('+1 minutes')); ?>";
        }
    }
    
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const modalElement = document.getElementById('extensionModal');

    // Flag to check if close button was clicked
    let closeButtonClicked = false;

    // Listen to Bootstrap's 'hide.bs.modal' event
    $(modalElement).on('hide.bs.modal', function(e) {
        if (!closeButtonClicked) {
            e.preventDefault();  // Prevent the modal from closing
        }
    });

    const closeButton = document.querySelector('.btn-close');

    closeButton.addEventListener('click', function(event) {
        closeButtonClicked = true;  // Set flag to true when close button is clicked
        const modalInstance = bootstrap.Modal.getInstance(modalElement);
        modalInstance.hide();  // Close the modal
        closeButtonClicked = false;  // Reset the flag after closing the modal
    });
});

</script>

<script>
    // URLSearchParams object to parse query parameters
    const urlParams = new URLSearchParams(window.location.search);
    const sessionId = urlParams.get('session_id'); // Retrieve the Stripe session ID
    const status = urlParams.get('status'); // Payment status ('success' or 'cancel')
    const messageDiv = document.getElementById('message'); // The div where messages are displayed

    // Function to clear URL parameters to avoid repeating the process on page reload
    function clearUrlParams() {
        const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
        window.history.replaceState({}, document.title, cleanUrl);
    }

    // Function to handle the server response
    function handleServerResponse(responseData) {
        if (responseData.error) {
            messageDiv.innerHTML = `<div class="alert alert-danger" role="alert">${responseData.error}</div>`;
        } else {
            messageDiv.innerHTML = `<div class="alert alert-success" role="alert">${responseData.message}</div>`;
            // Additional logic upon successful enrollment
            // For example, redirecting to a confirmation page or updating the UI
        }
    }

    // Function to call the server endpoint with the session ID
    function processPaymentSuccess(sessionId) {
        fetch('groupClassPaymentSuccess.php?sessionId=' + sessionId)
            .then(response => response.json()) // Parse the JSON response
            .then(data => {
                handleServerResponse(data); // Handle the response from the server
                clearUrlParams(); // Clear the URL parameters
            })
            .catch(error => {
                console.error('Error:', error);
                messageDiv.innerHTML = `<div class="alert alert-danger" role="alert">An error occurred while processing your request.</div>`;
            });
    }

    // Check the payment status and act accordingly
    if (status === 'success' && sessionId) {
        // Call the function to process the payment success
        processPaymentSuccess(sessionId);
    } else if (status === 'cancel') {
        messageDiv.innerHTML = `<div class="alert alert-warning" role="alert">Payment Cancelled. Your payment process was cancelled.</div>`;
        clearUrlParams();
    }
</script>


</body>

</html>