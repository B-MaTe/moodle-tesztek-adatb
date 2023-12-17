<?php

use controller\AuthController;
use util\pageable\Page;

AuthController::returnHomeIfLoggedOut();

$page = $page ?? new Page();

?>

<div class="card-container mb-5">
    <?php
    foreach ($page->getItems() as $item) {
        ?>
        <div class="card">
            <form action="edit-question" method="post" class="text-center w-100">
                <input type="text" name="id" value="<?php echo $item->getId(); ?>" hidden>
                <input type="text" name="page" value="<?php echo $page->getCurrentPage(); ?>" hidden>
                <input type="text" name="pageSize" value="<?php echo $page->getPageSize(); ?>" hidden>
                <input id="question-title" class="no-edit text-center" type="text" name="text" value="<?php echo $item->getText(); ?>" readonly required>
                <button id="edit-title-button" class="hidden btn btn-success w-50 mx-auto m-2" type="submit">Cím mentése</button>
            </form>
            <button id="toggle-title-button" class="btn btn-primary w-50 mx-auto m-2" onclick="toggleTitleEdit()">Cím szerkesztése</button>
            <a href="delete-question?id=<?php echo $item->getId(); ?>" class="card-link my-2">Kérdés törlése</a>

            <div class="created">
                <p>Készítette: <?php echo $item->getCreatedByEmail(); ?></p>
                <p><?php echo $item->getCreated_at()->format('Y-m-d'); ?></p>
            </div>
        </div>
        <?php
    }
    ?>
</div>
<nav class="d-flex justify-content-center mt-5">
    <ul class="pagination">
        <li class="page-item"><a class="page-link" href="<?php echo $page->getPreviousPageParams('questions'); ?>">Előző</a></li>
        <?php
        for ($i = 0; $i < $page->getTotalPages(); $i++) {
            echo '<li class="page-item"><a class="page-link" href="' . $page->getParams('questions', $page->getPageSize(), $i) . '">' . $i+1 .'</a></li>';
        }
        ?>
        <li class="page-item"><a class="page-link" href="<?php echo $page->getNextPageParams('questions'); ?>">Következő</a></li>
    </ul>
</nav>
<script>
    const title = document.getElementById('question-title');
    const editTitleButton = document.getElementById('edit-title-button');
    const toggleTitleButton = document.getElementById('toggle-title-button');
    let edit = false;
    function toggleTitleEdit() {
        if (edit) {
            title.setAttribute('readonly', 'readonly');
            title.classList.add('no-edit');
            editTitleButton.classList.add('hidden');
            toggleTitleButton.classList.remove('hidden');
        } else {
            title.removeAttribute('readonly');
            title.classList.remove('no-edit');
            toggleTitleButton.classList.add('hidden');
            editTitleButton.classList.remove('hidden');
        }

        edit = !edit;
    }
</script>



