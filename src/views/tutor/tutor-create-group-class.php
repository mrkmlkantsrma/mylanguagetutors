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

require_once __DIR__ .'/../../models/TutorGroupClass.php';

$TutorGroupClass = new TutorGroupClass();
$tutors = $TutorGroupClass->getTutors();

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
                    <h1>Tutor Create Group Classes</h1>
                </div>
                    <form action="../../controllers/TutorGroupClassController.php" method="POST" enctype="multipart/form-data">
                    <div class="table-area extend">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="title">Title</label>
                                    <input class="inp" type="text" id="title" name="title" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="description">Description</label>
                                    <textarea class="inp" id="description" name="description" rows="3" required 
                                            onkeyup="updateCharacterCount(this)" 
                                            minlength="150" 
                                            maxlength="250"></textarea>
                                    <div id="charCount">0/250</div>
                                    <div id="errorMsg" style="color: red; display: none;">Minimum 150 characters required.</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="first-day">First Day</label>
                                    <select class="inp" id="first-day" name="first_day">
                                        <option value="Monday">Monday</option>
                                        <option value="Tuesday">Tuesday</option>
                                        <option value="Wednesday">Wednesday</option>
                                        <option value="Thursday">Thursday</option>
                                        <option value="Friday">Friday</option>
                                        <option value="Saturday">Saturday</option>
                                        <option value="Sunday">Sunday</option>
                                    </select>
                                    <label for="first-time">Time</label>
                                    <input class="inp" type="time" id="first-time" name="first_time">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="second-day">Second Day</label>
                                    <select class="inp" id="second-day" name="second_day">
                                        <option value="Monday">Monday</option>
                                        <option value="Tuesday">Tuesday</option>
                                        <option value="Wednesday">Wednesday</option>
                                        <option value="Thursday">Thursday</option>
                                        <option value="Friday">Friday</option>
                                        <option value="Saturday">Saturday</option>
                                        <option value="Sunday">Sunday</option>
                                    </select>
                                    <label for="second-time">Time</label>
                                    <input class="inp" type="time" id="second-time" name="second_time">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="number-of-classes">Number of Classes</label>
                                    <input class="inp" type="number" id="number-of-classes" name="number_of_classes" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="duration">Duration (minutes)</label>
                                    <input class="inp" type="number" id="duration" name="duration" required>
                                </div>
                            </div>
                           
                            <!-- Assign Tutor  -->
                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="tutor">Tutor</label>
                                    <input type ="text" class="inp" value="<?php if(isset($_SESSION['username'])) echo $_SESSION['username']; ?>" readonly>
                                </div>
                            </div>

                            <div class="col-sm-6"  style="display:none;">
                                <div class="inp-wrap sm">
                                    <label for="tutor">Tutor</label>
                                    <input type ="text" class="hidden" value="<?php echo $_SESSION['user_id']; ?>">
                                </div>
                            </div>


                            <!-- Hidden input field to hold the tutor's email -->
                            <input type="hidden" id="tutor_email" name="tutor_email" value="<?php echo $_SESSION['userData']['email']; ?>">

                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="pricing">Pricing (USD)</label>
                                    <input class="inp" type="number" step="0.01" id="pricing" name="pricing" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="inp-wrap sm">
                                        <label for="class-cover-image">Cover Image</label>
                                        <input class="inp" type="file" id="class-cover-image" name="class_cover_image" accept="image/*" onchange="previewImage(this)">
                                        <p>Accepted formats: jpg, jpeg, png. Max size: 5MB.</p>
                                        <img id="cover-image-preview" src="" alt="Class Cover Preview" style="max-width: 50%; display: none; margin-top: 10px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="action" value="create">

                        <div class="inp-wrap sm">
                            <input class="site-link sm" type="submit" value="Publish">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>  

  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>    

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

  <script src="./assets/js/custom.js"></script>  

    <script>
    function previewImage(input) 
    {
        if (input.files && input.files[0]) 
        {
            var reader = new FileReader();

            reader.onload = function (e) {
                var preview = document.getElementById('cover-image-preview');
                preview.src = e.target.result;
                preview.style.display = 'block'; // display the preview image
            }

            reader.readAsDataURL(input.files[0]); // read the data as URL
        }
    }

    function updateCharacterCount(textarea) 
    {
        const charCount = document.getElementById('charCount');
        const errorMsg = document.getElementById('errorMsg');
        const currentLength = textarea.value.length;

        charCount.textContent = `${currentLength}/250`;

        if (currentLength < 150) {
            textarea.style.color = 'red';
            errorMsg.style.display = 'block';
        } else {
            textarea.style.color = 'black';
            errorMsg.style.display = 'none';
        }
    }

    function updateTutorEmail(selectElement) 
    {
        var email = selectElement.options[selectElement.selectedIndex].getAttribute('data-email');
        document.getElementById('tutor_email').value = email;
    }
    </script>
  </body>

</html>

