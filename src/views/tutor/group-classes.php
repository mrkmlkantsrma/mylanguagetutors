<?php

if(session_status() == PHP_SESSION_NONE) 
{
  session_start();
}

if(empty($_SESSION['username']) || $_SESSION['role'] !== 'Tutor') 
{
    // Store the initially requested page in the session
    $_SESSION['requested_page'] = $_SERVER['REQUEST_URI'];
    header('Location: ../../../login.php');
    exit();
}

require_once __DIR__ . '/../../controllers/UserController.php';
require_once __DIR__ . '/../../models/User.php';

require_once __DIR__ .'/../../controllers/TutorGroupClassController.php';
require_once __DIR__ .'/../../models/TutorGroupClass.php';

// Assuming the GroupClassController has the method to get all group classes
$TutorGroupClassController = new TutorGroupClassController(); 
$allClasses = $TutorGroupClassController->TutorGetAllGroupClasses();



$username = $_SESSION['username'];

$userData = $user->getUserData($username);

$_SESSION['user_data'] = $userData;


?>


<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, shrink-to-fit=no">
    <link rel="shortcut icon" href="assets/images/favicon.png">
    <title>MyLanguageTutor : Group Classes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="stylesheet" href="assets/css/group-classes.css">

    <script type="text/javascript">
    function googleTranslateElementInit() 
    {
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
                    <h1>Group Classes</h1>
                </div>
                <div class="table-top">
                    <a href="tutor-create-group-class.php">Create New</a>
                </div>
                <?php 
                if (isset($_SESSION['success_msg'])) {
                    echo '<div class="success-message">' . $_SESSION['success_msg'] . '</div>';
                    unset($_SESSION['success_msg']);  // Clear the message so it's not shown again
                }
                ?>
                <div class="table-area extend mt-0">
                    <div class="table-responsive">
                        <table class="table theme-table">
                            <thead>
                                <tr>
                                    <th>TITLE</th>
                                    <th>PRICE</th>
                                    <th>TUTOR</th>
                                    <th>STATUS</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($allClasses as $class) : ?>
                                    <tr class="group-classes">
                                        <td>
                                            <a href="tutor-view-class.php?classId=<?php echo htmlspecialchars($class['class_id']); ?>">
                                                <?php echo htmlspecialchars($class['title']); ?>
                                            </a>
                                            <div class="actions">
                                                <a href="tutor-edit-group-class.php?classId=<?php echo htmlspecialchars($class['class_id']); ?>" class="edit-action">Edit</a> 
                                                
                                                <!-- Draft Form -->
                                                <form action="../../controllers/TutorGroupClassController.php" method="POST" style="display:inline;">
                                                    <input type="hidden" name="action" value="draft">
                                                    <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class['class_id']); ?>">
                                                    <button type="submit" class="btn-action">Draft</button>
                                                </form>

                                                <!-- Delete Form with a confirmation -->
                                                <form action="../../controllers/TutorGroupClassController.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this class?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class['class_id']); ?>">
                                                    <button type="submit" class="btn-action">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                        <td>$<?php echo htmlspecialchars($class['pricing']); ?></td>
                                        <!-- <td><?php //echo isset($class['tutor_name']) ? htmlspecialchars($class['tutor_name']) : 'N/A'; ?></td> -->
                                        <td>
                                        <?php if(isset($_SESSION['username']))
                                        {
                                            echo $_SESSION['username']; 
                                        }
                                        else
                                        {
                                            echo 'N/A'; 
                                        }?>
                                        </td>
                                        <td><?php echo htmlspecialchars($class['status']); ?></td>
                                        <td>
                                            <a href="#" onclick="copyToClipboard(this, 'src/views/student/group-classes.php?highlight=<?php echo htmlspecialchars($class['class_id']); ?>');return false;" class="btn-action">Share</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>

                            <tfoot></tfoot>
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
            function htmlspecialchars(str) {
                // A basic JavaScript version of PHP's htmlspecialchars
                if (typeof(str) == "string") {
                    str = str.replace(/&/g, "&amp;"); /* must do &amp; first */
                    str = str.replace(/"/g, "&quot;");
                    str = str.replace(/'/g, "&#039;");
                    str = str.replace(/</g, "&lt;");
                    str = str.replace(/>/g, "&gt;");
                }
                return str;
            }

            // This function will copy the provided URL to the clipboard and change the link text
            function copyToClipboard(element, path) {
                // Use window.location.origin to get the base URL
                const baseUrl = window.location.origin;
                const fullPath = baseUrl + '/' + path;

                // Copy the fullPath to the clipboard
                navigator.clipboard.writeText(fullPath).then(() => {
                    element.textContent = 'copied';
                    setTimeout(() => {
                        element.textContent = 'Share';
                    }, 5000);
                }).catch(err => {
                    console.error('Failed to copy text: ', err);
                });
            }

        </script>

  </body>

</html>

