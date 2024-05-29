<?php
$page_title = 'MyLanguageTutor : Login';
$page_description = '';

require_once 'header.php';
?>

  <div class="login-wrap">

    <div class="login-left">

      <div class="login-left-img"><img src="./assets/images/login.png" alt=""></div>

    </div>

    <div class="login-right">

      <div class="login-right-main">

        <h2 class="text-center">Forgot Password</h2>

        <p class="pt-3 pb-5 text-center">To reset your password, <br> please enter your email address or username below.</p>

        <?php 

    if(isset($_SESSION['success'])) {

        echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>';

        unset($_SESSION['success']); // remove it after displaying

    }

    if(isset($_SESSION['errors']['reset'])) {

        echo '<div class="alert alert-danger">'.$_SESSION['errors']['reset'].'</div>';

        unset($_SESSION['errors']['reset']); // remove it after displaying

    }

?>



        <form action="src/controllers/UserController.php" method="POST">

            <div class="inp-wrap">

                <label for="email">E-mail</label>

                <input class="inp" id="email" name="email" type="email" placeholder="Example@domainname.com" required>

            </div>

            <div class="inp-wrap">

                <button type="submit" name="forgotPassword" class="site-link full">Reset Password</button>

            </div>

            <div class="inp-wrap d-flex justify-content-between">

                <p>Back to <a href="login">Login</a></p>

                <p>Don't Have Account? <a href="register">Register Here</a></p>

            </div>

        </form>

      </div>

</div>



  </div>  




  <?php require_once 'footer.php'; ?>

  </body>

</html>

