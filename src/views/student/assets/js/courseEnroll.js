// Function to show toast message
function showToastMessage(message) {
    document.getElementById("toast-text").innerText = message;
    let toast = new bootstrap.Toast(document.getElementById("toast-message"));
    toast.show();
}

function loadPaypalSdk(clientId) {
    return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = `https://www.paypal.com/sdk/js?client-id=${clientId}`;
        script.onload = () => resolve();
        script.onerror = () => reject(new Error('Failed to load PayPal SDK'));
        document.body.appendChild(script);
    });
}

async function initializePaypalButton(clientId, price, classCardElement) {
    await loadPaypalSdk(clientId);

    paypal.Buttons({
        createOrder: function (data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: price
                    }
                }]
            });
        },
        onApprove: function (data, actions) {
            return actions.order.capture().then(function (details) {
                console.log("Order captured:", details);

                // Extract necessary details for enrollment
                const courseId = classCardElement.dataset.classId;
                const studentId = document.querySelector('.containern').dataset.studentId;
                const amountPaid = details.purchase_units[0].amount.value; // This is from the PayPal transaction details

                // AJAX call to save payment details to your database
                $.ajax({
                    url: '../../controllers/groupClassController.php',
                    type: 'POST',
                    data: {
                        action: 'enroll_student',
                        student_id: studentId,
                        class_id: courseId,
                        date_enrolled: new Date().toISOString().slice(0, 19).replace('T', ' '),
                        amount_paid: amountPaid,
                        status: 'Paid'
                    },
                    success: function (response) {
                        console.log("Server Response:", response);
                    
                        const jsonResponse = JSON.parse(response); // Parse the JSON response from the server
                        if (jsonResponse.status === 'success') {
                            // Close the courseDetailModal
                            const courseModal = new bootstrap.Modal(document.getElementById('courseDetailModal'));
                            courseModal.hide();
                    
                            // Show success status in the transactionStatusModal
                            document.getElementById("transactionStatusModalBody").innerText = jsonResponse.message;
                            const statusModal = new bootstrap.Modal(document.getElementById('transactionStatusModal'));
                            statusModal.show();
                        } else {
                            // Handle the error here
                            console.error(jsonResponse.message);
                    
                            // Close the courseDetailModal
                            const courseModal = new bootstrap.Modal(document.getElementById('courseDetailModal'));
                            courseModal.hide();
                    
                            // Show error status in the transactionStatusModal
                            document.getElementById("transactionStatusModalBody").innerText = jsonResponse.message;
                            const statusModal = new bootstrap.Modal(document.getElementById('transactionStatusModal'));
                            statusModal.show();
                        }
                    },
                });
            });
        },
        onError: function (error) {
            console.error("Payment error:", error);
            alert("An error occurred during the payment process. Please try again or contact support.");
        }
    }).render('#paypal-course-button-container');
}

document.addEventListener("DOMContentLoaded", function() {
    const enrollButtons = document.querySelectorAll('.enroll-btn');

    enrollButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const classCard = e.target.closest('.group-class-card');
            const price = classCard.querySelector('.price-enroll p').innerText.split('$')[1];
            const clientId = PAYPAL_CLIENT_ID;

            initializePaypalButton(clientId, price, classCard);

            // Optionally, you can open the modal here:
            const modal = new bootstrap.Modal(document.getElementById('courseDetailModal'));
            modal.show();
        });
    });
});
