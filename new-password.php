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
        <h2 class="text-center">Insert new password</h2>
        <p class="pt-3 pb-5 text-center">To reset your password, <br> please enter your new password here</p>
        <form action="src/controllers/UserController.php" method="POST">
    <div class="inp-wrap">
        <label for="newPassword">New password</label>
        <div class="password-field">
            <input class="inp" type="password" placeholder="" id="newPassword" name="newPassword">
            <span data-toggle="#newPassword" class="toggle-password">Show</span>
            <div id="password_strength">Enter a min 9 length with Uppercase, Lowercase and character</div>
        </div>
    </div>
    <div class="inp-wrap">
        <label for="confirmPassword">Confirm New password</label>
        <div class="password-field">
            <input class="inp" type="password" placeholder="" id="confirmPassword" name="confirmPassword">
            <span data-toggle="#confirmPassword" class="toggle-password">Show</span>
            <div id="password_match" style="color: red;">Passwords do not match.</div>
        </div>
    </div>
    <!-- Hidden input field for token -->
    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
    
    <div class="inp-wrap">
        <button type="submit" name="resetPassword" class="site-link full" id="submit" disabled>Reset Password</button>
    </div>
    <div class="inp-wrap d-flex justify-content-between">
        <p>Back to <a href="login">Login</a></p>
        <p>Don't Have Account? <a href="register">Enroll Here</a></p>
    </div>
</form>

    </div>
</div>

  </div>  


  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>    
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const newPassword = document.querySelector('#newPassword');
    const confirmPassword = document.querySelector('#confirmPassword');
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    const passwordStrength = document.querySelector('#password_strength');
    const passwordMatch = document.querySelector('#password_match');
    const submitBtn = document.querySelector('#submit');

    togglePasswordButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            const pwdInput = document.querySelector(this.getAttribute('data-toggle'));
            const type = pwdInput.getAttribute('type') === 'password' ? 'text' : 'password';
            pwdInput.setAttribute('type', type);
            this.textContent = this.textContent === 'Show' ? 'Hide' : 'Show';
        });
    });

    newPassword.addEventListener('input', updatePasswordValidation);
    confirmPassword.addEventListener('input', updatePasswordValidation);

    function updatePasswordValidation() {
        const val = newPassword.value;
        let strength = 0;

        if (val.length >= 9) strength += 20;
        if (val.length >= 12) strength += 10;
        if (/[a-z]/.test(val) && /[A-Z]/.test(val)) strength += 20;
        if (/[0-9]/.test(val)) strength += 20;
        if (/\W/.test(val)) strength += 20;
        if (val.length >= 16) strength += 10;

        passwordStrength.textContent = `Password Strength: ${strength}%`;

        if(strength < 65) {
            passwordStrength.style.color = "red";
        } else {
            passwordStrength.style.color = "green";
        }

        if (newPassword.value !== confirmPassword.value || strength < 65) {
            passwordMatch.style.display = "block";
            submitBtn.disabled = true;
        } else {
            passwordMatch.style.display = "none";
            submitBtn.disabled = false;
        }
    }
</script>
  </body>
</html>
