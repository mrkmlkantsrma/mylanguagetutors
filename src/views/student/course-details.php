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

// UserData
$username = $_SESSION['username'];
$userData = $user->getUserData($username);
$_SESSION['user_data'] = $userData;

// Get the tutor's username from the URL
$username = $_GET['username'];

// Create a new User object
$user = new User();

// Fetch the tutor's details
$tutor = $user->getTutorByUsername($username);

if ($tutor === null) {
    die("No tutor found with username {$username}");
}

$averageRating = $tutor['average_rating'];
$reviews = $tutor['reviews'];

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
    <link rel="stylesheet" href="assets/css/custom.css" />
    <link rel="stylesheet" href="public_assets/css/custom.css" />
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

                <section class="course-listing-public">
                    <div class="container">
                        <div class="course-details">
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="course-details-left">
                                        <div class="course-details-top">
                                            <div class="course-single-img">
                                                <?php
                                                  $profilePicture = $tutor['profile_photo_filepath'];
                                                  if (isset($profilePicture) && !empty($profilePicture)) {
                                                      $profilePicture = str_replace('../../', '', $profilePicture);
                                                      $profilePictureUrl = '/' . $profilePicture;
                                                      echo "<div class='course-single-img'><img src='" . $profilePictureUrl . "' alt='" . $tutor['username'] . "'  /></div>";
                                                  } else {
                                                      echo "<div class='course-single-img'><img src='https://images.pexels.com/photos/415829/pexels-photo-415829.jpeg' alt='" . $tutor['first_name'] . ' ' . $tutor['last_name'] . "' /></div>";
                                                  }
                                                  ?>

                                            </div>
                                            <div class="course-single-right-public">
                                                <div class="course-single-title">
                                                    <h5><?php echo $tutor['username']; ?></h5>

                                                </div>
                                                <div class="tutor-desc">
                                                    <p>
                                                        <strong>Education /Professional Experience</strong> -
                                                        <?php echo $tutor['education_experience']; ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="course-details-bottom">
                                            <div class="tut-meta">
                                                <ul>
                                                    <li>
                                                        <div class="meta-icon"><i class="fa-solid fa-earth-americas"></i></div>
                                                        <div class="meta-txt">
                                                            <p>
                                                                <span>From</span> <br />
                                                                <?php echo $tutor['country']; ?>
                                                            </p>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="meta-icon"><i class="fa-solid fa-user-tie"></i></div>
                                                        <div class="meta-txt">
                                                            <p>
                                                                <span>Teaching</span> <br />
                                                                <?php echo $tutor['languages_spoken']; ?>
                                                            </p>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="meta-icon"><i class="fa-regular fa-flag"></i></div>
                                                        <div class="meta-txt">
                                                            <p>
                                                                <span>Teaching Languages</span> <br />
                                                                <?php echo $tutor['native_language']; ?>
                                                            </p>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="meta-icon"><i class="fa-regular fa-user"></i></div>
                                                        <div class="meta-txt">
                                                            <p>
                                                                <span>Working With</span> <br />
                                                                <?php echo $tutor['working_with']; ?>
                                                            </p>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="meta-icon"><i class="fa-solid fa-video"></i></div>
                                                        <div class="meta-txt">
                                                            <!-- <p><span>Platform</span> <br> <?php //echo $tutor['platforms_used']; ?></p> -->
                                                            <p>
                                                                <span>Platform</span> <br />
                                                                Zoom
                                                            </p>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="meta-icon"><i class="fa-solid fa-video"></i></div>
                                                        <div class="meta-txt">
                                                            <p>
                                                                <span>Teaching Level</span> <br />
                                                                <?php echo $tutor['levels_you_teach']; ?>
                                                            </p>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="course-details-right">
                                        <h5 class="pb-3">
                                            Book -
                                            <?php echo $tutor['username']; ?>
                                        </h5>

                                        <div class="inp-wrap small">
                                            <input id="continue-button" class="site-link full" type="submit" value="View Availability" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="rating-sec">
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="rating-left">
                                        <div class="total-rating">
                                            <h3><?php echo round($tutor['average_rating'], 1); ?></h3>
                                            <div class="pt-2 pb-2">
                                                <?php for($i = 1; $i <= 5; $i++): ?>
                                                <?php if($i <= $tutor['average_rating']): ?>
                                                <i class="fa-solid fa-star"></i>
                                                <?php else: ?>
                                                <i class="fa-regular fa-star"></i>
                                                <?php endif; ?>
                                                <?php endfor; ?>
                                            </div>
                                            <p><?php echo count($tutor['reviews']); ?> Reviews</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <div class="rating-right">
                                        <div class="tutor-review">
                                            <?php foreach($tutor['reviews'] as $review): ?>
                                            <div class="tutor-review-single">
                                                <div class="tutor-review-txt">
                                                    <div class="d-flex align-items-center">
                                                        <h5><?php echo htmlspecialchars($review['student_username']); ?></h5>
                                                        <div class="ps-2">
                                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                            <?php if($i <= $review['star_rating']): ?>
                                                            <i class="fa-solid fa-star"></i>
                                                            <?php else: ?>
                                                            <i class="fa-regular fa-star"></i>
                                                            <?php endif; ?>
                                                            <?php endfor; ?>
                                                        </div>
                                                    </div>
                                                    <p class="pb-2"><strong><?php echo date("F j, Y", strtotime($review['review_date'])); ?></strong></p>
                                                    <p>
                                                        <?php echo htmlspecialchars($review['review']); ?>
                                                    </p>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="./public_assets/js/custom.js"></script>
    <script src="./assets/js/datepicker.js"></script>
</body>

</html>