let annualModalElement = document.getElementById("annualDetailModal");
let annualModal = new bootstrap.Modal(annualModalElement);

function loadPaypalSdk(clientId) {
    return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = `https://www.paypal.com/sdk/js?client-id=${clientId}`;
        script.onload = () => resolve();
        script.onerror = () => reject(new Error('Failed to load PayPal SDK'));
        document.body.appendChild(script);
    });
}

async function createAnnualPaypalButton(annualFee) {
    try {
        await loadPaypalSdk(PAYPAL_CLIENT_ID);
    } catch (error) {
        console.error('Error loading PayPal SDK:', error);
        return;
    }

    let oldPaypalButton = document.getElementById("annual-paypal-button-container");
    while (oldPaypalButton.firstChild) oldPaypalButton.removeChild(oldPaypalButton.firstChild);

    try {
        paypal.Buttons({
            createOrder: function (data, actions) {
                return actions.order.create({
                    purchase_units: [
                        {
                            description: "Annual Access Fee",
                            amount: {
                                value: annualFee,
                            },
                        },
                    ],
                });
            },

            onApprove: function (data, actions) {
                return actions.order.capture().then(function (details) {
                    return fetch("../../controllers/annualFeeController.php", {
                        method: "post",
                        headers: {
                            "content-type": "application/json",
                        },
                        body: JSON.stringify({
                            username: USER_NAME,
                            orderID: data.orderID,
                            planName: "Annual Access Fee",
                            userPaid: annualFee,
                        }),
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error("HTTP error " + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Server response:', data);
                        if (data.message === "Annual fee payment successful") {
                            alert("Transaction completed by " + details.payer.name.given_name);
                            annualModal.hide();
                        }
                    })
                    .catch(error => {
                        console.error('Fetch Error:', error);
                    });
                })
                .catch(error => {
                    console.error('Error capturing order:', error);
                });
            },
        })
        .render("#annual-paypal-button-container");
    } catch (error) {
        console.error("Error while creating PayPal button:", error);
    }
}

annualModalElement.addEventListener('shown.bs.modal', function () {
  createAnnualPaypalButton(30);
});

document.addEventListener("DOMContentLoaded", function (event) {
    fetch("../../controllers/annualFeeController.php?action=status", {
        method: "post",
        headers: {
            "content-type": "application/json",
        },
        body: JSON.stringify({
            username: USER_NAME,
        }),
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error("HTTP error " + response.status);
            }
            return response.json();
        })
        .then((data) => {
            let annualFeeStatusElement = document.getElementById("annualFeeStatus");
            let annualFeeActionElement = document.getElementById("annualFeeAction");
            let trialStatusElement = document.getElementById("trialStatus");
            //let planContainerElement = document.getElementById("planContainer");

            if (data.status === "inactive") {
                annualFeeStatusElement.innerText = "";
                annualFeeActionElement.innerText = "Pay Now";
                annualFeeActionElement.style.display = "block";
            } else {
                let expiryDate = new Date(data.expire_date);
                let formattedDate = expiryDate.toLocaleDateString();
                annualFeeStatusElement.innerText = `Active - Expires ${formattedDate}`;
                annualFeeActionElement.style.display = "none";
            }

            // Handle trial status
            if (data.classes === 1) {
                trialStatusElement.innerText = "Unused";
            } else {
                trialStatusElement.innerText = "Used";
            }

            // Handle plan container
            // if (data.annual_fee_paid === 1) {
            //     planContainerElement.classList.remove("disabled");
            // } else {
            //     planContainerElement.classList.add("disabled");
            // }
        })

        .catch((error) => {
            console.error("Fetch Error:", error);
        });
});