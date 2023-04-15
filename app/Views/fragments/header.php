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
                    <li><a class="navbar-item" href="/login">Login</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<script>
    const navbarBurger = document.querySelector('.navbar-burger');
    const navbarMenu = document.querySelector('.navbar-menu');
    
    navbarBurger.addEventListener('click', function() {
        navbarBurger.classList.toggle('is-active');
        navbarMenu.classList.toggle('is-active');
    });
</script>