<?php

use controller\FormController;

if ($test->getId() > 0) {
    // existing test
    ?>
    <div class="row col-12 test">
        <h4 class="fw-bold text-center m-5"><?php echo $test->getName(); ?></h4>
        <form action="" method="post">
            <?php

            foreach ($test->getQuestions() as $question) {
                ?>
                <fieldset>
                <div class="d-flex question justify-content-center flex-column mb-3 p-4 rounded">
                    <p class="fw-bold fs-5"><?php echo $question->getText(); ?></p>

                    <?php

                    $questionId = $question->getId();

                    foreach ($question->getRandomizedAnswers() as $answer) {
                        $answerId = $answer->getId();
                    ?>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="<?php echo $questionId; ?>" id="<?php echo $answerId; ?>" value="<?php echo $answerId; ?>" />
                        <label class="form-check-label" for="<?php echo $answerId; ?>">
                            <?php echo $answer->getText(); ?>
                        </label>
                    </div>
                        <?php
                    }
                        ?>
                </div>
                <div class="pb-3">
                    <button type="button" onclick="resetGroup(<?php echo $questionId; ?>)" class="btn btn-danger">Alaphelyzet</button>
                </div>
                </fieldset>
            <?php
            }
            ?>
            <div class="text-end py-3">
                <button type="submit" class="btn btn-success">Beküldés</button>
            </div>
        </form>

    </div>
<?php
} else {
?>
    <div class="row col-12 test">
        <form action="add-test" method="post" class="d-flex justify-content-center flex-column flex-space-between">
            <div class="row mb-2">
                <div class="col-12">
                    <h4>Teszt Címe</h4>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-9">
                    <input type="text" required id="title" class="form-control" name="title" placeholder="Teszt címe">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12">
                    <h4>Sikeres teszt minimum pontszáma:</h4>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-9">
                    <input type="number" required id="min_points" class="form-control" name="min_points" placeholder="Teszt minimum pontszáma">
                    <?php FormController::showFieldError('min_points'); ?>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12">
                    <h4>Kérdések hozzáaádsa</h4>
                </div>
            </div>
            <div id="questionsContainer"></div>
            <div class="col-3 my-5">
                <button type="button" class="btn btn-secondary" onclick="addQuestion()"><i class="bi bi-plus"></i> Új kérdés hozzáadása</button>
            </div>
            <div class="row mt-5">
                <div class="col-12 text-center">
                    <button type="submit" id="submit" disabled class="btn btn-success"><i class="bi bi-download"></i> Teszt hozzáadása</button>
                </div>
            </div>
        </form>
    </div>
<?php
}

?>

<script src="app/static/js/test.js"></script>


