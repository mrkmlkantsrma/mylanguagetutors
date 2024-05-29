<?php
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(empty($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    // Store the initially requested page in the session
    $_SESSION['requested_page'] = $_SERVER['REQUEST_URI'];
    
    header('Location: ../../../login.php');
    exit();
}

require_once __DIR__ . '/../../controllers/adminUsersController.php';
require_once __DIR__ .'/../../controllers/groupClassController.php';
require_once __DIR__ .'/../../models/groupClass.php';

// Assuming the GroupClassController has the method to get all group classes
$groupClassController = new GroupClassController(); // You need to instantiate the GroupClassController
$allClasses = $groupClassController->getAllGroupClasses();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, shrink-to-fit=no">
    <link rel="shortcut icon" href="assets/images/favicon.png">
    <title>MyLanguageTutor : Withdrawal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="stylesheet" href="assets/css/group-classes.css">
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
                    <div class="notific">
                        <!-- <div class="dropdown">
                            <div class="dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fa-regular fa-bell"></i>
                                <span class="notific-count">10</span>
                            </div>
                            <div class="dropdown-menu">
                                <ul>
                                    <li><span><i class="fa-solid fa-volume-low"></i></span> Lorem Ipsum is simply dummy text of the printing and typesetting industry.</li>
                                    <li><span><i class="fa-solid fa-volume-low"></i></span> Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</li>
                                    <li><span><i class="fa-solid fa-volume-low"></i></span> It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. </li>
                                </ul>
                            </div>
                        </div> -->
                    </div>
                    <div class="profile-dropdown">
                    <div class="dropdown">
                            <div class="dropdown-toggle" data-bs-toggle="dropdown">
                                <span class="profile-dropdown-img"><img src="https://images.pexels.com/photos/415829/pexels-photo-415829.jpeg?auto=compress&amp;cs=tinysrgb&amp;w=1260&amp;h=750&amp;dpr=1" alt=""></span>
                                <span class="btn-txt">Admin</span>
                            </div>                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-wrap">
            <div class="side-nav">
                <ul>
                    <li><a href="overview"><i class="fa-solid fa-chart-column"></i> <span>Overview</span></a></li>
                    <li><a href="users"><i class="fa-solid fa-users"></i> <span>Users</span></a></li>
                    <li><a href="lessons"><i class="fa-solid fa-book"></i> <span>Lessons</span></a></li>
                    <li><a class="active" href="group-classes"><i class="fa-solid fa-user-group"></i><span>Group Classes</span></a></li>
                    <li><a href="withdrawal"><i class="fa-solid fa-receipt"></i> <span>Withdrawal</span></a></li>
                    <?php
                    $numberOfUnapprovedTutors = count($unapprovedTutors);
                    ?>

                    <li>
                        <a href="tutor-request">
                            <i class="fa-solid fa-user-plus"></i>
                            <span>Tutor Request</span>
                            <span class="notification-count">+<?= $numberOfUnapprovedTutors ?></span>
                        </a>
                    </li>
                    <li><a href="manage-tutors"><i class="fa-solid fa-list-check"></i> <span>Manage Tutors</span></a></li>
                    <li><a href="payments"><i class="fa-regular fa-money-bill-1"></i> <span>Payments</span></a></li>
                    <li><a href="settings"><i class="fa-solid fa-gear"></i> <span>Settings</span></a></li>
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
                    <a href="create-group-class.php">Create New</a>
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
                                            <a href="view-class.php?classId=<?php echo htmlspecialchars($class['class_id']); ?>">
                                                <?php echo htmlspecialchars($class['title']); ?>
                                            </a>
                                            <div class="actions">
                                                <a href="edit-group-class.php?classId=<?php echo htmlspecialchars($class['class_id']); ?>" class="edit-action">Edit</a> 
                                                
                                                <!-- Draft Form -->
                                                <form action="../../controllers/groupClassController.php" method="POST" style="display:inline;">
                                                    <input type="hidden" name="action" value="draft">
                                                    <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class['class_id']); ?>">
                                                    <button type="submit" class="btn-action">Draft</button>
                                                </form>

                                                <!-- Delete Form with a confirmation -->
                                                <form action="../../controllers/groupClassController.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this class?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class['class_id']); ?>">
                                                    <button type="submit" class="btn-action">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                        <td>$<?php echo htmlspecialchars($class['pricing']); ?></td>
                                        <td><?php echo isset($class['tutor_name']) ? htmlspecialchars($class['tutor_name']) : 'N/A'; ?></td>
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