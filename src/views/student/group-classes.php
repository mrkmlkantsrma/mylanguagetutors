<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['username']) || $_SESSION['role'] !== 'Student') {
    // Store the initially requested page in the session
    $_SESSION['requested_page'] = $_SERVER['REQUEST_URI'];

    header('Location: ../../../login.php');
    exit();
}

require_once __DIR__ . '/../../controllers/UserController.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/groupClass.php';

// get all tutors data
$language = $_GET['language'] ?? null;
$tutors = $user->getTutorsData($language);

// Userdata
$username = $_SESSION['username'];
$userData = $user->getUserData($username);
$_SESSION['user_data'] = $userData;

// Save student_id in session if it exists in user data as 'id'
if (isset($userData['id'])) {
    $_SESSION['student_id'] = $userData['id'];
}

// Access student_id from session
$student_id = isset($_SESSION['student_id']) ? $_SESSION['student_id'] : "Not Set";

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


$groupClass = new GroupClass();
$groupClasses = $groupClass->getAllGroupClasses();

// Helper function to determine if a class can be joined
function canJoinClass($scheduledDate, $scheduledTime)
{
    $classDateTime = new DateTime("$scheduledDate $scheduledTime");
    $currentTime = new DateTime("now", new DateTimeZone('UTC')); // Assuming your server is set to UTC

    $classStart = clone $classDateTime;
    $classStart->modify('-10 minutes');

    $classEnd = clone $classDateTime;
    $classEnd->modify('+60 minutes');

    return $currentTime >= $classStart && $currentTime <= $classEnd;
}

