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
    <title>MyLanguageTutor : Upgrade</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
    <link rel="stylesheet" href="assets/css/custom.css" />
    <script type="text/javascript">
    function googleTranslateElementInit() {
      new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
    }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <style>
        .modal-dialog {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .toast {
            z-index: 9999;
            background: #171c34;
        }
    </style>
</head>

<body data-username="<?php echo $_SESSION['username']; ?>">
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

                <h2 class="title pt-5 pb-3">Choose a new plan</h2>
                <div class="owl-carousel price-carousal">
                    <div class="item">
                        <div class="col-12">
                            <div class="fees-structure var-3">
                                <div class="price-icon"><i class="fa-solid fa-layer-group"></i></div>
                                <div>
                                    <h3>Casual Learner</h3>
                                    <h4>$<?php $casualLearner = $_ENV['CASUAL_LEARNER_PRICE']; echo $casualLearner;?></h4>
                                    <p>30$ per hour/Less than 5 classes</p>
                                </div>
                                <div class="price-btn">
                                    <button class="site-link full small" onclick="selectPlan('Casual Learner', <?php echo $_ENV['CASUAL_LEARNER_PRICE']; ?>, event)">Select</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="item">
                        <div class="col-12">
                            <div class="fees-structure var-4">
                                <div class="price-icon"><i class="fa-solid fa-tree"></i></div>
                                <div>
                                    <h3>Beginner's Bundle</h3>
                                    <h4>$<?php $beginner = $_ENV['BEGINNERS_BUNDLE_PRICE']; echo $beginner;?></h4>
                                    <p>5 class plan</p>
                                </div>
                                <div class="price-btn">
                                    <button class="site-link full small" onclick="selectPlan('Beginner\'s Bundle', <?php echo $_ENV['BEGINNERS_BUNDLE_PRICE']; ?>, event)">Select</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="item">
                        <div class="col-12">
                            <div class="fees-structure var-5">
                                <div class="price-icon"><i class="fa-solid fa-landmark"></i></div>
                                <div>
                                    <h3>Intermediate Pack</h3>
                                    <h4>$<?php $intermediate = $_ENV['INTERMEDIATE_PACK_PRICE']; echo $intermediate;?></h4>
                                    <p>10 class plan</p>
                                </div>
                                <div class="price-btn">
                                    <button class="site-link full small" onclick="selectPlan('Intermediate Pack', <?php echo $_ENV['INTERMEDIATE_PACK_PRICE']; ?>, event)">Select</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="item">
                        <div class="col-12">
                            <div class="fees-structure var-6">
                                <div class="price-icon"><i class="fa-solid fa-store"></i></div>
                                <div>
                                    <h3>Master Class Package</h3>
                                    <h4>$<?php $master = $_ENV['MASTER_CLASS_PACKAGE_PRICE']; echo $master;?></h4>
                                    <p>20 class plan</p>
                                </div>
                                <div class="price-btn">
                                    <button class="site-link full small" onclick="selectPlan('Master Class Package', <?php echo $_ENV['MASTER_CLASS_PACKAGE_PRICE']; ?>, event)">Select</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Plan Detail Modal -->
                <div class="modal fade" id="planDetailModal" tabindex="-1" aria-labelledby="planDetailModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="planDetailModalLabel">Plan Details</h5>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                            <div class="modal-body" id="planDetailModalBody">
                                <!-- Details will be filled in by JavaScript -->
                            </div>
                            <div class="modal-footer">
                                <div id="paypal-button-container"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="toast-message" class="toast align-items-center text-white bg-primary border-0 position-fixed top-0 start-50 translate-middle-x" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true">
        <div class="d-flex">
            <div class="toast-body">
                <!-- The message will be inserted here -->
                <span id="toast-text"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="./assets/js/custom.js"></script>
    <script>
    function selectPlan(planName, planPrice, event) {
        // Get the clicked button
        var button = $(event.target);

        // Change button text to 'Please Wait' and disable it
        button.text('Please Wait').prop('disabled', true);

        $.ajax({
            url: '../../controllers/PlanController.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                action: 'processPayment',
                planName: planName,
                planPrice: planPrice
            }),
            success: function(response) {
                // Parse the JSON response
                var responseData = JSON.parse(response);
                if (responseData.checkoutSessionId) {
                    // Redirect to the Stripe checkout
                    var stripe = Stripe('<?php echo $_ENV['STRIPE_PUBLISHABLE_KEY']; ?>');
                    stripe.redirectToCheckout({
                        sessionId: responseData.checkoutSessionId
                    }).then(function (result) {
                        // If `redirectToCheckout` fails due to a browser or network
                        // error, display the localized error message to your customer
                        alert(result.error.message);
                    });
                } else {
                    // Handle error here, e.g., show message to the user
                    console.error('Error:', responseData.error);
                }

                // Restore button text and enable it
                button.text('Select').prop('disabled', false);
            },
            error: function(xhr, status, error) {
                // Handle error here, e.g., show message to the user
                console.error('AJAX Error:', status, error);

                // Restore button text and enable it
                button.text('Select').prop('disabled', false);
            }
        });
    }
    </script>
    <script>
        var owl3 = $(".price-carousal");
        owl3.owlCarousel({
            smartSpeed: 1000,
            items: 4,
            margin: 10,
            nav: true,
            dots: false,
            loop: false,
            navText: ["<i class='fa-solid fa-arrow-left-long'></i>", "<i class='fa-solid fa-arrow-right-long'></i>"],
            responsive: {
                0: {
                    items: 1
                },
                500: {
                    items: 3
                },
                789: {
                    items: 4
                },
            },
        });

        $(".price-carousal .item").on("click", function() {
            $(".price-carousal .item.current").removeClass("current");
            $(this).addClass("current");

            $("html, body").animate({
                scrollTop: $("#selectedPlan").position().top,
            });
        });
    </script>
</body>

</html>