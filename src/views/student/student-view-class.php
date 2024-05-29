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
require_once __DIR__ . '/../../models/StudentGroupClass.php';


$StudentGroupClass = new StudentGroupClass();
$tutors = $StudentGroupClass->getTutors();

$classId = $_GET['classId'];
$currentDetails = $StudentGroupClass->TutorGetGroupClassById($classId);
$getMeetings = $StudentGroupClass->getZoomMeetingsByClassId($classId);


$tutorName = "Not assigned";
foreach ($tutors as $tutor) {
    if ($tutor['id'] == $currentDetails['tutor_id']) {
        $tutorName = $tutor['username'];
        break;
    }
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
    <style> #studentSearch, #studentList { display: none; }</style>
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
                    <h1>Group Class Details</h1>
                </div>
                <div class="table-area extend">

                    <!-- Group Classes Table -->
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="page-title">
                                <h1 class="meeting-details-heading">Meeting Details</h1>
                            </div>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Class Title</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Copy Link</th>
                                    </tr>
                                </thead>
                                <tbody id="zoom-meetings-table-body">
                                    <?php foreach ($getMeetings as $Meeting){ ?>
                                        <tr>
                                            <td><?php echo $Meeting['title']; ?></td>
                                            <td><?php echo $Meeting['scheduled_date']; ?></td>
                                            <td><?php echo $Meeting['scheduled_time']; ?></td>
                                            <td>
                                                <button class="copy-btn site-link small" data-link="<?php echo $Meeting['zoom_link']; ?>">Copy</button>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="table-area extend">
                    <div class="row">
                        <!-- Title -->
                        <div class="col-sm-6">
                            <div class="inp-wrap sm">
                                <label>Title</label>
                                <p><?php echo htmlspecialchars($currentDetails['title'] ?? ''); ?></p>
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div class="col-sm-6">
                            <div class="inp-wrap sm">
                                <label>Description</label>
                                <p><?php echo htmlspecialchars($currentDetails['description'] ?? ''); ?></p>
                            </div>
                        </div>
                        
                        <!-- First Day -->
                        <div class="col-sm-6">
                            <div class="inp-wrap sm">
                                <label>First Day</label>
                                <p><?php echo htmlspecialchars($currentDetails['first_day'] ?? ''); ?></p>
                                <label>Time</label>
                                <p><?php echo htmlspecialchars($currentDetails['first_time'] ?? ''); ?></p>
                            </div>
                        </div>
                        
                        <!-- Second Day -->
                        <div class="col-sm-6">
                            <div class="inp-wrap sm">
                                <label>Second Day</label>
                                <p><?php echo htmlspecialchars($currentDetails['second_day'] ?? ''); ?></p>
                                <label>Time</label>
                                <p><?php echo htmlspecialchars($currentDetails['second_time'] ?? ''); ?></p>
                            </div>
                        </div>

                        <!-- Duration -->
                        <div class="col-sm-6">
                            <div class="inp-wrap sm">
                                <label>Duration (minutes)</label>
                                <p><?php echo htmlspecialchars($currentDetails['duration'] ?? ''); ?></p>
                            </div>
                        </div>

                        <!-- Number of Classes -->
                        <div class="col-sm-6">
                            <div class="inp-wrap sm">
                                <label>Number of Classes</label>
                                <p><?php echo htmlspecialchars($currentDetails['number_of_classes'] ?? ''); ?></p>
                            </div>
                        </div>
                        
                    <!-- Tutor -->
                        <div class="col-sm-6">
                            <div class="inp-wrap sm">
                                <label>Tutor</label>
                                <p><?php echo htmlspecialchars($tutorName); ?></p>
                            </div>
                        </div>

                        <!-- Pricing -->
                        <div class="col-sm-6">
                            <div class="inp-wrap sm">
                                <label>Price ($)</label>
                                <p><?php echo htmlspecialchars($currentDetails['pricing'] ?? ''); ?></p>
                            </div>
                        </div>
                        
                        <!-- Cover Image -->
                        <div class="col-sm-6">
                            <div class="inp-wrap sm">
                                <label>Cover Image</label>
                                <!-- Image preview -->
                                <img id="cover-image-preview" src="/uploads/groupClasses/<?php echo htmlspecialchars($currentDetails['cover_image_path'] ?? ''); ?>" alt="Cover Image" style="max-width: 50%;">
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>

        </div>

        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="./assets/js/custom.js"></script>
        <script src="./assets/js/save_group_link.js"></script>
    </body>

</html>