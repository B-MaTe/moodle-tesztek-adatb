<?php
    use controller\AuthController;
    use controller\UserController;
    use util\pageable\Page;

    AuthController::returnHomeIfLoggedOut();

    $page = $page ?? new Page();

    ?>

<div class="card-container mb-5">
    <?php
    foreach ($page->getItems() as $item) {
        ?>
        <div class="card">
            <form action="edit-test-name" method="post" class="text-center w-100">
                <input type="text" name="id" value="<?php echo $item->getId(); ?>" hidden>
                <input type="text" name="page" value="<?php echo $page->getCurrentPage(); ?>" hidden>
                <input type="text" name="pageSize" value="<?php echo $page->getPageSize(); ?>" hidden>
                <input id="test-title" class="no-edit text-center" type="text" name="name" value="<?php echo $item->getName(); ?>" readonly>
                <button id="edit-title-button" class="hidden btn btn-success w-50 mx-auto m-2" type="submit">Cím mentése</button>
            </form>
                <button id="toggle-title-button" class="btn btn-primary w-50 mx-auto m-2" onclick="toggleTitleEdit()">Cím szerkesztése</button>
            <a href="test?id=<?php echo $item->getId(); ?>" class="card-link my-2">Teszt kitöltése</a>
            <?php
            if (UserController::adminOrTeacher()) {
                ?>
                <a href="edit-test?id=<?php echo $item->getId(); ?>" class="card-link my-2">Teszt módosítása</a>
                <a href="delete-test?id=<?php echo $item->getId(); ?>" class="card-link my-2">Teszt törlése</a>
            <?php
            }
            ?>


            <div class="completions">
                <div class="py-5">
                    <?php
                    if (count($item->getCompletions()) == 0) {
                        ?>
                        <div class="col-12">
                            <p class="col-12 fs-5 fw-bold text-center">Még nem töltötte ki ezt a tesztet.</p>
                        </div>
                        <?php
                    } else {
                        $bestCompletion = $item->getCompletions()[0];
                        ?>
                        <div class="border border-3 rounded <?php echo $bestCompletion ? 'border-success' : 'border-danger' ?>">
                            <div class="row px-1">
                                <p class="col-6 fs-5 fw-bold text-center">Legmagasabb elért pontszám</p>
                                <p class="col-6 fs-5 fw-bold text-center">Eredmény</p>
                            </div>
                            <div class="row px-1">
                                <p class="col-6 fs-5 text-center"><?php echo $bestCompletion->getEarnedPoints(); ?></p>
                                <p class="col-6 fs-5 text-center"><?php echo $bestCompletion->isSuccessfulCompletion() ? 'Sikeres' : 'Sikertelen'; ?> teszt kitöltés.</p>
                            </div>
                            <div class="row mt-5">
                                <p class="col-12 fs-5 fw-bold text-center"><a href="test-statistics?id=<?php echo $item->getId(); ?>">Teszthez tartozó eredményeim</a></p>
                            </div>
                        </div>
                        <?php
                    }

                    ?>
                    <div class="col-12">
                        <p class="card-text"></p>
                    </div>
                </div>
            </div>

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
        <li class="page-item"><a class="page-link" href="<?php echo $page->getPreviousPageParams('tests'); ?>">Előző</a></li>
        <?php
            for ($i = 0; $i < $page->getTotalPages(); $i++) {
                echo '<li class="page-item"><a class="page-link" href="' . $page->getParams('tests', $page->getPageSize(), $i) . '">' . $i+1 .'</a></li>';
            }
        ?>
        <li class="page-item"><a class="page-link" href="<?php echo $page->getNextPageParams('tests'); ?>">Következő</a></li>
    </ul>
</nav>

<script>
    const title = document.getElementById('test-title');
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



