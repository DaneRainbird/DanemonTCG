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
});
</script>