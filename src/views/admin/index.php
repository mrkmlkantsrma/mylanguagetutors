<?php session_start(); ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, shrink-to-fit=no">
    <link rel="shortcut icon" href="assets/images/favicon.png">
    <title>MyLanguageTutor : Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <script type="text/javascript">
    function googleTranslateElementInit() {
      new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
    }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <style>
      .password-field {
    position: relative;
    display: block;
}

.pass-inp {
    padding-right: 30px; /* or enough to not overlap the text */
}

.toggle-password {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
}
    </style>
  </head>
  <body>  


  <div class="login-wrap">

    <div class="login-head">
      <div class="login-head-left"><img src="./assets/images/logo.png" alt=""></div>
    </div>
    
    <div class="login-left">
      <div class="login-left-img"><img src="./assets/images/login.png" alt=""></div>
    </div>
    <div class="login-right">
      <div class="login-right-main">
        <h2 class="text-center">Login</h2>
        <p class="pt-3 text-center">Login to admin panel</p>
        <form action="../../controllers/LoginController.php" method="POST">
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
      </div>
    </div>
  </div>  


  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>    
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
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
  </body>
</html>
