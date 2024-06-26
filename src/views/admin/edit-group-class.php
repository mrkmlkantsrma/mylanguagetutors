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
                    <h1>Edit <?php echo htmlspecialchars($currentDetails['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
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
                    <h1>Edit Group Classes</h1>
                </div>
                <form action="../../controllers/groupClassController.php" method="POST" enctype="multipart/form-data">
                    <div class="table-area extend">
                        <div class="row">
                            
                            <!-- Title -->
                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="title">Title</label>
                                    <input class="inp" type="text" id="title" name="title" value="<?php echo htmlspecialchars($currentDetails['title'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                </div>
                            </div>
                            
                            <!-- Description -->
                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="description">Description</label>
                                    <textarea class="inp" id="description" name="description" rows="3" required><?php echo htmlspecialchars($currentDetails['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                                </div>
                            </div>
                            
                            <!-- First Day -->
                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="first-day">First Day</label>
                                    <select class="inp" id="first-day" name="first_day">
                                        <option value="Monday" <?php echo $currentDetails['first_day'] == 'Monday' ? 'selected' : ''; ?>>Monday</option>
                                        <option value="Tuesday" <?php echo $currentDetails['first_day'] == 'Tuesday' ? 'selected' : ''; ?>>Tuesday</option>
                                        <option value="Wednesday" <?php echo $currentDetails['first_day'] == 'Wednesday' ? 'selected' : ''; ?>>Wednesday</option>
                                        <option value="Thursday" <?php echo $currentDetails['first_day'] == 'Thursday' ? 'selected' : ''; ?>>Thursday</option>
                                        <option value="Friday" <?php echo $currentDetails['first_day'] == 'Friday' ? 'selected' : ''; ?>>Friday</option>
                                        <option value="Saturday" <?php echo $currentDetails['first_day'] == 'Saturday' ? 'selected' : ''; ?>>Saturday</option>
                                        <option value="Sunday" <?php echo $currentDetails['first_day'] == 'Sunday' ? 'selected' : ''; ?>>Sunday</option>

                                    </select>
                                    <label for="first-time">Time</label>
                                    <input class="inp" type="time" id="first-time" name="first_time" value="<?php echo htmlspecialchars($currentDetails['first_time'], ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                            </div>
                            
                            <!-- Second Day -->
                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="second-day">Second Day</label>
                                    <select class="inp" id="second-day" name="second_day">
                                        <option value="Monday" <?php echo $currentDetails['second_day'] == 'Monday' ? 'selected' : ''; ?>>Monday</option>
                                        <option value="Tuesday" <?php echo $currentDetails['second_day'] == 'Tuesday' ? 'selected' : ''; ?>>Tuesday</option>
                                        <option value="Wednesday" <?php echo $currentDetails['second_day'] == 'Wednesday' ? 'selected' : ''; ?>>Wednesday</option>
                                        <option value="Thursday" <?php echo $currentDetails['second_day'] == 'Thursday' ? 'selected' : ''; ?>>Thursday</option>
                                        <option value="Friday" <?php echo $currentDetails['second_day'] == 'Friday' ? 'selected' : ''; ?>>Friday</option>
                                        <option value="Saturday" <?php echo $currentDetails['second_day'] == 'Saturday' ? 'selected' : ''; ?>>Saturday</option>
                                        <option value="Sunday" <?php echo $currentDetails['second_day'] == 'Sunday' ? 'selected' : ''; ?>>Sunday</option>

                                    </select>
                                    <label for="second-time">Time</label>
                                    <input class="inp" type="time" id="second-time" name="second_time" value="<?php echo htmlspecialchars($currentDetails['second_time'], ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="duration">Duration (minutes)</label>
                                    <input class="inp" type="number" id="duration" name="duration" value="<?php echo htmlspecialchars($currentDetails['duration'], ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="number-of-classes">Number of Classes</label>
                                    <input class="inp" type="number" id="number-of-classes" name="number_of_classes" value="<?php echo htmlspecialchars($currentDetails['number_of_classes'], ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                            </div>
                            
                           <!-- Assign Tutor -->
                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="tutor">Tutor</label>
                                    <select class="inp" id="tutor" name="tutor">
                                        <?php foreach ($tutors as $tutor): ?>
                                            <option value="<?php echo $tutor['id']; ?>" <?php echo $currentDetails['tutor_id'] == $tutor['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($tutor['username'], ENT_QUOTES, 'UTF-8'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Pricing -->
                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="price">Price ($)</label>
                                    <input class="inp" type="number" id="pricing" name="pricing" value="<?php echo htmlspecialchars($currentDetails['pricing'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                </div>
                            </div>
                            
                            <!-- Cover Image -->
                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="cover_image">Cover Image</label>
                                    <input class="inp" type="file" id="cover_image" name="class_cover_image" onchange="previewImage(this);">
                                    <img id="cover-image-preview" src="/uploads/groupClasses/<?php echo htmlspecialchars($currentDetails['cover_image_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="Cover Image" style="max-width: 50%;">
                                </div>
                            </div>

                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="current_cover_image" value="<?php echo $currentDetails['cover_image_path']; ?>">


                            <!-- Submit Button -->
                            <div class="col-sm-12">
                                <div class="inp-wrap sm">
                                    <input type="hidden" name="class_id" value="<?php echo $classId; ?>"> <!-- Pass the class ID for updates -->
                                    <input class="site-link sm" type="submit" value="Save">
                                </div>
                            </div>

                        </div>

                    </div>
                </form>

            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="./assets/js/custom.js"></script>
        <script>
            function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            var preview = document.getElementById('cover-image-preview');
            preview.src = e.target.result;
        }

        reader.readAsDataURL(input.files[0]); // read the data as URL
    }
}

        </script>



</body>

</html>