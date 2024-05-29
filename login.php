<?php 

$page_title = 'MyLanguageTutor : Login';
$page_description = '';

require_once 'header.php';

$_SESSION['requested_page'] = $_SERVER['REQUEST_URI'];

$pathroot = __DIR__;

/**************** Login with Google ****************************/ 
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

/**************** Login with Google Sec End  *************************/ 
?>

  <div class="login-wrap">

  <!-- <div class="login-head">
    <div class="login-head-left"><img src="./assets/images/logo.png" alt=""></div>
  </div> -->
  
  <div class="login-left">
    <div class="login-left-img"><img src="./assets/images/login.png" alt=""></div>
  </div>

    <div class="login-right">
        <div class="login-right-main">
            <!-- ... header and other code ... -->
            <div class="row">
                <div class="col-sm-12">

                  <h2 class="text-center">Login</h2>
                  <p class="pt-3 text-center">Login into your Account</p>
            
                  <form action="src/controllers/LoginController.php" method="POST">
                    <div class="inp-wrap">
                      <label for="username">Username</label>
                      <input class="inp" id="username" type="text" placeholder="Username" name="username" required>
                      <div id="username_response" class="error">
                          <?php 
                              if(isset($_SESSION['errors']['username'])) {
                                  echo $_SESSION['errors']['username']; 
                                  unset($_SESSION['errors']['username']);
                              } 
                          ?>
                        </div>
                      </div>        
                    <div class="inp-wrap">
                      <label for="password">Password</label>
                      <div class="password-field">
                          <input class="inp" id="password" type="password" placeholder="Enter your password" name="password" required>
                          <span id="togglePassword" class="toggle-password">Show</span>
                      </div>
                      <div id="password_response" class="error">
                          <?php 
                              if(isset($_SESSION['errors']['password'])) {
                                  echo $_SESSION['errors']['password']; 
                                  unset($_SESSION['errors']['password']);
                              } 
                          ?>
                      </div>
                    </div>
                    <!-- Email verification error message -->
                    <div class="inp-wrap">
                        <div id="email_response" class="error">
                            <?php 
                                if(isset($_SESSION['errors']['email'])) {
                                    echo $_SESSION['errors']['email']; 
                                    unset($_SESSION['errors']['email']);
                                } 
                            ?>
                        </div>
                      </div>
                      <div class="inp-wrap">
                        <div id="success_response" class="success">
                            <?php 
                                if(isset($_SESSION['success'])) {
                                    echo $_SESSION['success']; 
                                    unset($_SESSION['success']);
                                } 
                            ?>
                        </div>
                    </div>
                    <div class="inp-wrap">
                        <div id="" class="error">
                          <?php 
                                if(isset($_SESSION['errors']['account'])) {
                                    echo $_SESSION['errors']['account']; 
                                    unset($_SESSION['errors']['account']);
                                } 
                            ?>
                        </div>
                    </div>
                    <div class="inp-wrap d-flex align-items-center">
                      <label class="custom-check">
                          <input type="checkbox" name="remember" id="remember">
                          <span class="check-mark"></span>
                      </label>
                      <label class="m-0 p-0 ms-2" for="remember">Keep me signed in</label>
                    </div>
                    
                    <!-- Hidden login field -->
                    <input type="hidden" name="login" value="1">
                    
                    <div class="inp-wrap">
                      <input type="submit" class="site-link full" value="Sign in">
                    </div>
                  </form>
            
                  <div class="inp-wrap d-flex justify-content-between">
                    <p>Don't Have Account? <a href="register.php">Register Here</a></p>
                    <p><a href="forgot-password.php">Forgot your password?</a></p>
              </div>
          </div>

            <!-- /**************** Login with Google Sec   *************************/  -->
            <div class="col-sm-12">
              <div class="row mt-5 social_login_outer">
                
                <div class="social_login_box" >
                    <div class="row social_btns">
                        <div class="col-sm-6">
                          <div class="inp-wrap small google_btn_box">
                              <?php $authUrl = $gClient->createAuthUrl();
                                $output = '<a href="'.filter_var($authUrl, FILTER_SANITIZE_URL).'"><div class="google-btn"><div class="google-icon-wrapper"><img class="google-icon" src="/assets/images/google-img.png"></div><p class="btn-text">Login with Google</p></div></a>'; ?>
                              <div><?php echo $output; ?></div>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="inp-wrap small facebook_btn_box">
                              <?php $loginURL = $helper->getLoginUrl($redirectURL, $fbPermissions);
                                $output = '<a class="btn-fb" href="'.htmlspecialchars($loginURL).'"><div class="fb-content"><div class="logo"><img src="/assets/images/facebook-img.png" alt="" width="32px" height="32px"></div><p>Login with Facebook</p></div></a>'; ?>
                              <div><?php echo $output; ?></div>
                          </div>
                        </div>
                    </div>
                </div>
              </div>
           </div>
           <!-- /**************** Login with Google Sec End  *************************/  -->
            </div>
        </div>
      </div>

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
        
  </body>
  <script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');

    togglePassword.addEventListener('click', function (e) {
        // toggle the type attribute
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        // toggle the text content
        this.textContent = this.textContent === 'Show' ? 'Hide' : 'Show';
    });
  </script>
  <?php
    if($_SESSION){
      if($_SESSION['user_id']){
        if($_SESSION['role'] == 'Admin'){ ?>
          <script>
              jQuery(document).ready(function() {
                  var baseUrl = window.location.origin;
                  var baseUrlpath = window.location.pathname;
                  var fullUrl = window.location.origin + window.location.pathname;
                  window.location.href = baseUrl + '/src/views/admin/overview';
              });
          </script>
        <?php }else{?>
          <script>
              jQuery(document).ready(function() {
                  var baseUrl = window.location.origin;
                  var baseUrlpath = window.location.pathname;
                  var fullUrl = window.location.origin + window.location.pathname;
                  window.location.href = baseUrl;
              });
          </script>
        <?php }
      }
  }
  ?>

</html>
