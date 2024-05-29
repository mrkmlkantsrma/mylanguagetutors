<?php
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(empty($_SESSION['username']) || $_SESSION['role'] !== 'Student') {
    // Store the initially requested page in the session
    $_SESSION['requested_page'] = $_SERVER['REQUEST_URI'];
    
    header('Location: ../../../login.php');
    exit();
}

if (isset($_GET['booking_id'])) {
    $_SESSION['current_booking_id'] = $_GET['booking_id'];
}

$currentBookingId = isset($_SESSION['current_booking_id']) ? $_SESSION['current_booking_id'] : null;


require_once __DIR__ . '/../../controllers/UserController.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../controllers/LessonController.php';

$lessonModel = new Lesson();  // Assuming you have a Lesson model class
$lessonController = new LessonController($lessonModel);

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <style>
        /* Chat Container */
        .chat-container {
            display: flex;
            flex-direction: column;
            width: 100vw;  /* full screen width */
            height: 100vh;  /* full screen height */
            overflow: auto;  /* enable scroll if content exceeds screen height */
            padding: 20px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            background-color: #f8f9fa;
        }
        
        /* Messages */
        .message {
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .m-action{
            padding: 20px;
            border-radius: 0;
            margin-bottom: 20px;
            background-color: #84d19f;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .user-info {
            font-weight: 500;
            margin-bottom: 12px;
            color: #333;
        }
        
        .message p {
            margin-bottom: 12px;  
            color: #555;  
        }
        
        /* Buttons */
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.1s ease, box-shadow 0.1s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
        }
        
        .btn-approve {
            background-color: #28a745;
            color: #ffffff;
            margin-right: 15px;
        }
        
        .btn-decline {
            background-color: #dc3545;
            color: #ffffff;
        }
        
        /* Actions */
        .actions {
            display: flex;
            justify-content: flex-start;
            gap: 10px;
        }

        .disabled-btn{
            background-color: #ced3d7;
            border-style: none;
        }
    </style>

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
                                        $profilePicture = str_replace('../../', '', $profilePicture);
                                        $profilePictureUrl = '/my-language-tutor/' . $profilePicture;
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
                <div class="chat-card">
                    <?php 
                        $isPending = false;

                        if (isset($GLOBALS['requestedChanges']) && is_array($GLOBALS['requestedChanges'])) {
                            $lastChange = end($GLOBALS['requestedChanges']);  // Get the last item in the array
                            if ($lastChange !== false && $lastChange['status'] === 'pending') {
                                $isPending = true;
                            }
                        }                
                        
                    ?>

                    <div class="message m-action">
                        <div class="user-info">
                            <p><em>Request Extension or Cancellation</em></p>
                        </div>
                        <div class="actions">
                            <button type="button" 
                                    class="site-link small <?php echo $isPending ? 'disabled-btn' : ''; ?>" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#extensionModal" 
                                    data-booking-id="<?php echo $currentBookingId; ?>" 
                                    <?php echo $isPending ? 'disabled' : ''; ?>>
                                Request extension
                            </button>                            
                            <form action="" method="POST">
                                <input type="hidden" name="action" value="cancel_class">
                                <input type="hidden" name="lesson_id" value="<?php echo $currentBookingId; ?>">
                                <button type="submit" class="btn btn-decline">Cancel Class</button>
                            </form>

                        </div>
                    </div>
                </div>
                <div class="">
                    <?php if (isset($bookingInfo) && is_array($bookingInfo)): ?>
                        <div class="chat-card">
                            <div class="message">
                                <div class="user-info">
                                    <strong><?php echo htmlspecialchars($bookingInfo['username']); ?></strong> | <?php echo date("M d, Y, g:i a", strtotime($bookingInfo['date_booked'])); ?>
                                </div>
                                <p><em>Booked the lesson</em></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php 
                    $loggedInUser = $_SESSION['username'];  // Assuming you're storing logged-in user's username in the session
                    $lastIndex = count($GLOBALS['requestedChanges']) - 1;
                ?>

                <?php if (isset($GLOBALS['requestedChanges']) && is_array($GLOBALS['requestedChanges'])): ?>
                    <?php foreach($GLOBALS['requestedChanges'] as $index => $change): ?>
                        <div class="chat-card">
                            <div class="message">
                                <div class="user-info">
                                    <strong><?php echo htmlspecialchars($change['requested_by']); ?></strong> | <?php echo date("M d, Y, g:i a", strtotime($change['new_date_time_request_date'])); ?>
                                </div>
                                <p><em><?php echo ucwords(htmlspecialchars($change['status'] === 'pending' ? 'requested extension' : ($change['status'] . ' the request'))); ?></em></p>
                                
                                <?php if($change['status'] === 'pending'): ?>
                                    <p>Current date and time: <?php echo htmlspecialchars($change['new_date_time_request_date']); ?></p>
                                    <p>Requested new date: <?php echo htmlspecialchars($change['new_date_time_request']); ?></p>
                                    <p>Reason: <?php echo htmlspecialchars($change['new_date_time_request_reason']); ?></p>
                                    
                                    <?php if($index === $lastIndex && $loggedInUser !== $change['requested_by']): ?>
                                        <div class="actions">
                                            <form action="" method="POST">
                                                <input type="hidden" name="action" value="approve_request">
                                                <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($change['booking_id']); ?>">
                                                <input type="hidden" name="new_date_time_request" value="<?php echo htmlspecialchars($change['new_date_time_request']); ?>">
                                                <button type="submit" class="btn btn-approve">Approve</button>
                                            </form>
                                            
                                            <form action="" method="POST">
                                                <input type="hidden" name="action" value="decline_request">
                                                <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($change['booking_id']); ?>">
                                                <button type="submit" class="btn btn-decline">Decline request</button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No booking changes found.</p>
                <?php endif; ?>


                </div>

            </div>
            
        </div>
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
                            <!-- Updated this line to add booking_id directly from PHP -->
                            <input type="hidden" id="hiddenBookingId" name="booking_id" value="<?php echo $currentBookingId; ?>">
                            <input type="hidden" name="action" value="handleRequestExtension">

                            <button type="submit" class="site-link small">Submit Request</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
            // Set the date we're counting down to
            var countDownDate = new Date("<?php echo $nextLessonDateTime; ?>").getTime();

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


        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="./assets/js/custom.js"></script>
        
</body>

</html>