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

let currentPlanName = "";
let currentPlanPrice = 0;
let isAnnualFeePaid = false;

let planModal = new bootstrap.Modal(document.getElementById("planDetailModal"));

function showPlanDetails(planName, totalPrice) {
    let modalBodyText = "You have selected the " + planName + " plan.";

    // Only mention the annual access fee if it's not yet paid.
    if (!isAnnualFeePaid) {
        modalBodyText += " Including an annual access fee of $30.";
    }
    
    modalBodyText += " Total price is $" + totalPrice + ".";

    if (planName === "Casual Learner") {
        modalBodyText += "<br><input id='casualHours' type='number' min='1' max='4' value='1'>";
    }

    document.getElementById("planDetailModalBody").innerHTML = modalBodyText;
    currentPlanName = planName;
    currentPlanPrice = isAnnualFeePaid ? totalPrice : totalPrice - 30;
}

async function createPaypalButton() {
    await loadPaypalSdk(PAYPAL_CLIENT_ID);

    if (typeof paypal === 'undefined') {
        setTimeout(createPaypalButton, 500);
        return;
    }

    let oldPaypalButton = document.getElementById('paypal-button-container');
    while (oldPaypalButton.firstChild) {
        oldPaypalButton.removeChild(oldPaypalButton.firstChild);
    }

    paypal.Buttons({
        createOrder: function(data, actions) {
            let amount = currentPlanPrice;

            if (currentPlanName === "Casual Learner") {
                selectedHours = document.getElementById("casualHours").value;
                amount *= selectedHours;
            }

            if (!isAnnualFeePaid) {
                amount += 30;
            }

            return actions.order.create({
                purchase_units: [{
                    description: currentPlanName + " + Annual Access Fee",
                    amount: {
                        value: amount,
                    },
                }],
            });
        },

        onApprove: function(data, actions) {
            planModal.hide();

            return actions.order.capture().then(function(details) {
                showToastMessage("Transaction completed by " + details.payer.name.given_name);

                let selectedHours = 1;
                if (currentPlanName === "Casual Learner") {
                    selectedHours = document.getElementById("casualHours").value;
                }

                // Send request to PlanController
                return fetch("../../controllers/PlanController.php", {
                    method: "post",
                    headers: {
                        "content-type": "application/json",
                    },
                    body: JSON.stringify({
                        orderID: data.orderID,
                        planName: currentPlanName,
                        planPrice: currentPlanPrice * selectedHours,
                        selectedHours: selectedHours,
                    }),
                })
                .then(function(res) {
                    if (!res.ok) {
                        throw new Error('Network response was not ok.');
                    } else {
                        if (!isAnnualFeePaid) {
                            let annualFeePaymentData = {
                                username: USER_NAME,
                                orderID: data.orderID,
                                planName: "Annual Access Fee",
                                userPaid: 30,
                            };

                            // After successful response from PlanController, send request to AnnualFeeController
                            return fetch("../../controllers/annualFeeController.php", {
                                method: "post",
                                headers: {
                                    "content-type": "application/json",
                                },
                                body: JSON.stringify(annualFeePaymentData),
                            });
                        }
                    }
                })
                .then(function(res) {
                    if (res && !res.ok) {
                        throw new Error('Network response was not ok.');
                    }
                })
                .catch(function(error) {
                    console.log('There has been a problem with your fetch operation: ', error.message);
                });
            });
        }

    }).render("#paypal-button-container");
}

window.onload = function() {
    let selectButtons = document.querySelectorAll(".price-btn a");
    let username = document.body.dataset.username;

    selectButtons.forEach(function(button) {
        button.addEventListener("click", function(event) {
            event.preventDefault();

            fetch("../../controllers/PlanController.php", {
                method: "post",
                headers: {
                    "content-type": "application/json",
                },
                body: JSON.stringify({
                    action: 'checkActiveSubscription',
                    username: username,
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.hasActiveSubscription) {
                    showToastMessage("You currently have an active subscription. Please cancel your existing subscription before purchasing a new one.");
                } else {
                    fetch("../../controllers/annualFeeController.php?action=status", {
                        method: "post",
                        headers: {
                            "content-type": "application/json",
                        },
                        body: JSON.stringify({
                            username: username,
                        }),
                    })
                    .then(res => {
                        if (!res.ok) {
                            throw new Error("HTTP error " + res.status);
                        }
                        return res.json();
                    })
                    .then(annualData => {
                        isAnnualFeePaid = annualData.annual_fee_paid === 1;

                        let annualFee = isAnnualFeePaid ? 0 : 30;
                        
                        let planName = button.parentElement.parentElement.querySelector("h3").innerText;
                        let planPrice = button.parentElement.parentElement.querySelector("h4").innerText.slice(1);
                        let totalPrice = parseInt(planPrice) + annualFee;

                        showPlanDetails(planName, totalPrice);
                        planModal.show();
                        createPaypalButton();
                    });
                }
            });
        });
    });

    let closeButtons = document.querySelectorAll('[data-dismiss="modal"]');
    closeButtons.forEach(function(button) {
        button.addEventListener("click", function(event) {
            planModal.hide();
        });
    });
};
