<?php

require_once __DIR__ . '/../../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../'); 
$dotenv->load();

if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(empty($_SESSION['username']) || $_SESSION['role'] !== 'Student') {
    // Store the initially requested page in the session
    $_SESSION['requested_page'] = $_SERVER['REQUEST_URI'];
    
    header('Location: ../../../login.php');
    exit();
}

require_once __DIR__ . '/../../controllers/BillingHistoryController.php';
require_once __DIR__ . '/../../controllers/UserController.php';
require_once __DIR__ . '/../../models/User.php';

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
        <title>MyLanguageTutor : Plans & Payment</title>
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
            .disabled {
    pointer-events: none;
    opacity: 0.6;
}

        </style>
    </head>
    <body data-username="<?php echo $_SESSION['username']; ?>">
    <?php require_once __DIR__ . '/../../controllers/PlanDetailsController.php';?>
        <div class="site-wrapper">
            <div class="site-header">
                <div class="logo"><img src="./assets/images/logo.png" alt="" /></div>
                <div class="site-header-right">
                    <div class="site-title">
                        <span class="collapse-nav"><img src="./assets/images/collapse.png" alt="" /></span>
                        <h1>Plans & Payment</h1>
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
                        <li>
                            <a href="my-lessons"><i class="fa-regular fa-newspaper"></i> <span>My lessons</span></a>
                        </li>
                        <li>
                            <a href="find-a-tutor"><i class="fa-solid fa-user-tie"></i> <span>Find a tutor</span></a>
                        </li>
                        <li><a href="group-classes"><i class="fa-solid fa-user-group"></i><span>Group Classes</span></a></li>
                        <li>
                            <a class="active" href="plans-payment"><i class="fa-regular fa-credit-card"></i> <span>Plans & Payment</span></a>
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
                        <h1>Plans & Payment</h1>
                    </div>
                    <div id="message"></div>
                    <h2 class="title">Subscription Status</h2>
                    <a class="tut-location" href="fee">Go To Plans</a>
                        <div class="row">
                        <div id="planContainer" class="col-sm-6 col-lg-4">
                            <div class="info-box">
                                <span class="info-icon"><i class="fa-solid fa-layer-group"></i></span>
                                <h3>Plan</h3>
                                <div class="action-box">
                                    <h4><?php echo $_SESSION['planName'] ?></h4>
                                    <?php if ($_SESSION['planName'] == 'No active plan'): ?>
                                        <a class="site-link small" href="fee">Subscribe</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="col-sm-6 col-lg-3">
                            <div class="info-box">
                                <span class="info-icon"><i class="fa-solid fa-wifi"></i></span>
                                <h3>Free Trial</h3>
                                <h4 id="trialStatus">Used</h4>
                            </div>
                        </div> -->
                        <div class="col-sm-6 col-lg-4">
                            <div class="info-box">
                                <span class="info-icon"><i class="fa-solid fa-book"></i></span>
                                <h3>Classes</h3>
                                <h4><a href=""><?php echo $_SESSION['classesUsed'] . '/' . $_SESSION['numberOfClasses'] ?> used</a></h4>
                            </div>
                        </div>
                    </div>
                    <h2 class="title pt-5">Billing History</h2>
                    <div class="table-area">
                        <div class="table-responsive">
                            <table class="table theme-table">
                                <thead>
                                    <tr>
                                        <th>ORDER NO <span><img src="./assets/images/filter.png" alt="" /></span></th>
                                        <th>PLAN <span><img src="./assets/images/filter.png" alt="" /></span></th>
                                        <th>PAYMENT METHOD <span><img src="./assets/images/filter.png" alt="" /></span></th>
                                        <th class="text-end">PRICE <span><img src="./assets/images/filter.png" alt="" /></span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $perPage = 15;
                                    $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                                    $controller = new BillingHistoryController();
                                    $history = $controller->getBillingHistory($username, $currentPage, $perPage);
                                    foreach ($history as $record) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($record['order_id']) . "</td>";
                                        echo "<td>" . htmlspecialchars($record['plan_name']) . "</td>";
                                    
                                        // Check if card_type and last_4_digits are available
                                        if (!empty($record['card_type']) && !empty($record['last_4_digits'])) {
                                            echo "<td>" . htmlspecialchars($record['card_type']) . " " . htmlspecialchars($record['last_4_digits']) . "</td>";
                                        } else {
                                            echo "<td>Paypal</td>"; // Default to Paypal when card details are not available
                                        }
                                    
                                        echo "<td class='text-end '>" . htmlspecialchars($record['price']) . "</td>";
                                        echo "</tr>";
                                    }
                                    $totalRecords = $controller->getTotalRecords($username);
                                    $totalPages = ceil($totalRecords / $perPage);
                                    $prevPage = $currentPage > 1 ? $currentPage - 1 : 1;
                                    $nextPage = $currentPage < $totalPages ? $currentPage + 1 : $totalPages;
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6">
                                            <span>Showing <?= ($currentPage - 1) * $perPage + 1 ?>-<?= min($currentPage * $perPage, $totalRecords) ?> of Total <?= $totalRecords ?></span>
                                            <span class="table-nav">
                                                <a href="?page=<?= $prevPage ?>"><i class="fa-solid fa-arrow-left"></i></a>
                                                <span><?= $currentPage ?></span>
                                                <a href="?page=<?= $nextPage ?>"><i class="fa-solid fa-arrow-right"></i></a>
                                            </span>
                                        </td>
                                    </tr>
                                </tfoot>
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
            const PAYPAL_CLIENT_ID = "<?php 
            $clientId = $_ENV['PAYPAL_CLIENT_ID'];
            echo $clientId;
             ?>";

            const USER_NAME = "<?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?>";
        </script>
        <script src="./assets/js/annualAccessFee.js"></script>
        <script>
            $(document).ready(function(){
                $("#annualFeeAction").click(function(){
                    $('#annualDetailModal').modal('show');
                });
            });
        </script>

        <script>
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            const sessionId = urlParams.get('session_id');
            const messageDiv = document.getElementById('message');

            function clearUrlParams() {
                const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                window.history.replaceState({}, document.title, cleanUrl);
            }

            if (status === 'success') {
                messageDiv.innerHTML = '<div class="alert alert-success" role="alert"><h4 class="alert-heading">Payment Successful!</h4><p>Thank you for your payment. Your transaction was successful.</p></div>';

                if (sessionId) {
                    fetch('handlePaymentSuccess.php?sessionId=' + sessionId)
                        .then(response => response.json())
                        .then(data => {
                            console.log('Payment processed:', data);
                            // Additional handling based on server response
                        })
                        .catch(error => console.error('Error:', error));
                }
                clearUrlParams();
            } else if (status === 'cancel') {
                messageDiv.innerHTML = '<div class="alert alert-warning" role="alert"><h4 class="alert-heading">Payment Cancelled</h4><p>Your payment process was cancelled.</p></div>';
                clearUrlParams();
            } else {
                messageDiv.innerHTML = ''; // Clear message if status is not defined
            }
        </script>

</body>
</html>

    </body>
</html>
