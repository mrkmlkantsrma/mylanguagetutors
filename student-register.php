<?php 

$page_title = 'MyLanguageTutor : Student Register';
$page_description = '';

require_once 'header.php';
?>

  <div class="login-wrap for-register">

     <div class="register-container">

        <form action="/my-language-tutor/src/controllers/UserController.php" method="POST">

            <div class="row">

                <div class="col-sm-6">

                  <div class="inp-wrap small">

                      <label for="username">Username</label>

                      <input class="inp" type="text" id="username" name="username" placeholder="" required>

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

                    <input class="inp" type="email" id="email" name="email" placeholder="" required>

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

                        <input class="inp" type="password" placeholder="" name="password">

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

                <div class="col-sm-6">

                    <div class="inp-wrap small">

                        <label for="role">Register as a</label>

                        <select class="inp" id="role" name="role" required>

                            <option value="Student">Student</option>

                            <option value="Tutor">Tutor</option>

                        </select>

                    </div>

                </div>

                <input type="hidden" name="register" value="1">

                <div class="inp-wrap small">

                    <input type="submit" class="site-link" value="Register">

                    <a class="site-link red" href="register">Cancel</a>

                </div>

            </div>

        </form>

     </div>

  </div>  





  <!-- ... footer and other code ... -->

  <?php require_once 'footer.php'; ?>



  <script>

    $(document).ready(function(){

        $('#username, #email').on('blur', function () {

            var fieldId = $(this).attr('id');

            var fieldValue = $(this).val();



            $.ajax({

                url: '/my-language-tutor/src/controllers/UserController.php',

                method: 'POST',

                data: {

                    [fieldId + '_check']: fieldValue

                },

                success: function (data) {

                    if (data === 'taken') {

                        $('#' + fieldId + '_response').html(fieldId.charAt(0).toUpperCase() + fieldId.slice(1) + ' already taken');

                        $('#register_form input[type="submit"]').prop('disabled', true);

                    } else {

                        $('#' + fieldId + '_response').html('');

                        $('#register_form input[type="submit"]').prop('disabled', false);

                    }

                }

            });

        });

    });

  </script>
  </body>

</html>