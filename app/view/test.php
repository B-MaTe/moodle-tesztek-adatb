<?php

use controller\AuthController;
use controller\NotificationController;
use controller\UserController;
use enum\NotificationType;

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
    AuthController::checkAdminPrivilege();




}

?>

<script>
    function resetGroup(groupName) {
        document.querySelectorAll('input[type="radio"][name="' + groupName + '"]').forEach(function (radio) {
            radio.checked = false;
        });
    }
</script>


