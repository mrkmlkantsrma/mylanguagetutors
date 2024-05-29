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
require_once __DIR__ . '/../../controllers/LessonController.php';

$lessonController->displayStudentBookingsForSupport();

// get all tutors data
$language = $_GET['language'] ?? null;
$tutors = $user->getTutorsData($language);

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

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, shrink-to-fit=no">
    <link rel="shortcut icon" href="assets/images/favicon.png">
    <title>MyLanguageTutor : Help & support</title>
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
                    <h1>Help & support</h1>
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
                    <li><a href="my-lessons"><i class="fa-regular fa-newspaper"></i> <span>My lessons</span></a></li>
                    <li><a href="find-a-tutor"><i class="fa-solid fa-user-tie"></i> <span>Find a tutor</span></a></li>
                    <li><a href="group-classes"><i class="fa-solid fa-user-group"></i><span>Group Classes</span></a></li>
                    <li><a href="plans-payment"><i class="fa-regular fa-credit-card"></i> <span>Plans & Payment</span></a></li>
                    <li><a href="profile"><i class="fa-solid fa-user-astronaut"></i> <span>My profile</span></a></li>
                    <li><a class="active" href="help-support"><i class="fa-solid fa-circle-info"></i> <span>Help & support</span></a></li>
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
              if (isset($_GET['status'])) {
                  if ($_GET['status'] == 'success') {
                      echo '<div class="alert alert-success">Message sent successfully!</div>';
                  } else {
                      echo '<div class="alert alert-danger">There was an error sending your message. Please try again later.</div>';
                  }
              }
              ?>


                <div class="page-title-mob">
                    <h1>Help & support</h1>
                </div>

                <form action="../../controllers/processStudentSupport.php" method="POST">
                  <div class="table-area extend">
                      <h2 class="title">Help & support</h2>
                      <div class="row">
                          <div class="col-sm-6">
                              <div class="inp-wrap sm">
                                  <label for="name">Name</label>
                                  <input class="inp" type="text" name="name" value="<?php echo $firstName . ' ' . $lastName; ?>" required readonly>
                              </div>
                          </div>
                          <div class="col-sm-6">
                              <div class="inp-wrap sm">
                                  <label for="email">Email</label>
                                  <input class="inp" type="email" name="email" value="<?php echo $email; ?>" required readonly>
                              </div>
                          </div>

                          <div class="col-sm-6">
                            <div class="inp-wrap sm">
                                <label for="lesson">Select relevant lesson (Optional)</label>
                                <select class="inp" name="lesson">
                                  <option value="">Select a lesson</option>
                                  <?php foreach ($GLOBALS['bookings'] as $booking) { 
                                      $date = new DateTime($booking['class_date_time']);
                                      $formattedDate = $date->format('M d, Y');
                                      $lessonDetail = "{$booking['language']} lesson with {$booking['tutor_username']} on $formattedDate Lesson ID: {$booking['id']}";
                                  ?>
                                      <option value="<?php echo $lessonDetail; ?>">
                                          <?php echo $lessonDetail; ?>
                                      </option>
                                  <?php } ?>
                              </select>

                            </div>
                        </div>
                          <div class="col-sm-6">
                              <div class="inp-wrap sm">
                                  <label for="subject">Subject</label>
                                  <input class="inp" type="text" name="subject" required>
                              </div>
                          </div>
                      </div>
                      <div class="inp-wrap sm">
                          <label for="message">Message</label>
                          <textarea class="inp" name="message" rows="3" maxlength="400" oninput="updateCharCount(this)" required></textarea>
                          <span id="charCount">0/400</span>
                      </div>
                      <div class="inp-wrap sm">
                          <input class="site-link sm" type="submit" value="Submit">
                      </div>
                  </div>
              </form>
            </div>

        </div>

  </div>  
  <script>
    function updateCharCount(textarea) {
    document.getElementById('charCount').innerText = `${textarea.value.length}/400`;
}

  </script>
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>    
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./assets/js/custom.js"></script>  
  </body>
</html>
