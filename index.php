

<?php
use controller\LayoutController;
use router\Router;

require_once 'app/util/NumUtils.php';
require_once 'app/controller/Controller.php';
require_once 'app/controller/DataController.php';
require_once 'app/controller/NotificationController.php';
require_once 'app/controller/TestCompletionController.php';
require_once 'app/controller/QuestionController.php';
require_once 'app/controller/AnswerController.php';
require_once 'app/controller/LayoutController.php';
require_once 'app/controller/UserController.php';
require_once "app/config/db.php";
require_once "app/config/config.php";
require_once "app/router/Router.php";
require_once 'app/util/pageable/Pageable.php';
require_once 'app/util/pageable/PageableBuilder.php';
require_once 'app/util/pageable/Page.php';

session_start();

$router = new Router(ROUTES);
$layoutController = new LayoutController();

?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <title>Moodle Teszt</title>
    <link rel="icon" href="app/static/img/favicon.png" type="image/x-icon">
    <link rel="shortcut icon" href="app/static/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="app/static/css/style.css">
</head>
<body>
    <?php
        $layoutController->header();
        include('app/template/notification.php');

        echo '<div class="main">';
            $router->route($_GET, $_POST);
        echo '</div>';

        $layoutController->footer();
    ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>