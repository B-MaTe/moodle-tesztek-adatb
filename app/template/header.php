<?php
    use controller\UserController;
    $loggedIn = UserController::userLoggedIn();
    $admin = UserController::admin();
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="home">Moodle Tesztek</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="home">Kezdőlap</a>
                </li>
                <?php
                if ($loggedIn) {
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="tests">Tesztek</a>
                    </li>
                <?php
                }

                if ($admin) {
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="test?id=0">Teszt létrehozása</a>
                    </li>
                <?php
                }
                ?>
            </ul>
            <form class="d-flex">
                <a class="nav-link"
                   title="<?php echo $loggedIn ? 'Kijelentkezés' : 'Bejelentkezés' ?>"
                   href="<?php echo $loggedIn ? 'logout' : 'login' ?>"><i class="bi bi-box-arrow-in-<?php echo $loggedIn ? 'left' : 'right' ?> h4"></i></a>
            </form>
        </div>
    </div>
</nav>