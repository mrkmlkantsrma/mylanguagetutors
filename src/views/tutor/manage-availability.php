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
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, shrink-to-fit=no" />
        <link rel="shortcut icon" href="assets/images/favicon.png" />
        <title>MyLanguageTutor : Availability</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" />
        <link rel="stylesheet" href="assets/css/custom.css" />
        <script type="text/javascript">
    function googleTranslateElementInit() {
      new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
    }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
        <style>
            .time.selected {
                background-color: #00a2ff; /* Change this to whatever color you want */
                color: white; /* Change this to whatever color you want */
            }

            .toast {
                z-index: 9999;
                background: #171c34;
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
                        <h1>Availability</h1>
                    </div>
                    <div class="login-head-right">
                        
                        <div class="profile-dropdown">
                            <div class="dropdown">
                            <div class="dropdown-toggle" data-bs-toggle="dropdown">
                                    <span class="profile-dropdown-img">
                                    <?php
                                    if(isset($profilePicture) && !empty($profilePicture)) {
                                        $profilePicture = str_replace('../../', '', $profilePicture);
                                        $profilePictureUrl = '/' . $profilePicture;
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
                        <li>
                            <a class="active" href="my-lessons"><i class="fa-regular fa-newspaper"></i> <span>My lessons</span></a>
                        </li>
                        <li>
                            <a href="earnings-payment"><i class="fa-regular fa-credit-card"></i> <span>Earnings & payments</span></a>
                        </li>
                        <li>
                            <a href="profile"><i class="fa-solid fa-user-astronaut"></i> <span>My profile</span></a>
                        </li>
                        <li>
                            <a href="reviews"><i class="fa-solid fa-certificate"></i> <span>Reviews</span></a>
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
                    <div id="toast-message" class="toast align-items-center text-white bg-primary border-0 position-fixed top-0 start-50 translate-middle-x" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <!-- The message will be inserted here -->
                                <span id="toast-text"></span>
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                    <div class="page-title-mob">
                        <h1>Availability</h1>
                    </div>

                    <div class="table-area mt-0">
                    <div class="form-btn text-start d-flex">
                        <div class="form-btn text-start d-flex">
                            <select class="inp" name="" id="timezone-select">
                                <option value="Asia/Dubai">Asia/Dubai</option>
                                <option value="UTC-10:00 Pacific/Honolulu">Pacific/Honolulu (UTC-10:00)</option>
                                <option value="UTC-09:00 America/Anchorage">America/Anchorage (UTC-09:00)</option>
                                <option value="UTC-08:00 America/Los_Angeles">America/Los_Angeles (UTC-08:00)</option>
                                <option value="UTC-07:00 America/Denver">America/Denver (UTC-07:00)</option>
                                <option value="UTC-06:00 America/Chicago">America/Chicago (UTC-06:00)</option>
                                <option value="UTC-05:00 America/New_York">America/New_York (UTC-05:00)</option>
                                <option value="UTC-03:00 America/Argentina/Buenos_Aires">America/Argentina/Buenos_Aires (UTC-03:00)</option>
                                <option value="UTC-01:00 Atlantic/Azores">Atlantic/Azores (UTC-01:00)</option>
                                <option value="Europe/Rome">Europe/Rome</option>
                                <option value="UTC+01:00 Europe/Paris">Europe/Paris (UTC+01:00)</option>
                                <option value="UTC+02:00 Europe/Helsinki">Europe/Helsinki (UTC+02:00)</option>
                                <option value="UTC+03:00 Asia/Beirut">Asia/Beirut (UTC+03:00)</option>
                                <option value="UTC+04:00 Asia/Dubai">Asia/Dubai (UTC+04:00)</option>
                                <option value="UTC+05:00 Asia/Karachi">Asia/Karachi (UTC+05:00)</option>
                                <option value="UTC+05:30 Asia/Kolkata">Asia/Kolkata (UTC+05:30)</option>
                                <option value="UTC+06:00 Asia/Dhaka">Asia/Dhaka (UTC+06:00)</option>
                                <option value="UTC+07:00 Asia/Jakarta">Asia/Jakarta (UTC+07:00)</option>
                                <option value="UTC+08:00 Asia/Shanghai">Asia/Shanghai (UTC+08:00)</option>
                                <option value="UTC+09:00 Asia/Tokyo">Asia/Tokyo (UTC+09:00)</option>
                                <option value="UTC+10:00 Australia/Sydney">Australia/Sydney (UTC+10:00)</option>
                                <option value="UTC+12:00 Pacific/Fiji">Pacific/Fiji (UTC+12:00)</option>
                                <option value="UTC+14:00 Pacific/Kiritimati">Pacific/Kiritimati (UTC+14:00)</option>
                            </select>
                            <button class="site-link small ms-2" id="timezone-edit-button"><i class="fa-regular fa-pen-to-square"></i> Edit</button>
                        </div>
                        <!-- Add a switch button here with name "Go Offline" -->
                    </div>

                    <div class="table-area extend">
                        <div class="course-details-left">
                            <div class="box">
                                <div>
                                    <h3 class="title">Set Your Weekly Schedule</h3>
                                    <p>Welcome to your scheduling portal! As a valued tutor, you have the flexibility to select your preferred working hours. Please use this space to indicate your availability for the upcoming week. This ensures students can book sessions with you during your active hours.</p>
                                </div>
                                <div class="table-responsive">
                                    <table class="table calendar-table">
                                        <thead>
                                            <tr>
                                                <th>Sun</th>
                                                <th>Mon</th>
                                                <th>Tue</th>
                                                <th>Wed</th>
                                                <th>Thu</th>
                                                <th>Fri</th>
                                                <th class="week-end txt-orange">Sat</th>
                                            </tr>
                                        </thead>
                                        <tbody id="availability">
                                            <!-- The availability times will be filled in by JavaScript  -->
                                        </tbody>
                                    </table>
                                </div>
                                <button id="save-availability">Save Availability</button>
                            </div>
                        </div>
                    </div>
                </div>

                <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
                <script src="./assets/js/custom.js"></script>
                <script src="./assets/js/saveData.js"></script>
                <script src="./assets/js/availability.js"></script>
            </div>
        </div>
    </body>
</html>
