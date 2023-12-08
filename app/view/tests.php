<?php
    use controller\UserController;
    $loggedIn = UserController::userLoggedIn();

    if (!$loggedIn) {
        header('Location: logout');
    }
?>

<div class="card-container mb-5">
    <?php
    foreach ($page->getItems() as $item) {
        ?>
        <div class="card">
            <h5 class="card-title"><?php echo $item->getName(); ?></h5>
            <a href="test?id=<?php echo $item->getId(); ?>" class="card-link">Teszt kitöltése</a>

            <div class="created">
                <p>Készítette: <?php echo $item->getCreated_by(); ?></p>
                <p><?php echo $item->getCreated_at()->format('Y-m-d'); ?></p>
            </div>
        </div>
    <?php
        }
    ?>
</div>
<nav class="d-flex justify-content-center mt-5">
    <ul class="pagination">
        <li class="page-item"><a class="page-link" href="<?php echo $page->getPreviousPageParams('tests'); ?>">Előző</a></li>
        <?php
            for ($i = 0; $i < $page->getTotalPages(); $i++) {
                echo '<li class="page-item"><a class="page-link" href="' . $page->getParams('tests', $page->getPageSize(), $i) . '">' . $i+1 .'</a></li>';
            }
        ?>
        <li class="page-item"><a class="page-link" href="<?php echo $page->getNextPageParams('tests'); ?>">Következő</a></li>
    </ul>
</nav>



