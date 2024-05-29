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



$lessonController->showEarnings();



$tutorUsername =$_SESSION['username'];

$earnings = $lessonController->getTutorEarnings($tutorUsername);



$paypalemail = $lessonController->getWithdrawalEmailController();



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

    <title>MyLanguageTutor : Earnings</title>

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

        .disabled-btn{

            cursor: not-allowed;

            opacity: 0.5;

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

                    <h1>Earnings</h1>

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

                    <li><a class="active" href="earnings-payment"><i class="fa-regular fa-credit-card"></i> <span>Earnings & payments</span></a></li>

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



                <div class="page-title-mob">

                    <h1>Earnings</h1>

                </div>



                <?php 

                    if(isset($_SESSION['message'])) {

                        echo '<div class="alert alert-success">'.$_SESSION['message'].'</div>';

                        unset($_SESSION['message']); // remove it after displaying

                    }

                    if(isset($_SESSION['error'])) {

                        echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';

                        unset($_SESSION['error']); // remove it after displaying

                    }

                ?>

                <div class="row">

                    <div class="col-sm-6 col-lg-4">

                        <div class="info-box">

                            <span class="info-icon"><i class="fa-solid fa-money-bill-transfer"></i></span>

                            <div class="action-box">

                                <div>

                                    <h3>Available <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Tooltip on top"><i class="fa-solid fa-circle-exclamation"></i></span></h3>

                                    <h4>$<?php echo number_format($earnings['available'], 2); ?></h4>

                                </div>



                                <?php

                                    $earningsAvailable = isset($earnings['available']) ? $earnings['available'] : 0;

                                    

                                    if (isset($_SESSION['withdrawal_email']) && !empty($_SESSION['withdrawal_email']) && $earningsAvailable > 0) {

                                        // If withdrawal_email is set, is not empty, and earnings available is greater than 0, show the Withdraw button

                                        ?>

                                        <a class="site-link small grey" data-bs-toggle="modal" data-bs-target="#withdrawModal">

                                            <img src="./assets/images/paypal.png" alt=""> Withdraw

                                        </a>

                                        <?php

                                    } elseif(isset($_SESSION['withdrawal_email']) && !empty($_SESSION['withdrawal_email']) && ($earningsAvailable == 0 || empty($earningsAvailable))) {

                                        // If withdrawal_email is set, is not empty, but earnings available is zero or empty, show non-clickable Withdraw button

                                        ?>

                                        <span class="site-link small grey disabled disabled-btn">

                                            <img src="./assets/images/paypal.png" alt=""> Withdraw

                                        </span>

                                        <?php

                                    } else {

                                        // If withdrawal_email is not set or is empty, show the Payment Method button

                                        ?>

                                        <a class="site-link small grey" data-bs-toggle="modal" data-bs-target="#paymentModal">

                                            <img src="./assets/images/paypal.png" alt=""> Payment method

                                        </a>

                                        <?php

                                    }

                                ?>



                            </div>

                        </div>



                    </div>

                    <div class="col-sm-6 col-lg-4">

                        <div class="info-box">

                            <span class="info-icon"><i class="fa-solid fa-sack-dollar"></i></span>

                            <h3>Total earnings <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Tooltip on top"><i class="fa-solid fa-circle-exclamation"></i></span></h3>

                            <h4>$ <?php echo number_format($earnings['total_earned'], 2); ?></h4>

                        </div>

                    </div>



                    <!-- <div class="col-sm-6 col-lg-4">

                        <div class="info-box">

                            <span class="info-icon"><i class="fa-solid fa-circle-dollar-to-slot"></i></span>

                            <h3>Expected <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Tooltip on top"><i class="fa-solid fa-circle-exclamation"></i></span></h3>

                            <h4>$ 19.97</h4>

                        </div>

                    </div> -->

                </div>

                <!--Payment method Modal -->

                <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">

                    <div class="modal-dialog">

                        <div class="modal-content">

                            <div class="modal-header">

                                <p class="modal-title" id="paymentModalLabel">Enter your Paypal Email</p>

                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                            </div>

                            <div class="modal-body">

                                <form action="../../controllers/LessonController.php" method="post">

                                    <div class="d-flex justify-content-between">

                                        <input type="text" value='<?php echo $_SESSION['username']; ?>' hidden name="username">

                                        <input type="email" class="form-control me-2" name="paymentEmail" placeholder="Enter your email" required>

                                        <input type="hidden" name="action" value="saveWithdrawalEmail">

                                        <button type="submit" class="btn btn-primary">Save</button>

                                    </div>

                                </form>

                            </div>

                        </div>

                    </div>

                </div>



                <!--Withdraw Modal-->

                <div class="modal fade" id="withdrawModal" tabindex="-1" aria-labelledby="withdrawModalLabel" aria-hidden="true">

                    <div class="modal-dialog">

                        <div class="modal-content">

                            <div class="modal-header">

                                

                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                            </div>

                            <div class="modal-body">

                                <form action="../../controllers/LessonController.php" method="post">

                                    <p>Withdraw  <?php echo number_format($earnings['available'], 2); ?> to <em><?php

                                        if(isset($_SESSION['withdrawal_email'])) {

                                            echo $_SESSION['withdrawal_email'];

                                        }

                                        ?></em> </p> <br>

                                    

                                    <!-- Hidden input for the available earnings amount -->

                                    <input type="hidden" name="availableEarnings" value="<?php echo $earnings['available']; ?>">

                                    

                                    <!-- Hidden input for the PayPal email from the session if needed -->

                                    <input type="hidden" name="paypalEmail" value="<?php echo $_SESSION['withdrawal_email']; ?>">



                                    <button class="site-link small" type="submit" name="action" value="withdraw">Request Withdrawal</button>

                                </form>

                            </div>

                        </div>

                    </div>

                </div>



                <h2 class="title pt-5">My Earnings Activities</h2>

                <div class="table-area">

                    <div class="table-responsive">

                        <table class="table theme-table">

                            <thead>

                                <tr>

                                    <th>Date <span><img src="./assets/images/filter.png" alt=""></span></th>

                                    <th>Activity <span><img src="./assets/images/filter.png" alt=""></span></th>

                                    <th>Student <span><img src="./assets/images/filter.png" alt=""></span></th>

                                    <th>Amount <span><img src="./assets/images/filter.png" alt=""></span></th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach($earningData as $earning): ?>

                                <tr>

                                <td>

                                <?php 

                                $formattedDate = ($earning["activity"] === "Withdrawal") ? date("Y-m-d", strtotime($earning["date"])) : $earning["date"];

                                echo htmlspecialchars($formattedDate);

                                ?>

                            </td>



                                    <td><?php echo htmlspecialchars($earning["activity"]); ?></td>

                                    <td><?php echo isset($earning["student"]) ? htmlspecialchars($earning["student"]) : "-"; ?></td>



                                    <td>

                                        <?php

                                        if ($earning["activity"] === "Withdrawal") {

                                            echo '<span class="red-txt">- $', htmlspecialchars($earning["amount"]), '</span>';

                                        } else {

                                            echo '$ ', htmlspecialchars($earning["amount"]);

                                        }

                                        ?>

                                    </td>

                                </tr>

                                <?php endforeach; ?>

                            </tbody>





                            <!-- <tfoot>

                                <tr>

                                    <td colspan="6">

                                        <span>Showing 1-10 of Total 500</span>

                                        <span class="table-nav">

                                            <a href=""><i class="fa-solid fa-arrow-left"></i></a>

                                            <span>1</span>

                                            <a href=""><i class="fa-solid fa-arrow-right"></i></a>

                                        </span>

                                    </td>

                                </tr>

                            </tfoot> -->

                        </table>

                    </div>

                </div>



            </div>

        </div>



    </div>



    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="./assets/js/custom.js"></script>

    <script>

        $(function () {

    $('[data-bs-toggle="tooltip"]').tooltip();

});



    </script>

</body>



</html>