function convertTo12HourFormat($time)
{
    $dateTime = new DateTime($time);
    return $dateTime->format('g:i A');
}

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, shrink-to-fit=no">
    <link rel="shortcut icon" href="assets/images/favicon.png">
    <title>MyLanguageTutor : Group Classes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="stylesheet" href="assets/css/group-classes-style.css" />
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
                    <h1>Group Classes</h1>
                </div>
                <div class="login-head-right">
                    
                    <div class="profile-dropdown">
                        <div class="dropdown">
                            <div class="dropdown-toggle" data-bs-toggle="dropdown">
                                <span class="profile-dropdown-img">
                                    <?php
                                    if (isset($profilePicture) && !empty($profilePicture)) {
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
                    <li><a href="find-a-tutor"><i class="fa-solid fa-user-tie"></i> <span>Find a tutor</span></a></li>
                    <li><a class="active" href="group-classes"><i class="fa-solid fa-user-group"></i><span>Group Classes</span></a></li>
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
                    <h1>Group Classes</h1>
                </div>

                <div class="containern" data-student-id="<?php echo $student_id; ?>">
                    <!-- Modal for Course Details -->
                    <div class="modal" tabindex="-1" id="courseDetailModal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-body" id="courseDetailModalBody"></div>
                                <div class="modal-footer">
                                    <div id="paypal-course-button-container"></div>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" id="toast-message">
                        <div class="toast-header">
                            <strong class="mr-auto">Notification</strong>
                            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                    </div>
                        <div class="toast-body" id="toast-text"></div>
                    </div>

                    <div id="message"></div>

                    <section class="course-listing">
                        <div class="container">
                            <div class="group-class-head">
                                <h1>Learn Together, Grow Together</h1>
                                <p>Experience the joy of collaborative learning in our group classes. Engage in stimulating discussions, share perspectives, and enhance your language skills in a dynamic setting.</p>
                            </div>
                            <div class="row groupClassList">
                                <?php if (empty($groupClasses)): ?>
                                        <div class="col-12">
                                            <p>No group classes now. Please check back later.</p>
                                        </div>
                                <?php else: ?>
                                        <?php foreach ($groupClasses as $class): ?>
                                                <div class="col-sm-6 col-lg-3 group-class-card" data-class-id="<?= htmlspecialchars($class['class_id']) ?>">
                                                    <div class="group-class-single">
                                                        <div class="group-class-img">
                                                            <a href="#">
                                                                <img src="<?= !empty($class['cover_image_path']) ? '/uploads/groupClasses/' . htmlspecialchars($class['cover_image_path']) : './assets/images/tutor-3.jpg' ?>" alt="<?= htmlspecialchars($class['title']) ?>" />
                                                            </a>
                                                        </div>
                                                        <div class="group-class-details">
                                                            <h5>
                                                                <a href="#"><?= htmlspecialchars($class['title']) ?></a>
                                                            </h5>
                                                            <p><?= htmlspecialchars($class['description']) ?></p>
                                                    
                                                            <div class="class-info">
                                                                <p><i class="fa-solid fa-calendar"></i> Schedules: 
                                                                    <?= htmlspecialchars($class['first_day']) ?>         <?= convertTo12HourFormat($class['first_time']) ?>, 
                                                                    <?= htmlspecialchars($class['second_day']) ?>         <?= convertTo12HourFormat($class['second_time']) ?> Weekly
                                                                </p>
                                                                <p><i class="fa-solid fa-globe-americas"></i> Timezone: Eastern Time</p>
                                                                <p><i class="fa-solid fa-hashtag"></i> Number of Classes: <?= htmlspecialchars($class['number_of_classes']) ?></p>
                                                                <p><i class="fa-solid fa-clock"></i> Duration: <?= htmlspecialchars($class['duration']) ?> mins</p>
                                                            </div>
                                                            <div class="price-enroll">
                                                                <div class="price">
                                                                    <?php if ($class['is_enrolled'] == 0): ?> <!-- Check if the student isn't enrolled -->
                                                                            <p>Price: $<?= htmlspecialchars($class['pricing']) ?></p>
                                                                    <?php else: ?>
                                                                            <p>You're enrolled in this class</p>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <?php if ($class['is_enrolled'] != 0): ?> <!-- Check if the student isn't enrolled -->
                                                                    <a class="enrolled-groupclass-view" href="student-view-class.php?classId=<?php echo htmlspecialchars($class['class_id']); ?>">View</a>
                                                                <?php endif; ?>
                                                                <?php if ($class['enrolled_students_count'] < 10 && $class['is_enrolled'] == 0): ?> <!-- Check if less than 10 students are enrolled and the student isn't enrolled -->
                                                                        <button class="enroll-btn" onclick="enrollInClass('<?= $class['class_id'] ?>', '<?= $class['pricing'] ?>', this)">Enroll</button>
                                                                <?php elseif ($class['is_enrolled'] == 0): ?>
                                                                        <button class="enroll-btn" disabled>Enroll</button> <!-- If 10 students are enrolled, disable the button -->
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="share-class">
                                                                <a href="#" onclick="shareClass('<?= htmlspecialchars($class['class_id']) ?>', this, event);">
                                                                    <i class="fa-solid fa-share"></i> <span>Share</span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                        <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/custom.js"></script>
    <!-- <script src="./assets/js/courseEnroll.js"></script> -->
    <script>
         const PAYPAL_CLIENT_ID = "<?php
         $clientId = $_ENV['PAYPAL_CLIENT_ID'];
         echo $clientId;
         ?>";

        const USER_NAME = "<?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?>";
    </script>
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

        // This function will be called when the share link is clicked
        function shareClass(classId, linkElement, event) {
            if (event) {
                event.preventDefault(); // Prevent the default anchor behavior
            }
            
            const classUrl = window.location.origin + window.location.pathname + '?highlight=' + classId; // Construct the URL

            navigator.clipboard.writeText(classUrl).then(() => {
                // Change the icon or text
                const iconElement = linkElement.querySelector('i');
                const textElement = linkElement.querySelector('span');
                
                if (iconElement) {
                    iconElement.classList.replace('fa-share', 'fa-check'); // Replace share icon with check icon
                }
                
                if (textElement) {
                    textElement.textContent = 'Copied!'; // Change text to Copied!
                }

                // Optionally, revert the icon and text back to original after a few seconds
                setTimeout(() => {
                    if (iconElement) {
                        iconElement.classList.replace('fa-check', 'fa-share');
                    }
                    
                    if (textElement) {
                        textElement.textContent = 'Share';
                    }
                }, 2000); // After 2 seconds, revert back

            }).catch(err => {
                console.error('Could not copy text:', err);
            });
        }

        // Add this after the DOM is fully loaded
        document.addEventListener('DOMContentLoaded', (event) => {
            // Check if the URL has the 'highlight' parameter
            const urlParams = new URLSearchParams(window.location.search);
            const highlightClassId = urlParams.get('highlight');

            if (highlightClassId) {
                const classToHighlight = document.querySelector('.group-class-card[data-class-id="' + highlightClassId + '"]');
                if (classToHighlight) {
                    classToHighlight.classList.add('highlight');
                    // Optionally scroll to the class element
                    classToHighlight.scrollIntoView({ behavior: 'smooth', block: 'center' });

                    // Remove the highlight after 5 seconds with a fade effect
                    setTimeout(() => {
                        classToHighlight.classList.remove('highlight');
                    }, 10000);
                }
            }
        });

    </script>

    <script>
        function enrollInClass(classId, pricing, buttonElement) {
    $(buttonElement).text('Processing').prop('disabled', true);

    $.ajax({
        url: '../../controllers/groupClassController.php',
        method: 'POST',
        data: {
            action: 'enrollInClass',
            classId: classId,
            pricing: pricing
        },
        success: function(response) {
            var responseData = JSON.parse(response);
            if (responseData.checkoutSessionId) {
                // Initialize Stripe with your publishable key
                var stripe = Stripe('<?php echo $_ENV['STRIPE_PUBLISHABLE_KEY']; ?>');

                // Redirect to Stripe checkout
                stripe.redirectToCheckout({
                    sessionId: responseData.checkoutSessionId
                }).then(function (result) {
                    if (result.error) {
                        alert(result.error.message);
                        $(buttonElement).text('Enroll').prop('disabled', false);
                    }
                });
            } else {
                // Handle any errors that occur during the creation of the session
                alert(responseData.error ? responseData.error : 'An error occurred');
                $(buttonElement).text('Enroll').prop('disabled', false);
            }
        },
        error: function() {
            alert('An error occurred while processing your request.');
            $(buttonElement).text('Enroll').prop('disabled', false);
        }
    });
}


    </script>
    
    <script>
    // URLSearchParams object to parse query parameters
    const urlParams = new URLSearchParams(window.location.search);
    const sessionId = urlParams.get('session_id'); // Retrieve the Stripe session ID
    const status = urlParams.get('status'); // Payment status ('success' or 'cancel')
    const messageDiv = document.getElementById('message'); // The div where messages are displayed

    // Function to clear URL parameters to avoid repeating the process on page reload
    function clearUrlParams() {
        const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
        window.history.replaceState({}, document.title, cleanUrl);
    }

    // Function to handle the server response
    function handleServerResponse(responseData) {
        if (responseData.error) {
            messageDiv.innerHTML = `<div class="alert alert-danger" role="alert">${responseData.error}</div>`;
        } else {
            messageDiv.innerHTML = `<div class="alert alert-success" role="alert">${responseData.message}</div>`;
            // Additional logic upon successful enrollment
            // For example, redirecting to a confirmation page or updating the UI
        }
    }

    // Function to call the server endpoint with the session ID
    function processPaymentSuccess(sessionId) {
        fetch('groupClassPaymentSuccess.php?sessionId=' + sessionId)
            .then(response => response.json()) // Parse the JSON response
            .then(data => {
                handleServerResponse(data); // Handle the response from the server
                clearUrlParams(); // Clear the URL parameters
            })
            .catch(error => {
                console.error('Error:', error);
                messageDiv.innerHTML = `<div class="alert alert-danger" role="alert">An error occurred while processing your request.</div>`;
            });
    }

    // Check the payment status and act accordingly
    if (status === 'success' && sessionId) {
        // Call the function to process the payment success
        processPaymentSuccess(sessionId);
    } else if (status === 'cancel') {
        messageDiv.innerHTML = `<div class="alert alert-warning" role="alert">Payment Cancelled. Your payment process was cancelled.</div>`;
        clearUrlParams();
    }
</script>

</body>

</html>