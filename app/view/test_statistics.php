<?php

use controller\UserController;

$completions = $completions ?? [];
?>

<div id="statistics">
    <table class="table table-striped">
        <thead>
        <tr class="text-center">
            <th scope="col">#</th>
            <th scope="col">Elért Pontszám</th>
            <th scope="col">Kitöltés ideje</th>
            <th scope="col">Sikeres kitöltés</th>
            <th scope="col">kérdések megtekintése</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $count = 0;
        foreach ($completions as $completion) {
            $count++;

            ?>
            <tr class="text-center">
                <th scope="row"><?php echo $count;?></th>
                <td><?php echo $completion->getEarnedPoints(); ?></td>
                <td><?php echo $completion->sqlCreated_at(); ?></td>
                <td><?php echo $completion->isSuccessfulCompletion() ? '<i class="bi bi-check2"></i>' : '<i class="bi bi-x"></i>'; ?></td>
                <td><a onclick="openModal(<?php echo $completion->getId(); ?>)" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modal"><i class="bi bi-arrows-angle-expand"></i></a></td>
            </tr>
            <?php
        }

        if ($count == 0) {
            echo '<tr class="text-center"><td colspan="5">Nincs kitöltés.</td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>


<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 75%;">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalLabel">Kérdések</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <?php
                foreach ($completions as $completion) {
                    echo '<div id="' . $completion->getId() . '" class="hidden">';
                    foreach ($completion->getQuestions() as $question) {
                        ?>
                            <div class="row">
                                <div class="col-12 fs-2 text-center p-3" style="font-style: italic; text-decoration: underline;">
                                    <?php echo $question->getText(); ?>
                                </div>
                            </div>

                            <table class="table table-striped">
                                <thead>
                                <tr class="text-center">
                                    <th scope="col">#</th>
                                    <?php
                                    if (UserController::adminOrTeacher()) {
                                        ?>
                                        <th scope="col">Helyes válasz</th>
                                    <?php } ?>
                                    <th scope="col">Válasz</th>
                                    <th scope="col">Kiválasztott</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $count = 0;
                                foreach ($question->getRandomizedAnswers() as $answer) {
                                    $count++;

                                    ?>
                                    <tr class="text-center">
                                        <th scope="row"><?php echo $count;?></th>
                                        <?php
                                        if (UserController::adminOrTeacher()) {
                                            ?>
                                        <td><?php echo ($answer->isCorrect() ? '<i class="bi bi-check2"></i>' : '<i class="bi bi-x"></i>'); ?></td>

                                            <?php
                                            }
                                        ?>
                                        <td><?php echo $answer->getText(); ?></td>
                                        <td><?php echo ($answer->getId() === $question->getSelectedAnswer()->getId() ? ($answer->isCorrect() ? '<i class="bi bi-check2-square"></i> helyes' : '<i class="bi bi-x-square"></i> helytelen' ) : ''); ?></td>
                                    </tr>
                                    <?php
                                }

                                if ($count == 0) {
                                    echo '<tr class="text-center"><td colspan="4">Nincsennek válaszok.</td></tr>';
                                }
                                ?>
                                </tbody>
                            </table>
                        <?php
                        echo '<hr />';
                    }
                    ?>
                <?php
                    echo '</div>';
                } ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bezárás</button>
            </div>
        </div>
    </div>
</div>

<script>
    let lastId = 0;
    const modal = document.getElementById('modal');
    modal.addEventListener('hide.bs.modal', () => setTimeout(() => closeModal(lastId), 500));

    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        lastId = id;
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        lastId = id;
    }
</script>
