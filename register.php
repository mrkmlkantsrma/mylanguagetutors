<?php 

$page_title = 'MyLanguageTutor : Login';
$page_description = '';

require_once 'header.php';

// session_destroy();
$_SESSION['requested_page'] = $_SERVER['REQUEST_URI'];

/**************** Singup with Google ****************************/ 
require_once $pathroot . '/vendor/google-api/Google_Client.php';
require_once $pathroot . '/vendor/google-api/contrib/Google_Oauth2Service.php';
require_once $pathroot . '/vendor/facebook-php-sdk/autoload.php';

$clientId = $_ENV['GOOGLE_CLIENT_ID'];
$clientSecret = $_ENV['GOOGLE_SECRET_KEY'];
$redirectURL = $_ENV['GOOGLE_REDIRECT_URL'];

$gClient = new Google_Client();
$gClient->setApplicationName('Login Using Google');
$gClient->setClientId($clientId);
$gClient->setClientSecret($clientSecret);
$gClient->setRedirectUri($redirectURL);
$google_oauthV2 = new Google_Oauth2Service($gClient);

require_once $pathroot . '/src/config/Database.php';
// Create an instance of the Database class
$database = new Database();
// Get the database connection object
$conn = $database->dbConnection();

use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

$appId         = $_ENV['FACEBOOK_APP_ID']; //Facebook App ID
$appSecret     = $_ENV['FACEBOOK_SECRET_KEY']; //Facebook App Secret
$redirectURL   = $_ENV['FACEBOOK_REDIRECT_URL']; //Callback URL
$fbPermissions = array('email');  //Optional permissions

$fb = new Facebook(array(
    'app_id' => $appId,
    'app_secret' => $appSecret,
    'default_graph_version' => 'v2.2',
));

// Get redirect login helper
$helper = $fb->getRedirectLoginHelper();

// Try to get access token
try {
    if(isset($_SESSION['facebook_access_token'])){
        $accessToken = $_SESSION['facebook_access_token'];
    }else{
          $accessToken = $helper->getAccessToken();
    }
} catch(FacebookResponseException $e) {
     echo 'Graph returned an error: ' . $e->getMessage();
      exit;
} catch(FacebookSDKException $e) {
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
}

/**************** Singup with Google Sec End  *************************/ 

