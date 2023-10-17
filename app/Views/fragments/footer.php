<footer class="footer">
    <div class="container">
        <p>Copyright &copy; <a href="https://danerainbird.me/" target="_blank">Dane Rainbird</a> 2023</p>
        <p>Data by the <a href="https://pokemontcg.io" target="_blank">Pokémon TCG API</a></p>
    </div>
</footer>

<script>
// Event listener for clicks on the DOM 
document.addEventListener('click', function(event) {
    // Check to see if the user clicked on the notification close button, and if so, close the notification
    const notificationClose = event.target.closest('.notification-close');
    if (notificationClose) {
        const notificationBox = notificationClose.closest('.notification');
        notificationBox.style.transition = 'opacity 0.5s ease-in-out';
        notificationBox.style.opacity = '0';
        setTimeout(function() {
            notificationBox.parentNode.removeChild(notificationBox);
        }, 500);
    }

    // Check to see if the user clicked on the copy button, and if so, copy the id of the parent H2 element
    const copyButton = event.target.closest('.copy');
    if (copyButton) {
        const copyText = copyButton.parentNode.id;
        const location = copyButton.parentNode;
        copyToClipBoard(copyText, location);
    }
});


/**
 * Creates a notification box and inserts it below the navbar
 * @param {string} message The message to display in the notification box
 * @param {string} type The type of notification box to create (is-success, is-danger, etc.)
 */
function createNotification(message, type) {
    var navbar = document.getElementById("navbar");
    var notificationBox = document.createElement("div");
    notificationBox.classList.add("notification", type);
    notificationBox.setAttribute("id", "notification-box");
    notificationBox.innerHTML = `
        ${message}
        <span class="notification-close" id="notification-close">X</span>
    `;

    navbar.parentNode.insertBefore(notificationBox, navbar.nextSibling);

    // Scroll to the notification box 
    notificationBox.scrollIntoView();
}

/**
 * Copies the provided text to the user's clipboard using the Clipboard API if available, or a textarea element if not
 * @param {string} text The text to copy to the user's clipboard
 * @param {string} location The location of where to create the textarea element if the Clipboard API is not available (prevents scrolling)
 */
function copyToClipBoard(text, location) {
    // Create the link location by stripping the current page URL of any # values and appending the provided text
    const linkToCopy = window.location.href.replace(/#.*/, '') + '#' + text;

    // Set the current page URL to the link location without refreshing the page
    window.history.pushState("", "", linkToCopy);

    // Scroll to the link location
    window.location.hash = text;

    // Check if the browser supports the Clipboard API
    if (!navigator.clipboard) {
        // If not, create a textarea element and insert the text to copy into it under the provided location
        var textArea = document.createElement("textarea");
        textArea.value = linkToCopy;
        textArea.setAttribute("readonly", "");
        textArea.style.position = "absolute";
        textArea.style.left = "-9999px";
        location.appendChild(textArea);
        textArea.focus();
        textArea.select();

        // Attempt to copy the text
        try {
            document.execCommand("copy");
        } catch (err) {
            console.error("Unable to copy to clipboard due to the following error: ", err);
        }

        // Remove the textarea element
        location.removeChild(textArea);
        return;
    } else {
        // If the browser does support the Clipboard API, use it to copy the text
        navigator.clipboard.writeText(linkToCopy);
    }
}

</script>