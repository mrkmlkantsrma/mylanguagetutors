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

require_once __DIR__ . '/../../models/adminUsers.php';
require_once __DIR__ . '/../../controllers/adminUsersController.php';
if (isset($_GET['email'])) {
    $email = $_GET['email'];
    $adminUsers = new AdminUsers();

    // Fetch the basic user details by username
    $tutorDetails = $adminUsers->getUnapprovedTutorByEmail($email);

    if (!$tutorDetails) {
        // Handle case when no user found for the given username.
        die('User not found.');
    }

} else {
    // Handle case when no username parameter is provided.
    die('Invalid access.');
}

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, shrink-to-fit=no">
    <link rel="shortcut icon" href="assets/images/favicon.png">
    <title>MyLanguageTutor : Tutor details</title>
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
                    <h1>Users Details</h1>
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
                    <li><a  href="users"><i class="fa-solid fa-users"></i> <span>Users</span></a></li>
                    <li><a href="lessons"><i class="fa-solid fa-book"></i> <span>Lessons</span></a></li>
                    <li><a href="group-classes"><i class="fa-solid fa-user-group"></i><span>Group Classes</span></a></li>
                    <li><a href="withdrawal"><i class="fa-solid fa-receipt"></i> <span>Withdrawal</span></a></li>
                    <?php
                    $numberOfUnapprovedTutors = count($unapprovedTutors);
                    ?>

                    <li>
                        <a class="active" href="tutor-request">
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

            <?php 
                    if(isset($_SESSION['message'])) {
                        echo $_SESSION['message']; 
                        unset($_SESSION['message']);
                    } 
                ?>

                <div class="page-title-mob">
                    <h1>Tutor Details</h1>
                </div>

                <?php if ($tutorDetails['role'] === 'Student'): ?>
                <div class="row">
                    <div class="col-sm-6 col-lg-4">
                        <div class="info-box">
                            <span class="info-icon"><i class="fa-solid fa-crown"></i> </span>
                            <h3>Active plan</h3>
                            <h4>
                                <?php 
                                if (is_array($activePlan)) {
                                    // If it's an array, maybe you want the 'plan_name' key
                                    echo $activePlan['plan_name'];
                                } else {
                                    // Otherwise, directly print or use the value
                                    echo $activePlan;
                                }
                                ?>
                            </h4>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="info-box">
                            <span class="info-icon"><i class="fa-solid fa-coins"></i> </span>
                            <h3>Number of classes completed</h3>
                            <h4><?= $totalClasses ?></h4>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="info-box">
                            <span class="info-icon"><i class="fa-solid fa-sack-dollar"></i></span>
                            <h3>Annual access fee <em>Expires</em></h3>
                            <h4>
                                <?php
                                if (is_array($annualAccessFee)) {
                                    // If it's an array, maybe you want the 'plan_name' key
                                    echo $annualAccessFee['expire_date'];
                                } else {
                                    // Otherwise, directly print or use the value
                                    echo $annualAccessFee;
                                }
                                ?>
                            </h4>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <div class="table-area pt-0 extend">
                    <div class="row">

                        <!-- Displaying Basic Information for all roles -->
                        <div class="col-sm-6">
                            <div class="inp-wrap sm">
                                <label for="">Username</label>
                                <input class="inp" type="text" value="<?php echo empty($tutorDetails['username']) ? 'Not submitted' : $tutorDetails['username']; ?>" readonly>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="inp-wrap sm">
                                <label for="">Country</label>
                                <input class="inp" type="text" value="<?php echo empty($tutorDetails['country']) ? 'Not submitted' : $tutorDetails['country']; ?>" readonly>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="inp-wrap sm">
                                <label for="">Full Name</label>
                                <input class="inp" type="text" value="<?php echo empty($tutorDetails['first_name']) || empty($tutorDetails['last_name']) ? 'Not submitted' : $tutorDetails['first_name'] . ' ' . $tutorDetails['last_name']; ?>" readonly>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="inp-wrap sm">
                                <label for="">Speaking Language</label>
                                <input class="inp" type="text" value="<?php echo empty($tutorDetails['languages_spoken']) ? 'Not submitted' : $tutorDetails['languages_spoken']; ?>" readonly>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="inp-wrap sm">
                                <label for="">Mobile No</label>
                                <input class="inp" type="phone" value="<?php echo empty($tutorDetails['mobile_no']) ? 'Not submitted' : $tutorDetails['mobile_no']; ?>" readonly>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="inp-wrap sm">
                                <label for="">Email Address</label>
                                <input class="inp" type="email" value="<?php echo empty($tutorDetails['email']) ? 'Not submitted' : $tutorDetails['email']; ?>" readonly>
                            </div>
                        </div>

                        <!-- Additional details to be hidden for students -->
                        <?php if ($tutorDetails['role'] !== 'Student'): ?>
                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="">Level Taught</label>
                                    <input class="inp" type="text" value="<?php echo empty($tutorDetails['levels_you_teach']) ? 'Not submitted' : $tutorDetails['levels_you_teach']; ?>" readonly>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="">Education & Experience</label>
                                    <input class="inp" type="text" value="<?php echo empty($tutorDetails['education_experience']) ? 'Not submitted' : $tutorDetails['education_experience']; ?>" readonly>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="">Teaching Languages</label>
                                    <input class="inp" type="text" value="<?php echo empty($tutorDetails['native_language']) ? 'Not submitted' : $tutorDetails['native_language']; ?>" readonly>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="">Working With</label>
                                    <input class="inp" type="text" value="<?php echo empty($tutorDetails['working_with']) ? 'Not submitted' : $tutorDetails['working_with']; ?>" readonly>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="col-sm-6">
                            <div class="inp-wrap sm">
                                <label for="">Profile Photo</label>
                                <?php 
                                $profile_photo_filepath = empty($tutorDetails['profile_photo_filepath']) ? 'Not submitted' : '../' . $tutorDetails['profile_photo_filepath'];
                                if ($profile_photo_filepath !== 'Not submitted'): 
                                ?>
                                <a href="<?php echo $profile_photo_filepath; ?>" target="_blank">
                                    <?php echo basename($profile_photo_filepath); ?> - Download
                                </a>
                                <?php else: ?>
                                <input class="inp" type="text" value="Not submitted" readonly>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($tutorDetails['role'] !== 'Student'): ?>
                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="">CV Filepath</label>
                                    <?php 
                                    $cv_filepath = empty($tutorDetails['cv_filepath']) ? '' : '../' . $tutorDetails['cv_filepath'];
                                    if (!empty($cv_filepath)): 
                                    ?>
                                    <a href="<?php echo $cv_filepath; ?>" target="_blank">
                                        <?php echo basename($cv_filepath); ?> - Download
                                    </a>
                                    <?php else: ?>
                                    <input class="inp" type="text" value="Not submitted" readonly>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="">Official ID Filepath</label>
                                    <?php 
                                    $official_id_filepath = empty($tutorDetails['official_id_filepath']) ? 'Not submitted' : '../' . $tutorDetails['official_id_filepath'];
                                    if ($official_id_filepath !== 'Not submitted'): 
                                    ?>
                                    <a href="<?php echo $official_id_filepath; ?>" target="_blank">
                                        <?php echo basename($official_id_filepath); ?> - Download
                                    </a>
                                    <?php else: ?>
                                    <input class="inp" type="text" value="Not submitted" readonly>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="searchTop">
                        <form action="<?php echo $_SERVER['PHP_SELF'] . '?email=' . urlencode($email); ?>" method="post">
                            <div class="searchTop-left">
                                <p style="white-space: nowrap;"><strong>Edit user status</strong></p>
                                <select class="inp ms-3" name="tutor_status" id="tutor_status">
                                    <option value="">Select Status</option>
                                    <option value="approve">Approve</option>
                                    <option value="unapprove">Request Changes</option>
                                    
                                </select>
                                <input type="hidden" name="email" value="<?php echo $email; ?>">
                                <button class="site-link small ms-3" type="submit" name="update_tutor">Update Status</button>
                            </div>
                        </form>
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
