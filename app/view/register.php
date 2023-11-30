<?php
use controller\FormController;
require_once 'app/controller/FormController.php';
?>

<div class="container-fluid w-auto">
    <div class="rounded d-flex justify-content-center">
        <div class="col-md-4 col-sm-12 shadow-lg p-3 bg-light">
            <div class="text-center">
                <h3 class="text-primary">Regisztráció</h3>
            </div>
            <div>
                <form action="registerUser" method="post">
                    <div class="input-group mb-3">
                                <span class="input-group-text bg-primary"><i
                                        class="bi bi-envelope text-white"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="E-mail" required>
                    </div>
                    <?php
                        FormController::showFieldError('email');
                    ?>
                    <div class="input-group mb-3">
                                <span class="input-group-text bg-primary"><i
                                            class="bi bi-person text-white"></i></span>
                        <input type="text" name="name" class="form-control" placeholder="Teljes név" required>
                    </div>
                    <?php
                        FormController::showFieldError('name');
                    ?>
                    <div class="input-group mb-3">
                                <span class="input-group-text bg-primary"><i
                                        class="bi bi-key-fill text-white"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Jelszó" required>
                    </div>
                    <?php
                        FormController::showFieldError('password');
                    ?>
                    <div class="input-group mb-3">
                                <span class="input-group-text bg-primary"><i
                                            class="bi bi-key-fill text-white"></i></span>
                        <input type="password" name="passwordAgain" class="form-control" placeholder="Jelszó megerősítés" required>
                    </div>
                    <?php
                        FormController::showFieldError('passwordAgain');
                    ?>
                    <div class="d-grid col-12 mx-auto">
                        <button class="btn btn-primary" type="submit">Regisztrálás</button>
                    </div>
                    <p class="text-center mt-3">Van már fiókod?
                        <span class="text-primary"><a href="login">Bejelentkezés</a></span>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>