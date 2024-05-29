function getURLParameter(name) {
    return decodeURIComponent((new RegExp("[?|&]" + name + "=" + "([^&;]+?)(&|#|;|$)").exec(location.search) || [, ""])[1].replace(/\+/g, "%20")) || null;
}

document.addEventListener("DOMContentLoaded", async function () {
    var continueButton = document.getElementById("continue-button");

    if (continueButton) {
        setupContinueButton();
    } else {
        await setupTutorAvailability();
    }
});

function setupContinueButton() {
    var continueButton = document.getElementById("continue-button");
    continueButton.addEventListener("click", function (e) {
        e.preventDefault();

        var tutorUsername = getURLParameter("username");
        var tutorLanguages = getURLParameter("languages");
        window.location.href = "src/views/student/availability" + "?username=" + tutorUsername + "&languages=" + tutorLanguages;
    });
}