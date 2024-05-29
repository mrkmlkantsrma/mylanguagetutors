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
    <title>MyLanguageTutor : Find a tutor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <script type="text/javascript">
    function googleTranslateElementInit() {
      new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
    }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</head>

<body>

    <div class="site-wrapper">

        <div class="site-header">
            <div class="logo"><img src="./assets/images/logo.png" alt=""></div>
            <div class="site-header-right">
                <div class="site-title">
                    <span class="collapse-nav"><img src="./assets/images/collapse.png" alt=""></span>
                    <h1>Find a tutor</h1>
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
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-wrap">
            <div class="side-nav">
                <ul>
                    <li><a href="my-lessons"><i class="fa-regular fa-newspaper"></i> <span>My lessons</span></a></li>
                    <li><a class="active" href="find-a-tutor"><i class="fa-solid fa-user-tie"></i> <span>Find a tutor</span></a></li>
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

                <div class="page-title-mob">
                    <h1>Find a tutor</h1>
                </div>

                <div class="containern">
                    <div class="avl-language">
                        <a class="site-link sm green" href="?">All</a>
                        <a class="site-link sm" href="?language=english">English</a>
                        <a class="site-link sm" href="?language=french">French</a>
                        <a class="site-link sm" href="?language=spanish">Spanish</a>
                        <a class="site-link sm" href="?language=other">Other Languages</a>
                    </div>


                    <div class="row tutList">
                        <?php foreach ($tutors as $tutor): ?>
                            <div class="col-sm-6 col-lg-3">
                                <div class="course-single">
                                    <div class="course-single-img">
                                        <?php
                                        $profilePicture = $tutor['profile_photo_filepath'];
                                        $link = "course-details?username=" . $tutor['username'] . "&languages=" . urlencode($tutor['languages_spoken']);
                                        
                                        echo '<a href="' . $link . '">';

                                        if (isset($profilePicture) && !empty($profilePicture)) {
                                            $profilePicture = str_replace('../../', '', $profilePicture);
                                            $profilePictureUrl = '/' . $profilePicture;
                                            echo "<img src='" . $profilePictureUrl . "' alt='" . $tutor['username'] . "' />";
                                        } else {
                                            echo "<img src='https://images.pexels.com/photos/415829/pexels-photo-415829.jpeg' alt='" . $tutor['username'] . "' />";
                                        }

                                        echo '</a>';
                                        ?>
                                    </div>
                                    <div class="course-single-right">
                                        <div class="course-single-title">
                                            <h5>
                                                <a href="course-details?username=<?= $tutor['username'] ?>&languages=<?= urlencode($tutor['languages_spoken']) ?>">
                                                    <?= $tutor['username'] ?>
                                                </a>
                                            </h5>
                                            <div class="ratings">
                                                <p> <strong><?= number_format($tutor['average_rating'] ?? 0, 1) ?></strong> <i class="fa-solid fa-star"></i></p>
                                            </div>
                                        </div>
                                        <span class="tut-location"><i class="fa-solid fa-location-dot"></i> <?= $tutor['country'] ?></span>
                                        <div class="tut-meta">
                                            <ul>
                                                <li>
                                                    <div class="meta-icon"><i class="fa-regular fa-flag"></i></div>
                                                    <div class="meta-txt">
                                                        <p><span>Teaching Language</span> <br> <?= $tutor['languages_spoken'] ?></p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="meta-icon"><i class="fa-regular fa-map"></i></div>
                                                    <div class="meta-txt">
                                                        <p><span>Teaching Languages</span> <br><?= $tutor['native_language'] ?></p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="meta-icon"><i class="fa-regular fa-user"></i></div>
                                                    <div class="meta-txt">
                                                        <p><span>Teaching Level</span> <br>
                                                            <?php
                                                            if (isset($tutor['levels_you_teach']) && !is_null($tutor['levels_you_teach'])) {
                                                                $teachingLevels = explode(',', $tutor['levels_you_teach']);
                                                                echo implode(", ", $teachingLevels);
                                                            } ?>
                                                        </p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="meta-icon"><i class="fa-solid fa-calendar-days"></i></div>
                                                    <div class="meta-txt">
                                                        <p><span>Available</span> <br>
                                                        </p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="meta-icon"><i class="fa-solid fa-video"></i></div>
                                                    <div class="meta-txt">
                                                        <p><span>Platform</span> <br>
                                                        </p>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/custom.js"></script>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', (event) => {
            let avlLanguageLinks = document.querySelectorAll('.avl-language .site-link');
            let currentUrl = new URL(window.location.href);
            let currentLanguage = currentUrl.searchParams.get("language");

            avlLanguageLinks.forEach((link) => {
                let url = new URL(link.href);
                let language = url.searchParams.get("language");

                if (language === currentLanguage) {
                    link.classList.add("green");
                } else {
                    link.classList.remove("green");
                }
            });
        });
    </script>

</body>

</html>