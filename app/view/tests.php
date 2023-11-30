<?php
    use controller\UserController;
    $loggedIn = UserController::userLoggedIn();

    if (!$loggedIn) {
        header('Location: logout');
    }

    print_r($data);
?>


