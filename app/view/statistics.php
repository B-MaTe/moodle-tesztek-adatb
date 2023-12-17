<?php
$testsForSummary = $testsForSummary ?? [];
$fillSummary = $fillSummary ?? [];
$resultsSummary = $resultsSummary ?? [];
$userPerformanceSummary = $userPerformanceSummary ?? [];
$generalSummary = $generalSummary ?? [];
$summaryTestId = $summaryTestId ?? 0;
?>

<div class="card-container mb-5">
    <div class="card p-3 w-100">
        <div class="card-header fs-2">Teszt kitöltések összegzése</div>
        <div class="card-body">
            <form id="dropdown" action="statistics" method="get">
                <select id="summary" name="summary" class="form-select" aria-label="Default select example">
                    <option <?php  echo $summaryTestId == 0 ? 'selected' : ''?> value="0">Válasszon ki egy tesztet</option>
                    <?php
                        foreach ($testsForSummary as $test) {
                            echo '<option ' . ($summaryTestId == $test->getId() ? ' selected' : '') . ' value="' . $test->getId() . '">' . $test->getName() . '</option>';
                        }
                    ?>
                </select>
                <button hidden type="submit"></button>
            </form>

            <table class="table table-striped">
                <thead>
                <tr class="text-center">
                    <th scope="col">#</th>
                    <th scope="col">Felhasználó email</th>
                    <th scope="col">Felhasználó név</th>
                    <th scope="col">Kitöltés kezdete</th>
                    <th scope="col">Kitöltés hossza (óra:perc)</th>
                    <th scope="col">Elért pontszám</th>
                    <th scope="col">Minimum pontszám</th>
                    <th scope="col">Sikeres kitöltés</th>
                    <th scope="col">Tesztet létrehozta</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $count = 0;
                foreach ($fillSummary as $item) {
                    $count++;
                    ?>
                        <tr class="text-center align-middle">
                            <th scope="row"><?php echo $count;?></th>
                            <td><?php echo $item['user_email'];?></td>
                            <td><?php echo $item['user_name'];?></td>
                            <td><?php echo $item['test_started_at'];?></td>
                            <td><?php echo $item['time_difference'];?></td>
                            <td><?php echo $item['test_earned_points'];?></td>
                            <td><?php echo $item['test_min_points'] ;?></td>
                            <td><?php echo $item['test_successful_completion'] ? '<i class="bi bi-check2"></i>' : '<i class="bi bi-x"></i>'; ?></td>
                            <td><?php echo $item['test_created_by'];?></td>
                        </tr>
                    <?php
                }

                if ($count == 0) {
                    echo '<tr class="text-center"><td colspan="9">Nincs adat.</td></tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card p-3 w-100">
        <div class="card-header fs-2">Teszt eredmények összegzése</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                <tr class="text-center">
                    <th scope="col">#</th>
                    <th scope="col">Teszt név</th>
                    <th scope="col">Kitöltések száma</th>
                    <th scope="col">Átlagos elért pontszám</th>
                    <th scope="col">Minimum elért pontszám</th>
                    <th scope="col">Maximum elért pontszám</th>
                    <th scope="col">Átlagos kitöltés idő (óra:perc)</th>
                    <th scope="col">Sikeres kitöltés arány (%)</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $count = 0;
                foreach ($resultsSummary as $item) {
                    $count++;
                    ?>
                    <tr class="text-center align-middle">
                        <th scope="row"><?php echo $count;?></th>
                        <td><?php echo $item['test_name'];?></td>
                        <td><?php echo $item['test_count'];?></td>
                        <td><?php echo $item['avg_points'];?></td>
                        <td><?php echo $item['min_points'];?></td>
                        <td><?php echo $item['max_points'];?></td>
                        <td><?php echo $item['average_time_difference'] ;?></td>
                        <td><?php echo $item['avg_successful_completion_percentage']; ?></td>
                    </tr>
                    <?php
                }

                if ($count == 0) {
                    echo '<tr class="text-center"><td colspan="8">Nincs adat.</td></tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card p-3 w-100">
        <div class="card-header fs-2">Felhasználók teljesítményének összegzése</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                <tr class="text-center">
                    <th scope="col">#</th>
                    <th scope="col">Felhasználó email</th>
                    <th scope="col">Felhasználó név</th>
                    <th scope="col">Kitöltötések száma</th>
                    <th scope="col">Különböző kitöltött tesztek száma</th>
                    <th scope="col">Átlagos kitöltés idő (óra:perc)</th>
                    <th scope="col">Sikeres kitöltés arány (%)</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $count = 0;
                foreach ($userPerformanceSummary as $item) {
                    $count++;
                    ?>
                    <tr class="text-center align-middle">
                        <th scope="row"><?php echo $count;?></th>
                        <td><?php echo $item['user_email'];?></td>
                        <td><?php echo $item['user_name'];?></td>
                        <td><?php echo $item['test_count'];?></td>
                        <td><?php echo $item['completions_count'];?></td>
                        <td><?php echo $item['average_time_difference'];?></td>
                        <td><?php echo $item['avg_successful_completion_percentage'] ;?></td>
                    </tr>
                    <?php
                }

                if ($count == 0) {
                    echo '<tr class="text-center"><td colspan="7">Nincs adat.</td></tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card p-3 w-100">
        <div class="card-header fs-2">Általános statisztikák</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                <tr class="text-center">
                    <th scope="col">#</th>
                    <th scope="col">Felhasználók száma</th>
                    <th scope="col">Tesztek száma</th>
                    <th scope="col">Teszt kitöltések száma</th>
                    <th scope="col">Kérdések száma</th>
                    <th scope="col">Válaszok száma</th>
                </tr>
                </thead>
                <tbody>
                <?php
                ?>
                <tr class="text-center align-middle">
                    <th scope="row">1</th>
                    <td><?php echo $generalSummary['user_count'];?></td>
                    <td><?php echo $generalSummary['test_count'];?></td>
                    <td><?php echo $generalSummary['test_completion_count'];?></td>
                    <td><?php echo $generalSummary['question_count'];?></td>
                    <td><?php echo $generalSummary['answer_count'];?></td>
                </tr>

                <?php
                if (count($generalSummary) == 0) {
                    echo '<tr class="text-center"><td colspan="6">Nincs adat.</td></tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.getElementById('summary').addEventListener('change', function() {
        document.getElementById('dropdown').submit();
    });
</script>