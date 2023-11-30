<?php
    use controller\UserController;
    $loggedIn = UserController::userLoggedIn();
?>
<div class="title mx-auto row text-center mt-5">
    <h1 class="display-4 col-12">Üdvözlünk, kedves <?php echo $loggedIn ? UserController::getLoggedInUser()->getName() : 'vendég felhasználó' ?>!</h1>
</div>
<div class="row text-center">
    <?php
    if ($loggedIn) {
        ?>
        <!-- List tests -->
    <?php
    } else {
        ?>
        <p>A tesztek kitöltéséhez <a href="login">jelentkezzen be!</a></p>
    <?php
    }
    ?>
</div>
