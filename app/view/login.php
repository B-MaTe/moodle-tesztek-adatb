<?php
    use controller\FormController;
    require_once 'app/controller/FormController.php';
?>

<div class="flex w-50 mx-auto">
    <form action="loginUser" method="post">
        <h1 class="h3 mb-3 fw-normal">Bejelentkezés</h1>

        <div class="form-floating">
            <input type="email" name="email" class="form-control" id="floatingInput" placeholder="name@example.com">
            <label for="floatingInput">E-mail</label>
            <?php
                FormController::showFieldError('email');
            ?>
        </div>
        <div class="form-floating mt-3 mb-3">
            <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password">
            <label for="floatingPassword">Jelszó</label>
            <?php
            FormController::showFieldError('password');
            ?>
        </div>

        <div class="mb-3">
            <p>Nincs még fiókod? <a href="register">Regisztrálj!</a></p>
        </div>
        <button class="w-100 btn btn-lg btn-primary" type="submit">Bejelentkezés</button>
    </form>
</div>
