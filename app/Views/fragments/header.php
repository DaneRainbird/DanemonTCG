<nav class="navbar">
    <div class="container">
        <div class="navbar-brand">
            <a class="navbar-item navbar-brand-text" href="/">
                <img src="/assets/img/logo.png" alt="Logo">
                Datenmon TCG
            </a>
        </div>
        <div class="navbar-burger burger" data-target="navbarMenu">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div id="navbarMenu" class="navbar-menu">
            <div class="navbar-links-container">
                <ul class="navbar-links">
                    <li><a class="navbar-item" href="/about">About</a></li>
                    <li><a class="navbar-item" href="/cards">Cards</a></li>
                    <li><a class="navbar-item" href="/sets">Sets</a></li>
                    <?php if (session()->get('username')): ?>
                        <li><a class="navbar-item" href="/users/profile">Profile</a></li>
                        <li><a class="navbar-item" href="/users/logout">Logout</a></li>
                    <?php else: ?>
                        <li><a class="navbar-item" href="/login">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</nav>

<?php if (session()->get('success')): ?>
    <div class="notification is-success" id="notification-box">
        <?= session()->get('success') ?> <span class="notification-close" id="notification-close">X</span>
    </div>
<?php endif; ?>

<script>
    const navbarBurger = document.querySelector('.navbar-burger');
    const navbarMenu = document.querySelector('.navbar-menu');
    
    navbarBurger.addEventListener('click', function() {
        navbarBurger.classList.toggle('is-active');
        navbarMenu.classList.toggle('is-active');
    });

    const notificationBox = document.querySelector('#notification-box');
    const notificationClose = document.querySelector('#notification-close');

    notificationClose.addEventListener('click', function() {
        notificationBox.style.transition = 'opacity 0.5s ease-in-out';
        notificationBox.style.opacity = '0';
        setTimeout(function() {
            notificationBox.parentNode.removeChild(notificationBox);
        }, 500);
    });
</script>