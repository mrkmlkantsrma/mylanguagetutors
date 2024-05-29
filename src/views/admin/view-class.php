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
require_once __DIR__ . '/../../models/groupClass.php';

$groupClass = new GroupClass();
$tutors = $groupClass->getTutors();

$classId = $_GET['classId'];
$currentDetails = $groupClass->getGroupClassById($classId);

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
    <style>
    #studentSearch, #studentList {
        display: none;
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
                    <h1> <?php echo htmlspecialchars($currentDetails['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
                </div>
                <div class="login-head-right">
                    <div class="notific">
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
                    <h1>Group Class Details</h1>
                </div>
                <div class="table-area extend">
                    <!-- Add Zoom Meeting Button -->
                    <div class="row zoom-generate">
                        <div class="col-sm-12" id="zoom-container">
                            <button id="generate-zoom-meeting" class="site-link small pointer-hover">Generate Meeting Link</button>
                        </div>
                    </div>

                    <!-- Group Classes Table -->
                    <div class="row">
                        <div class="col-sm-12">
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
                                    <!-- Dynamic rows will be inserted here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="table-area extend">
                    <div class="row student-management">
                        <div class="col-sm-6">
                            <h2>Enrolled Students 
                                <span>
                                    <i id="add-new-student" class="fa fa-plus-circle" aria-hidden="true" style="font-size: 24px; color: #6255a5; cursor: pointer;"></i>
                                </span>
                            </h2>
                            <input type="text" id="studentSearch" placeholder="Search students..." class="form-control">
                            <div id="studentList">
                                <!-- Student list will be populated here in a table format -->
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Select</th>
                                        </tr>
                                    </thead>
                                    <tbody id="student-list-table-body">
                                        <!-- Dynamic student rows will be inserted here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Students Table -->
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Email Address</th>
                                        <th>Country</th>
                                    </tr>
                                </thead>
                                <tbody id="students-table-body">
                                    <!-- Dynamic rows will be inserted here -->
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