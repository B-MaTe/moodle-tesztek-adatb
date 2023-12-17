<?php

use controller\FormController;
use controller\QuestionController;

if ($test->getId() > 0) {
    // existing test
    ?>
    <div class="row col-12 test">
        <h4 class="fw-bold text-center m-5"><?php echo $test->getName(); ?></h4>
        <form action="fill-test" method="post">
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
                        <input class="form-check-input" type="radio" name="<?php echo $questionId; ?>" id="<?php echo $answerId; ?>" value="<?php echo $answerId; ?>" required />
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
            <input type="text" hidden aria-hidden="true" name="id" value="<?php echo $test->getId(); ?>">
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
                    <input type="text" required id="title" class="form-control" name="title">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12">
                    <h4>Sikeres teszt minimum pontszáma:</h4>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-9">
                    <input max="10000" type="number" required id="min_points" class="form-control" name="min_points">
                    <?php FormController::showFieldError('min_points'); ?>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12">
                    <h4>Kérdések hozzáaádsa</h4>
                </div>
            </div>
            <div id="questionsContainer"></div>
            <div class="row">
                <div class="col-3 my-5">
                    <button type="button" class="btn btn-secondary" onclick="addQuestion()"><i class="bi bi-plus"></i> Új kérdés hozzáadása</button>
                </div>
                <div class="col-3 my-5">
                    <button type="button" class="btn btn-secondary" id="existingQuestionsButton" onclick="toggleExistingQuestions()"><i class="bi bi-plus"></i> Létező kérdések megnyitása</button>
                </div>
                <div class="col-3 my-5">
                    <input class="form-control hidden" type="text" id="filterInput" placeholder="Kérdés keresése">
                </div>
            </div>
            <div class="hidden" id="existingQuestionsContainer">
                <?php
                $existingQuestions = QuestionController::getPageForTest(PHP_INT_MAX);

                foreach ($existingQuestions->getItems() as $question) {
                    ?>
                    <div>
                        <div class="row m-1">
                            <div class="col-3">
                                <p class="col-6 w-50 my-3"><b>Kérdés:</b></p>
                            </div>
                            <div class="col-3">
                                <p class="col-6 w-50 my-3"><b>Kérdés pontszáma:</b></p>
                            </div>
                            <div class="col-3">
                                <p class="col-6 w-50 my-3"><b>Válaszok:</b></p>
                            </div>
                            <div class="col-3">
                                <label for="existing-questions[]">
                                    <p class="col-6 w-50 my-3"><b>Hozzáadás a teszhez:</b></p>
                                </label>
                            </div>
                        </div>
                        <div class="row m-1">
                            <div class="col-3">
                                <p class="col-6 w-50 my-3 searchable"><?php echo $question->getText(); ?></p>
                            </div>
                            <div class="col-3">
                                <p class="col-6 w-50 my-3"><?php echo $question->getPoint(); ?></p>
                            </div>
                            <div class="col-3">
                                <ul>
                                    <?php
                                    foreach ($question->getAnswers() as $answer) {
                                        if ($answer != null) {
                                            ?>
                                            <li><p class=my-3"><?php echo $answer->getText() . ($answer->isCorrect() ? ' <b class="bg-success">(Helyes)</b>' : '');?></p></li>
                                            <?php
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                            <div class="col-3">
                                <label for="existing-questions[]">
                                    <input class="form-check-input" type="checkbox" name="existing-questions[]" value="eq-<?php echo $question->getId(); ?>" />
                                </label>
                            </div>
                        </div>
                        <hr />
                    </div>
                    <?php
                }
                ?>
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

<script src="app/static/js/question.js"></script>
<script src="app/static/js/test.js"></script>


