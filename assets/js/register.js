$(document).ready(function() {

    var otp = ""; // To store the OTP entered by the user



    $('form').on('submit', function(event) {

       event.preventDefault();

       

       var formData = $(this).serialize(); // Get the form data



       // Submit form data and send email with OTP

       $.ajax({

          url: '../src/controllers/UserController.php', // Update this with your registration endpoint

          method: 'POST',

          data: formData,

          success: function(response) {

             // If registration is successful and email sent, show the OTP modal

             if (response.status === 'success') {

                // Trigger OTP Modal

                $('#otpModal').modal('show');

                startTimer(120, document.querySelector('#timer'));

             } else {

                // Show an error message if there's a problem

                console.error('There was a problem with the registration:', response.error);

             }

          },

          error: function(jqXHR, textStatus, errorThrown) {

             console.error('There was a problem with the AJAX request:', errorThrown);

          }

       });

    });



    $('.otp-input').on('keyup', function() {

       // Collect OTP from input fields

       otp = Array.from($('.otp-input')).map(input => input.value).join('');

    });



    function startTimer(duration, display) {

       var timer = duration, minutes, seconds;

       setInterval(function () {

          minutes = parseInt(timer / 60, 10);

          seconds = parseInt(timer % 60, 10);



          minutes = minutes < 10 ? "0" + minutes : minutes;

          seconds = seconds < 10 ? "0" + seconds : seconds;



          display.textContent = minutes + ":" + seconds;



          if (--timer < 0) {

             display.textContent = "OTP expired";

             // Display a button to resend the OTP or a message that it has expired

             $('#resendOTP').removeClass('d-none');

          }

       }, 1000);

    }



    $('#submitOTP').click(function() {

       // Check OTP

       $.ajax({

          url: '../src/controllers/UserController.php', // update this with your check OTP endpoint

          method: 'POST',

          data: {

             otp: otp

          },

          success: function(response) {

             // If OTP is correct, continue with the registration

             if (response.otpValid) {

                // If OTP is valid, continue with registration or whatever action is needed

                // TODO: Implement this

             } else {

                // Show an error message that the OTP is incorrect

                console.error('The OTP is incorrect.');

             }

          },

          error: function(jqXHR, textStatus, errorThrown) {

             console.error('There was a problem with the AJAX request:', errorThrown);

          }

       });

    });



    $('#resendOTP').click(function(e) {

       e.preventDefault();



       // Resend OTP

       $.ajax({

          url: '../src/controllers/UserController.php', // update this with your resend OTP endpoint

          method: 'POST',

          data: {

             resendOtp: true

          },

          success: function(response) {

             // Reset the timer and clear the OTP inputs

             $('.otp-input').val('');

             startTimer(120, document.querySelector('#timer'));

          },

          error: function(jqXHR, textStatus, errorThrown) {

             console.error('There was a problem with the AJAX request:', errorThrown);

          }

       });

    });
 });





//  