?>
    <div class="login-wrap">

        <!-- <div class="login-head">
      <div class="login-head-left"><img src="./assets/images/logo.png" alt=""></div>
    </div> -->

        <div class="login-left">
            <div class="login-left-img"><img src="./assets/images/login.png" alt=""></div>
        </div>
        <div class="login-right">
            <!-- ... header and other code ... -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="login-wrap for-register">
                        <div class="register-container">
                            <h2 class="text-center">Sign Up</h2>
                            <p class="pt-3 text-center">Create an account</p>
                            <div id="statusMessage">
                            </div>


                            <form action="src/controllers/UserController.php" method="POST">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="inp-wrap small">
                                            <label for="username">Username</label>
                                            <input class="inp" type="text" id="username" name="username" placeholder=""
                                                required>
                                            <div id="username_response">
                                                <?php 
                            if(isset($_SESSION['errors']['username'])) {
                                echo $_SESSION['errors']['username']; 
                                unset($_SESSION['errors']['username']);
                            } 
                        ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="inp-wrap small">
                                            <label for="email">Email</label>
                                            <input class="inp" type="email" id="email" name="email" placeholder=""
                                                required>
                                            <div id="email_response">
                                                <?php 
                            if(isset($_SESSION['errors']['email'])) {
                                echo $_SESSION['errors']['email']; 
                                unset($_SESSION['errors']['email']);
                            } 
                        ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="inp-wrap small">
                                            <label for="firstName">First Name</label>
                                            <input class="inp" type="text" placeholder="" name="firstName">
                                            <div>
                                                <?php 
                                if(isset($_SESSION['errors']['firstName'])) {
                                    echo $_SESSION['errors']['firstName']; 
                                    unset($_SESSION['errors']['firstName']);
                                } 
                            ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="inp-wrap small">
                                            <label for="lastName">Last Name</label>
                                            <input class="inp" type="text" placeholder="" name="lastName">
                                            <div>
                                                <?php 
                                if(isset($_SESSION['errors']['lastName'])) {
                                    echo $_SESSION['errors']['lastName']; 
                                    unset($_SESSION['errors']['lastName']);
                                } 
                            ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="inp-wrap small">
                                            <label for="password">Password</label>
                                            <div class="password-field">
                                                <input class="inp" type="password" placeholder="" id="password"
                                                    name="password">
                                                <span id="togglePassword" class="toggle-password">Show</span>
                                            </div>
                                            <div id="password_strength_wrap">
                                                <div id="password_strength">Enter a min 9 length with Uppercase,
                                                    Lowercase and character</div>
                                            </div>
                                            <div>
                                                <?php 
                                  if(isset($_SESSION['errors']['password'])) {
                                      echo $_SESSION['errors']['password']; 
                                      unset($_SESSION['errors']['password']);
                                  } 
                              ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="inp-wrap small">
                                            <label for="role">Enroll as a</label>
                                            <select class="inp" id="role" name="role" required>
                                                <option value="Student"
                                                    <?php echo (isset($_GET['type']) && $_GET['type'] == 'student') ? 'selected' : ''; ?>>
                                                    Student</option>
                                                <option value="Tutor"
                                                    <?php echo (isset($_GET['type']) && $_GET['type'] == 'tutor') ? 'selected' : ''; ?>>
                                                    Tutor</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div id="success_message">
                                        <?php 
                          if(isset($_SESSION['success'])) {
                              echo $_SESSION['success']; 
                              unset($_SESSION['success']);
                          } 
                      ?>
                                    </div>

                                    <input type="hidden" name="register" value="1">
                                    <div class="inp-wrap small">
                                        <input type="submit" id="submit" class="site-link" value="Enroll" disabled>
                                        <a class="site-link red" href="index.php">Cancel</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- /**************** Singup with Google Sec   *************************/  -->
                <div class="col-sm-12">
                    <div class="row social_register_outer">
                        <a class="register_with_btn" href="javascript:void(0);">Sign up with <span><img
                                    class="google-icon" src="/assets/images/google-img.png" width="32px"
                                    height="32px"><img class="facebook-icon" src="/assets/images/facebook-img.png"
                                    alt="" width="22px" height="22px"></span></a>
                        <div class="social_register_box" style="display:none;">
                            <div class="col-sm-12">
                                <div class="inp-wrap small">
                                    <label for="role">Enroll as a</label>
                                    <select class="inp" id="usersrole" name="usersrole" required>
                                        <option value="Student"
                                            <?php echo (isset($_GET['type']) && $_GET['type'] == 'student') ? 'selected' : ''; ?>>
                                            Student</option>
                                        <option value="Tutor"
                                            <?php echo (isset($_GET['type']) && $_GET['type'] == 'tutor') ? 'selected' : ''; ?>>
                                            Tutor</option>
                                    </select>
                                </div>
                                <input type="hidden" id="user_role" value="Student">
                                <?php  $_SESSION['role'] = 'Student'; ?>
                            </div>
                            <div class="row social_btns">
                                <div class="col-sm-4">
                                    <div class="inp-wrap small google_btn_box">
                                        <?php $authUrl = $gClient->createAuthUrl();
                                            $output = '<a href="'.filter_var($authUrl, FILTER_SANITIZE_URL).'"><div class="google-btn"><div class="google-icon-wrapper"><img class="google-icon" src="/assets/images/google-img.png"></div><p class="btn-text">Sign Up with Google</p></div></a>'; ?>
                                        <div><?php echo $output; ?></div>
                                        <?php  $_SESSION['role'] = 'Student'; ?>
                                        <input type="hidden" id="user_role" value="Student">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="inp-wrap small facebook_btn_box">
                                        <?php
                                            $loginURL = $helper->getLoginUrl($redirectURL, $fbPermissions);
                                            $output = '<a class="btn-fb" href="'.htmlspecialchars($loginURL).'"><div class="fb-content"><div class="logo"><img src="/assets/images/facebook-img.png" alt="" width="32px" height="32px"></div><p>Sign Up with Facebook</p></div></a>'; ?>
                                        <div><?php echo $output; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /**************** Singup with Google Sec End  *************************/  -->
            </div>
            <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
            <script src="https://unpkg.com/tilt.js@1.2.1/dest/tilt.jquery.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
            <script src="./assets/js/custom.js"></script>
            <script src="assets/js/continue.js"></script> 
            <script src="./assets/js/translate.js"></script>
            <script type="text/javascript">
                document.addEventListener("DOMContentLoaded", (event) => {
                    let avlLanguageLinks = document.querySelectorAll(".avl-language .site-link");
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
            </script>
            <script src="https://unpkg.com/tilt.js@1.2.1/dest/tilt.jquery.min.js"></script>
            <script>
            const password = document.querySelector('#password');
            const togglePassword = document.querySelector('#togglePassword');
            const passwordStrength = document.querySelector('#password_strength');
            const submitBtn = document.querySelector('#submit');

            togglePassword.addEventListener('click', function(e) {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.textContent = this.textContent === 'Show' ? 'Hide' : 'Show';
            });

            password.addEventListener('focus', function(e) {
                passwordStrength.textContent = "Password Strength: 0%";
            });

            password.addEventListener('input', function(e) {
                const val = password.value;
                let strength = 0;

                if (val.length >= 9) strength += 20;
                if (val.length >= 12) strength += 10;
                if (/[a-z]/.test(val) && /[A-Z]/.test(val)) strength += 20;
                if (/[0-9]/.test(val)) strength += 20;
                if (/\W/.test(val)) strength += 20;
                if (val.length >= 16) strength += 10;

                passwordStrength.textContent = `Password Strength: ${strength}%`;

                // Check for password strength to change color
                if (strength < 65) {
                    passwordStrength.style.color = "red";
                    submitBtn.disabled = true;
                } else {
                    passwordStrength.style.color = "green";
                    submitBtn.disabled = false;
                }
            });

            /********************* SingUp with Google Sec *******************************/
            jQuery(document).ready(function() {
                jQuery(".register_with_btn").click(function() {
                    jQuery(".social_register_box").toggle();
                });

                jQuery("#usersrole").change(function() {
                    var selectedValue = jQuery(this).val();
                    jQuery("#user_role").val(selectedValue);

                    jQuery.ajax({
                        url: 'register.php',
                        type: 'POST',
                        data: {
                            userRole: selectedValue
                        },
                        success: function(response) {
                            console.log("User role stored in session: " + selectedValue);
                        },
                        error: function(xhr, status, error) {}
                    });
                });
            });
            </script>
            <?php
            if(isset($_POST['userRole'])) 
            {
              $_SESSION['role'] = $_POST['userRole'];
            }
            ?>
            <!-- /********************* SingUp with Google Sec End Script *******************************/  -->

</body>

</html>