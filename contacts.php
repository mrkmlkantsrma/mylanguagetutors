<?php
$page_title = 'Contact Us | MyLanguageTutor';
$page_description = 'Contact MyLanguageTutor for any inquiries or feedback. Reach out to us to discover expert language tutors online.';

require_once 'header.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

        <section class="contact-section">
            <div class="container">
                <div class="row">
                    <div class="col-sm-7">
                        <div class="contact-left">
                            <h3>Send a Message</h3>
                            <p>Have a question? Let's chat!</p>
                            <form action="src/controllers/processContact.php" method="POST">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="inp-wrap small">
                                            <label for="name">Your Name</label>
                                            <input class="inp" type="text" name="name" placeholder="John Doe" required />
                                        </div> 
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="inp-wrap small">
                                            <label for="email">Email</label>
                                            <input class="inp" type="email" name="email" placeholder="john.doe@example.com" required />
                                        </div>
                                    </div>
                                </div>
                                <div class="inp-wrap small">
                                    <label for="message">Ask your questions/describe your needs</label>
                                    <textarea class="inp" name="message" rows="3" required></textarea>
                                </div>
                                <div class="inp-wrap small">
                                    <input class="site-link" type="submit" value="Submit" />
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <div class="contact-left">
                            <h3>Contact Information</h3>
                            <ul class="footer-link">
                                <li>
                                    <a href="tel:+1819-635-5969"><i class="fa-solid fa-phone-volume"></i> +1 819-635-5969</a>
                                </li>
                                <li>
                                    <a href="https://maps.google.com/?q=Rue+de+l'Acropole,+Gatineau,+QC+J9J+0L7"><i class="fa-solid fa-location-dot"></i> Rue de l'Acropole, Gatineau, QC J9J 0L7</a>
                                </li>
                                <li>
                                    <a href="mailto:contact@mylanguagetutor.ca"><i class="fa-solid fa-envelope"></i>contact@mylanguagetutor.ca</a>
                                </li>
                            </ul>
                        </div>
                        <div class="contact-left mt-3">
                            <h3>Follow Us</h3>
                            <ul class="foot-social">
                                <li>
                                    <a href="https://www.facebook.com/profile.php?id=100089887141753" target="_blank" rel="noopener"><i class="fa-brands fa-square-facebook"></i></a>
                                </li>
                                <li>
                                    <a href="https://www.instagram.com/mylanguagetutor01" target="_blank" rel="noopener"><i class="fa-brands fa-square-instagram"></i></a>
                                </li>
                                <li>
                                    <a href="https://www.linkedin.com/company/my-language-tutor/?viewAsMember=true" target="_blank" rel="noopener"><i class="fa-brands fa-linkedin"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <?php
              if (isset($_GET['status'])) {
                  if ($_GET['status'] == 'success') {
                      echo '<div class="alert alert-success">Message sent successfully!
            </div>
            '; } else { echo '
            <div class="alert alert-danger">There was an error sending your message. Please try again later.</div>
            '; } } ?>
        </section>

        <section class="map-section">
            <iframe loading="lazy" src="https://maps.google.com/maps?q=Gatineau%2C%20QC%2C%20Canada&amp;t=m&amp;z=10&amp;output=embed&amp;iwloc=near" title="Gatineau, QC, Canada" aria-label="Gatineau, QC, Canada"></iframe>
        </section>

        <?php require_once 'footer.php'; ?>
    </body>
</html>
