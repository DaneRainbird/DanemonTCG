<?php 
    $activePage = $activePage ?? ''; // Ensure that $activePage is defined regardless of whether it was passed in
    $isAdmin = session()->get('isAdmin');
?>

<nav class="navbar" id="navbar">
    <div class="container">
        <div class="navbar-brand">
            <a class="navbar-item navbar-brand-text" href="/">
                <img src="/assets/img/logo.png" alt="Logo">
                Danemon TCG
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
                    <?php if ($isAdmin === 'true') echo '<li><a class="navbar-item admin-nav href="#">ADMIN MODE</a></li>' ?>
                    <li><a class="navbar-item <?php if ($activePage === 'about') echo 'active' ?>" href="/about">About</a></li>
                    <li><a class="navbar-item <?php if ($activePage === 'cards') echo 'active' ?>" href="/cards">Cards</a></li>
                    <li><a class="navbar-item <?php if ($activePage === 'sets') echo 'active' ?>" href="/sets">Sets</a></li>
                    <?php if (session()->get('username')): ?>
                        <li><a class="navbar-item  <?php if ($activePage === 'profile') echo 'active' ?>" href="/users/profile">Profile</a></li>
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
<?php elseif (session()->get('error')): ?>
    <div class="notification is-danger" id="notification-box">
        <?= session()->get('error') ?> <span class="notification-close" id="notification-close">X</span>
    </div>
<?php endif; ?>

<script>
    const navbarBurger = document.querySelector('.navbar-burger');
    const navbarMenu = document.querySelector('.navbar-menu');
    
    navbarBurger.addEventListener('click', function() {
        navbarBurger.classList.toggle('is-active');
        navbarMenu.classList.toggle('is-active');
    });
</script>