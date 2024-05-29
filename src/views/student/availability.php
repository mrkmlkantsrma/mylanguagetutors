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

require_once __DIR__ . '/../../controllers/UserController.php';
require_once __DIR__ . '/../../models/User.php';

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

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, shrink-to-fit=no" />
        <link rel="shortcut icon" href="assets/images/favicon.png" />
        <title>MyLanguageTutor : Find a tutor</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
        <link rel="stylesheet" href="assets/css/custom.css" />
        <link rel="stylesheet" href="public_assets/css/custom.css" />
        <script type="text/javascript">
    function googleTranslateElementInit() {
      new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
    }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

        <style>
            .time.disabled {
                background-color: #ccc; /* grey color for non-available slots */
                cursor: not-allowed; /* show a 'not-allowed' cursor when hovering over non-available slots */
            }

            .time.available {
                background-color: #4caf50; /* green color for available slots */
                cursor: pointer; /* show a 'pointer' cursor when hovering over available slots */
            }

            /* This will add the pointer cursor to the available slots */
            .available-slot {
                cursor: pointer !important;
            }

            /* This will add the not-allowed cursor to the unavailable slots */
            .fc-event {
                cursor: not-allowed !important;
            }

            /* This will override the not-allowed cursor for the available slots */
            .fc-event.available-slot {
                cursor: pointer !important;
            }

            .unavailable-slot {
                cursor: not-allowed !important;
            }
            
        </style>
    </head>
    <body>
        <div class="site-wrapper">
            <div class="site-header">
                <div class="logo"><img src="./assets/images/logo.png" alt="" /></div>
                <div class="site-header-right">
                    <div class="site-title">
                        <span class="collapse-nav"><img src="./assets/images/collapse.png" alt="" /></span>
                        <h1>Find a tutor</h1>
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
                        <li>
                            <a href="my-lessons"><i class="fa-regular fa-newspaper"></i> <span>My lessons</span></a>
                        </li>
                        <li>
                            <a class="active" href="find-a-tutor"><i class="fa-solid fa-user-tie"></i> <span>Find a tutor</span></a>
                        </li>
                        <li><a href="group-classes"><i class="fa-solid fa-user-group"></i><span>Group Classes</span></a></li>
                        <li>
                            <a href="plans-payment"><i class="fa-regular fa-credit-card"></i> <span>Plans & Payment</span></a>
                        </li>
                        <li>
                            <a href="profile"><i class="fa-solid fa-user-astronaut"></i> <span>My profile</span></a>
                        </li>
                        <li>
                            <a href="help-support"><i class="fa-solid fa-circle-info"></i> <span>Help & support</span></a>
                        </li>
                        <li>
                            <form action="../../controllers/LoginController.php" method="post" style="display: inline;">
                                <input type="hidden" name="logout" value="1" />
                                <a href="#" onclick="this.closest('form').submit(); return false;">
                                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                                    <span>Logout</span>
                                </a>
                            </form>
                        </li>
                    </ul>
                </div>

                <div class="main-container">
                    <div class="page-title-mob">
                        <h1>Find a tutor</h1>
                    </div>

                    <div class="table-area extend">
                        <div class="course-details-left">
                            <div class="box">
                                <div>
                                    <h3 class="title">Tutor's Availability</h3>
                                    <p>Click on an available slot to book a session with the tutor.</p>
                                    <p id="tutor-timezone"></p>
                                </div>
                                <div class="table-responsive">
                                    <div id="calendar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirm Booking Modal -->
        <div class="modal fade" id="confirm-modal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmModalLabel">Confirm Booking</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="confirm-modal-body"></div>
                    <div class="spinner-border text-primary" role="status" id="loadingSpinner" style="display:none;">
                    <span class="sr-only">Loading...</span>
                </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="confirm-button" class="btn btn-primary">Confirm Booking</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div class="modal fade" id="success-modal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="successModalLabel">Success</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="success-modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>
        <script src="./public_assets/js/custom.js"></script>
        <script src="./assets/js/datepicker.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                // Close button from the modal header
                let closeButtonHeader = document.querySelector("#success-modal .modal-header .btn-close");
                // Close button from the modal footer
                let closeButtonFooter = document.querySelector("#success-modal .modal-footer .btn");

                closeButtonHeader.addEventListener("click", function () {
                    location.reload();
                });

                closeButtonFooter.addEventListener("click", function () {
                    location.reload();
                });
            });
        </script>
        <script>
            $("#sandbox-container div").datepicker({
                todayHighlight: true,
            });
        </script>
    </body>
</html>
