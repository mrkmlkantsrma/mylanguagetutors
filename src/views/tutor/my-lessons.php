<?php

if(session_status() == PHP_SESSION_NONE) {

    session_start();

}



if(empty($_SESSION['username']) || $_SESSION['role'] !== 'Tutor') {

    // Store the initially requested page in the session

    $_SESSION['requested_page'] = $_SERVER['REQUEST_URI'];

    

    header('Location: ../../../login.php');

    exit();

}



require_once __DIR__ . '/../../controllers/UserController.php';

require_once __DIR__ . '/../../models/User.php';

require_once __DIR__ . '/../../controllers/LessonController.php';



$lessonController->displayTutorBookedLessons();

$lessonController->displayTutorLessonHistory();





require_once 'isVerified.php';

verifyTutorProfile();



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

    $educationExperience = $userData['education_experience'];

    $nativeLanguage = $userData['native_language'];

    $workingWith = isset($userData['working_with']) && $userData['working_with'] !== null

    ? explode(',', $userData['working_with'])

    : [];



$levelsYouTeach = isset($userData['levels_you_teach']) && $userData['levels_you_teach'] !== null

    ? explode(',', $userData['levels_you_teach'])

    : [];



    $cvTarget = $userData['official_id_filepath'];

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

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="assets/css/custom.css">

    <script type="text/javascript">

    function googleTranslateElementInit() {

      new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');

    }

    </script>

    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

    <style>

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

                                            $profilePictureUrl = '/' . $profilePicture;
                                        }

                                        echo "<img src='" . $profilePictureUrl . "' alt='Profile Picture' />";

                                    } else {

                                        echo "<img src='https://images.pexels.com/photos/415829/pexels-photo-415829.jpeg' />";

                                    }

                                ?>

                                </span>

                                <span class="btn-txt"> <?php echo $username = $userData['username']; ?></span>

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

                    <li><a class="active" href="group-classes"><i class="fa-solid fa-user-group"></i><span>Group Classes</span></a></li>

                    <li><a href="earnings-payment"><i class="fa-regular fa-credit-card"></i> <span>Earnings & payments</span></a></li>

                    <li><a href="profile"><i class="fa-solid fa-user-astronaut"></i> <span>My profile</span></a></li>

                    <li><a href="reviews"><i class="fa-solid fa-certificate"></i> <span>Reviews</span></a></li>

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



                <div class="table-area mt-0">

                    <div class="form-btn">

                        <a href="manage-availability"><button class="site-link small">Manage Availability</button></a>

                    </div>

                </div>



                <h2 class="title pt-5">Next Lesson</h2>

                <div class="row">

                    <div class="col-sm-6 col-lg-3">

                        <div class="info-box">

                            <span class="info-icon"><i class="fa-regular fa-flag"></i> </span>

                            <h3>Language</h3>

                            <h4><?php echo $languagesSpoken = $userData['languages_spoken']; ?></h4>

                        </div>

                    </div>

                    <div class="col-sm-6 col-lg-3">

                        <div class="info-box">

                            <span class="info-icon"><i class="fa-solid fa-graduation-cap"></i> </span>

                            <h3>Student</h3>

                            <?php if (isset($studentName)): ?>

                            <h4><?php echo $studentName; ?></h4>

                            <?php endif; ?>

                        </div>

                    </div>

                    <div class="col-sm-6 col-lg-3">

                        <div class="info-box">

                            <span class="info-icon"><i class="fa-solid fa-hourglass-half"></i></span>

                            <h3>Start In</h3>

                            <h4 id="countdown">Loading...</h4> <!-- Countdown timer will be displayed here -->

                        </div>

                    </div>



                    <div class="col-sm-6 col-lg-3">

                        <div class="info-box">

                            <span class="info-icon"><i class="fa-solid fa-video"></i></span>

                            <h3>Zoom</h3>

                            <?php if (isset($zoomLink)): ?>

                            <h4><a id="zoom_link" href="<?php echo $zoomLink; ?>" target="_blank" style="pointer-events: none; color: gray;">Join Now</a></h4>

                            <?php endif; ?>

                        </div>

                    </div>





                    <h2 class="title pt-5">Upcoming Lessons</h2>

                    <div class="table-area">

                        <div class="table-responsive">

                            <table class="table theme-table">

                                <thead>

                                    <tr>

                                        <th>Student <span><img src="./assets/images/filter.png" alt=""></span></th>

                                        <th>Date & Time <span><img src="./assets/images/filter.png" alt=""></span></th>

                                        <th>Duration <span><img src="./assets/images/filter.png" alt=""></span></th>

                                        <th>Status <span><img src="./assets/images/filter.png" alt=""></span></th>

                                        <th>Action <span><img src="./assets/images/filter.png" alt=""></span></th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php if(!is_null($bookedLessons)) : ?>

                                    <?php foreach($bookedLessons as $booking): ?>

                                    <tr>

                                        <td><?= htmlspecialchars($booking['username']) ?></td>

                                        <td><?= htmlspecialchars($booking['class_date_time']) ?></td>

                                        <td><?= htmlspecialchars($booking['duration']) ?> min</td>

                                        <td>

                                            <form action="../../controllers/LessonController.php" method="POST">

                                                <input type="hidden" name="action" value="submit_for_review">

                                                <input type="hidden" name="lesson_id" value="<?= $booking['id'] ?>">

                                                <select class="inp" name="status" onchange="this.form.submit()">

                                                    <option value="Booked" <?= $booking['status'] == 'Booked' ? 'selected' : '' ?>>Booked</option>

                                                    <?php

                                                    $oneHourPassed = strtotime($booking['class_date_time']) + 3600 <= time();

                                                    if ($oneHourPassed) : ?>

                                                    <option value="SUBMITTED" <?= $booking['status'] == 'SUBMITTED' ? 'selected' : '' ?>>Submit</option>

                                                    <?php endif; ?>

                                                </select>

                                            </form>

                                        </td>

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

                                    <?php endforeach; ?>

                                    <?php else: ?>

                                    <tr>

                                        <td colspan="5">No booked lessons found.</td>

                                    </tr>

                                    <?php endif; ?>

                                </tbody>





                                <tfoot>

                                    <tr>

                                        <td colspan="6">

                                            <span>Showing <?= $GLOBALS['offset'] + 1 ?>-<?= min($GLOBALS['offset'] + $GLOBALS['lessonsPerPage'], $GLOBALS['totalLessons']) ?> of Total <?= $GLOBALS['totalLessons'] ?></span>

                                            <span class="table-nav">

                                                <a href="?page=<?= max(1, $GLOBALS['currentPage'] - 1) ?>"><i class="fa-solid fa-arrow-left"></i></a>

                                                <span><?= $GLOBALS['currentPage'] ?></span>

                                                <a href="?page=<?= min($GLOBALS['totalPages'], $GLOBALS['currentPage'] + 1) ?>"><i class="fa-solid fa-arrow-right"></i></a>

                                            </span>



                                        </td>

                                    </tr>

                                </tfoot>

                            </table>

                        </div>



                    </div>



                    <!-- Request Extension modal -->



                    <div class="modal fade" id="extensionModal" tabindex="-1" aria-labelledby="extensionModalLabel" aria-hidden="true">

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

                                            <input type="datetime-local" class="form-control" id="new_date_time_request" name="new_date_time_request" required min="<?php echo date('Y-m-d\TH:i', strtotime("+1 minutes")); ?>">

                                        </div>

                                        <div class="mb-3">

                                            <label for="new_date_time_request_reason" class="form-label">Reason for Request:</label>

                                            <textarea class="form-control" id="new_date_time_request_reason" name="new_date_time_request_reason" rows="3" required></textarea>

                                        </div>

                                        <input type="hidden" id="hiddenBookingId" name="booking_id" value="">

                                        <input type="hidden" name="action" value="handleRequestExtension">



                                        <button type="submit" class="btn btn-primary">Submit Request</button>

                                    </form>

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

                                        <th>Student <span><img src="./assets/images/filter.png" alt=""></span></th>

                                        <th>Date&Time <span><img src="./assets/images/filter.png" alt=""></span></th>

                                        <th>Rating <span><img src="./assets/images/filter.png" alt=""></span></th>

                                        <th class="text-end">Status <span><img src="./assets/images/filter.png" alt=""></span></th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php foreach ($GLOBALS['lessonHistory'] as $lesson): ?>

                                    <tr>

                                        <td><?php echo $lesson['username']; ?></td>

                                        <td><?php echo $lesson['class_date_time']; ?></td>

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

                                        <td class="text-end"><?php echo $lesson['status']; ?></td>

                                    </tr>

                                    <?php endforeach; ?>

                                </tbody>

                                <tfoot>

                                    <tr>

                                        <td colspan="6">

                                            <span>Showing <?php echo $offset + 1; ?>-<?php echo min(($offset + $lessonsPerPage), $GLOBALS['totalHistoryLessons']); ?> of Total <?php echo $GLOBALS['totalHistoryLessons']; ?></span>

                                            <span class="table-nav">

                                                <a href="?history_page=<?php echo max(1, $GLOBALS['currentHistoryPage'] - 1); ?>"><i class="fa-solid fa-arrow-left"></i></a>

                                                <span><?php echo $GLOBALS['currentHistoryPage']; ?></span>

                                                <a href="?history_page=<?php echo min($GLOBALS['totalHistoryPages'], $GLOBALS['currentHistoryPage'] + 1); ?>"><i class="fa-solid fa-arrow-right"></i></a>

                                            </span>

                                        </td>

                                    </tr>

                                </tfoot>

                            </table>

                        </div>

                    </div>



                    <!-- <div class="table-area">

                    <div class="form-btn">

                        <button class="site-link small grey">Cancel</button>

                        <button class="site-link small">Confirm</button>

                    </div>

                </div> -->



                </div>

            </div>



        </div>

        <script>

    // Get the date we're counting down to

    var countDownDateStr = "<?php echo $nextLessonDateTime; ?>";

    

    // Check if the date string is valid

    if (!countDownDateStr || isNaN(new Date(countDownDateStr).getTime())) {

        document.getElementById("countdown").innerHTML = "Not Available";

    } else {

        var countDownDate = new Date(countDownDateStr).getTime();



        // Get the zoom link

        var zoomLink = document.getElementById("zoom_link");



        // Update the count down every 1 second

        var countdown = setInterval(function() {

            var now = new Date().getTime();

            var distance = countDownDate - now;



            // Time calculations for days, hours, minutes, and seconds

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

        document.addEventListener('DOMContentLoaded', function () {

    // Listen for all clicks on the document

    document.addEventListener('click', function (event) {



        // Only do something if clicked on an element with 'data-bs-toggle' attribute

        if (event.target.getAttribute('data-bs-toggle') !== 'modal') return;



        // Get the booking ID from the clicked element's 'data-booking-id' attribute

        var bookingId = event.target.getAttribute('data-booking-id');



        // Set the hidden input's value to the booking ID

        document.getElementById('hiddenBookingId').value = bookingId;



    }, false);

});



       </script>





        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

        <script src="./assets/js/custom.js"></script>

        

</body>



</html>