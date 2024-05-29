<?php 

$page_title = 'MyLanguageTutor : Tutor Register';
$page_description = '';

require_once 'header.php';
?>

    <div class="login-wrap for-register">

     <div class="register-container">

        <form action="/my-language-tutor/src/controllers/UserController.php" method="POST" enctype="multipart/form-data">

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

                        <input class="inp" type="text" id="firstName" name="firstName" placeholder="" required>

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

                        <input class="inp" type="text" id="lastName" name="lastName" placeholder="" required>

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

                        <input class="inp" type="password" id="password" name="password" placeholder="" required>

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

                        <label for="mobileNumber">Mobile Number</label>

                        <input class="inp" type="text" id="mobileNumber" name="mobileNumber" placeholder="" required>

                        <div>

                          <?php 

                              if(isset($_SESSION['errors']['mobileNumber'])) {

                                  echo $_SESSION['errors']['mobileNumber']; 

                                  unset($_SESSION['errors']['mobileNumber']);

                              } 

                          ?>

                        </div>

                    </div>

                </div>

                <div class="col-sm-6">

                    <div class="inp-wrap small">

                        <label for="country">Country</label>

                        <select class="inp" id="country" name="country" required>

                            <option value="">Select Country</option>

                            <option value="United States">United state</option>

                            <option value="India">India</option>

                            <option value="Nigeria">Nigeria</option>

                        </select>

                        <div>

                            <?php 

                                if(isset($_SESSION['errors']['country'])) {

                                    echo $_SESSION['errors']['country']; 

                                    unset($_SESSION['errors']['country']);

                                } 

                            ?>

                        </div>

                    </div>

                </div>

                <div class="col-lg-12">

                    <div class="inp-wrap small">

                        <label for="educationExperience">Education Experience</label>

                        <textarea class="inp" id="educationExperience" name="educationExperience" required></textarea>

                        <div>

                            <?php 

                                if(isset($_SESSION['errors']['educationExperience'])) {

                                    echo $_SESSION['errors']['educationExperience']; 

                                    unset($_SESSION['errors']['educationExperience']);

                                } 

                            ?>

                        </div>

                    </div>

                </div>

                <div class="col-sm-6">

                    <div class="inp-wrap small">

                        <label for="languagesSpoken">Languages Spoken</label>

                        <input class="inp" type="text" id="languagesSpoken" name="languagesSpoken" placeholder="" required>

                        <div>

                            <?php 

                                if(isset($_SESSION['errors']['languagesSpoken'])) {

                                    echo $_SESSION['errors']['languagesSpoken']; 

                                    unset($_SESSION['errors']['languagesSpoken']);

                                } 

                            ?>

                        </div>

                    </div>

                </div>

                <div class="col-sm-6">

                    <div class="inp-wrap small">

                        <label for="nativeLanguage">Native Language</label>

                        <input class="inp" type="text" id="nativeLanguage" name="nativeLanguage" placeholder="" required>

                        <div>

                            <?php 

                                if(isset($_SESSION['errors']['nativeLanguage'])) {

                                    echo $_SESSION['errors']['nativeLanguage']; 

                                    unset($_SESSION['errors']['nativeLanguage']);

                                } 

                            ?>

                        </div>

                    </div>

                </div>                

                <div class="inp-wrap small">

                    <label for="workingWith">Working With</label>

                    <ul class="list-inline">

                        <li>

                            <label class="custom-check">

                                <input type="checkbox" name="workingWith[]" value="Kids">

                                <span class="check-mark"></span>

                            </label>

                            <span class="label">Kids</span>

                        </li>

                        <li>

                            <label class="custom-check">

                                <input type="checkbox" name="workingWith[]" value="Youth">

                                <span class="check-mark"></span>

                            </label>

                            <span class="label">Youth</span>

                        </li>

                        <li>

                            <label class="custom-check">

                                <input type="checkbox" name="workingWith[]" value="Adult">

                                <span class="check-mark"></span>

                            </label>

                            <span class="label">Adult</span>

                        </li>

                        <li>

                            <label class="custom-check">

                                <input type="checkbox" name="workingWith[]" value="Group">

                                <span class="check-mark"></span>

                            </label>

                            <span class="label">Group</span>

                        </li>

                    </ul>

                </div>



                <!-- Levels You Teach -->

                <div class="inp-wrap small">

                    <label for="levelsYouTeach">Levels you teach</label>

                    <ul class="list-inline">

                        <li>

                            <label class="custom-check">

                                <input type="checkbox" name="levelsYouTeach[]" value="Beginner">

                                <span class="check-mark"></span>

                            </label>

                            <span class="label">Beginner</span>

                        </li>

                        <li>

                            <label class="custom-check">

                                <input type="checkbox" name="levelsYouTeach[]" value="Intermediate">

                                <span class="check-mark"></span>

                            </label>

                            <span class="label">Intermediate</span>

                        </li>

                        <li>

                            <label class="custom-check">

                                <input type="checkbox" name="levelsYouTeach[]" value="Advance">

                                <span class="check-mark"></span>

                            </label>

                            <span class="label">Advance</span>

                        </li>

                    </ul>

                </div> 



                <!-- Upload CV -->

                <div class="col-sm-4">

                    <div class="inp-wrap small">

                        <label for="cv">Upload CV</label>

                        <div class="upload-field">

                            <h4><i class="fa-solid fa-cloud-arrow-up"></i></h4>

                            <p>Upload Your CV</p>

                            <input type="file" id="cv" name="cv" required>

                            <div>

                                <?php 

                                    if(isset($_SESSION['errors']['cv'])) {

                                        echo $_SESSION['errors']['cv']; 

                                        unset($_SESSION['errors']['cv']);

                                    } 

                                ?>

                            </div>

                        </div>

                    </div>

                </div>



                <!-- Upload Profile Photo -->

                <div class="col-sm-4">

                    <div class="inp-wrap small">

                        <label for="profilePhoto">Profile Photo</label>

                        <div class="upload-field">

                            <h4><i class="fa-solid fa-cloud-arrow-up"></i></h4>

                            <p>Upload Your Profile Photo</p>

                            <input type="file" id="profilePhoto" name="profilePhoto" required>

                            <div>

                                <?php 

                                    if(isset($_SESSION['errors']['profilePhoto'])) {

                                        echo $_SESSION['errors']['profilePhoto']; 

                                        unset($_SESSION['errors']['profilePhoto']);

                                    } 

                                ?>

                            </div>

                        </div>

                    </div>

                </div>



                <!-- Upload Official ID -->

                <div class="col-sm-4">

                    <div class="inp-wrap small">

                        <label for="officialID">Upload Official ID</label>

                        <div class="upload-field">

                            <h4><i class="fa-solid fa-cloud-arrow-up"></i></h4>

                            <p>Upload Your Official ID</p>

                            <input type="file" id="officialID" name="officialID" required>

                            <div>

                                <?php 

                                    if(isset($_SESSION['errors']['officialID'])) {

                                        echo $_SESSION['errors']['officialID']; 

                                        unset($_SESSION['errors']['officialID']);

                                    } 

                                ?>

                            </div>

                        </div>

                    </div>

                </div>



                <!-- Video Introduction -->

                <div class="inp-wrap small">

                    <label for="videoIntroduction">Video Introduction</label>

                    <input class="inp" type="text" id="videoIntroduction" name="videoIntroduction" placeholder="Video Introduction link" required>

                    <div>

                        <?php 

                            if(isset($_SESSION['errors']['videoIntroduction'])) {

                                echo $_SESSION['errors']['videoIntroduction']; 

                                unset($_SESSION['errors']['videoIntroduction']);

                            } 

                        ?>

                    </div>

                </div>



                <input type="hidden" name="role" value="Tutor">



                <!-- Info Text -->

                <div class="inp-wrap small">

                    <div class="info-txt">

                        <p>There will be an assessment of profile and a security background check.</p>

                    </div>

                </div>



                <div class="inp-wrap small">

                    <input type="submit" class="site-link" value="Register">

                    <a class="site-link red" href="register">Cancel</a>

                </div>



            </div>

        </form>

     </div>

  </div>







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

