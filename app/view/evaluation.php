<?php

use model\Test;
use model\TestCompletion;

$completion = $completion ?? new TestCompletion();
$test = $test ?? new Test();
$oldCompletions = $oldCompletions ?? [];

if ($completion->isSuccessfulCompletion()) {
    $bg = 'bg-success';
    $message = 'Gratulálunk!<br />Elérte a minimum pontszámot!';
} else {
    $bg = 'bg-danger';
    $message = 'Sajnos nem sikerült elérni a minimum pontszámot!';
}

$message .= '<br />Töltse ki újra a tesztet az alul található gombbal, majd hasonlítsa össze a <br /><a class="evaluation-link" href="#statistics">többi kitöltésével!</a>';

?>

<div class="card text-center evaluation">
    <div class="card-header">
        Teszt eredmény
    </div>
    <div class="card-body <?php echo $bg; ?>">
        <h2 class="my-5 card-title text-light"><?php echo $test->getName(); ?></h2>
        <div class="row my-5">
            <div class="evaluation-points col-3 text-light">Maximum pontszám: <?php echo $maxPoints; ?></div>
            <div class="evaluation-points col-3 text-light">Minimum pontszám: <?php echo $test->getMin_points(); ?></div>
            <div class="evaluation-points col-3 text-light">Elért Pontszám: <?php echo $completion->getEarnedPoints(); ?></div>
            <div class="evaluation-points col-3 text-light">Elért százalék: <?php echo percentage($completion->getEarnedPoints(), $maxPoints); ?> %</div>
        </div>
        <div class="row">
            <div class="evaluation-text text-light col-12"><?php echo $message; ?></div>
        </div>
        <a href="test?id=<?php echo $test->getId(); ?>" class="mb-5 btn btn-primary">Teszt kitöltése újra</a>
    </div>
    <div class="card-footer text-body-secondary d-flex flex-row justify-content-between">
        <div class="mx-5">Teszt létrehozva: <?php echo $test->sqlCreated_at(); ?></div>
        <div class="mx-5">A tesztet létrehozta: <?php echo $test->getCreatedByEmail(); ?></div>
    </div>
</div>

<div id="statistics">
    <table class="table table-striped">
        <thead>
        <tr class="text-center">
            <th scope="col">#</th>
            <th scope="col">Elért Pontszám</th>
            <th scope="col">Kitöltés ideje</th>
            <th scope="col">Sikeres kitöltés</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $count = 0;
        foreach ($oldCompletions as $oldCompletion) {
            $count++;

            ?>
            <tr class="text-center">
                <th scope="row"><?php echo $count;?></th>
                <td><?php echo $oldCompletion->getEarnedPoints();?></td>
                <td><?php echo $oldCompletion->sqlCreated_at();?></td>
                <td><?php echo $oldCompletion->isSuccessfulCompletion() ? '<i class="bi bi-check2"></i>' : '<i class="bi bi-x"></i>'; ?></td>
            </tr>
        <?php
        }

        if ($count == 0) {
            echo '<tr class="text-center"><td colspan="4">Nincs kitöltés.</td></tr>';
        }
        ?>
        </tbody>
    </table>

</div>
