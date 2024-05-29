<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <!-- Include Stripe.js -->
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <h1>Redirecting to payment...</h1>
    <script>
        // Assuming the session ID is passed as a URL parameter
        var sessionId = new URLSearchParams(window.location.search).get('session_id');
        if (sessionId) {
            var stripe = Stripe('<?php echo $_ENV['STRIPE_PUBLISHABLE_KEY']; ?>');
            stripe.redirectToCheckout({ sessionId: sessionId })
            .then(function (result) {
                // If `redirectToCheckout` fails due to a browser or network
                // error, display the localized error message to your customer
                if (result.error) {
                    alert(result.error.message);
                }
            });
        } else {
            document.write('No session ID provided.');
        }
    </script>
</body>
</html>
