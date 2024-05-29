$(document).ready(function() {
    var languageChangeHandler = function(event) {
        event.preventDefault(); // prevent the anchor link's default behavior
        var lang = $(this).data('lang') || $(this).closest('a').data('lang');
        console.log("Trying to change language to:", lang);

        if (lang) {
            // Store the selected language in local storage
            localStorage.setItem('selectedLang', lang);
            changeLanguage(lang);
        }
    };

    $('body').on('click', '.header-right-top ul li, .lang-select a', languageChangeHandler);

    function changeLanguage(lang) {
        var selectField = document.querySelector("select.goog-te-combo");
        if (selectField) {
            console.log("Google Translate select found.");
            selectField.value = lang;
            selectField.dispatchEvent(new MouseEvent('mousedown'));
            selectField.dispatchEvent(new Event('change'));
            selectField.dispatchEvent(new MouseEvent('mouseup'));
        } else {
            console.log("Google Translate select not found.");
        }
    }

    var selectField = document.querySelector("select.goog-te-combo");
    if (selectField) {
        console.log("Google Translate widget is loaded.");

        // Check for stored language on page load
        var storedLang = localStorage.getItem('selectedLang');
        if (storedLang) {
            changeLanguage(storedLang);
        }
    } else {
        console.log("Google Translate widget not yet loaded, setting up MutationObserver.");

        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes && mutation.addedNodes.length > 0) {
                    var selectField = document.querySelector("select.goog-te-combo");
                    if (selectField) {
                        console.log("Google Translate widget detected by MutationObserver.");
                        
                        // Check for stored language when the widget loads
                        var storedLang = localStorage.getItem('selectedLang');
                        if (storedLang) {
                            changeLanguage(storedLang);
                        }

                        observer.disconnect();
                    }
                }
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
});