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



$tutorData = getTutorReviews();



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

    <title>MyLanguageTutor : Reviews</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

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

                    <h1>Reviews</h1>

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

                    <li><a href="my-lessons"><i class="fa-regular fa-newspaper"></i> <span>My lessons</span></a></li>

                    <li><a class="active" href="group-classes"><i class="fa-solid fa-user-group"></i><span>Group Classes</span></a></li>

                    <li><a href="earnings-payment"><i class="fa-regular fa-credit-card"></i> <span>Earnings & payments</span></a></li>

                    <li><a href="profile"><i class="fa-solid fa-user-astronaut"></i> <span>My profile</span></a></li>

                    <li><a class="active" href="reviews"><i class="fa-solid fa-certificate"></i> <span>Reviews</span></a></li>

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

                    <h1>Reviews</h1>

                </div>



                <div class="table-area extend mt-0">

    <div class="tutor-review">

        <?php foreach($tutorData['reviews'] as $review): ?>

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

                    <p><?php echo htmlspecialchars($review['review']); ?></p>

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

  </body>

</html>

