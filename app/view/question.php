<?php
?>


<form action="add-question" method="POST">
    <div id="questionsContainer1">
        <div id="qc-1">
            <hr />
            <div class="row m-3">
                <label class="px-2 my-auto col-2" for="q-1">Kérdés :</label>
                <input class="col-6 px-2 w-50 my-3" type="text" id="q-1" name="q-1" required>
            </div>
            <div class="row m-3">
                <label class="px-2 my-auto col-2" for="p-">Kérdés  pontszáma:</label>
                <input class="col-6 px-2 w-50 my-3" type="number" max="10000" id="p-1" name="p-1" required>
            </div>
            <button type="button" class="btn btn-secondary" id="add-answer-1"  onclick="addAnswer(1)"><i class="bi bi-plus"></i> Válasz hozzáadása</button>
            <div id="answersContainer1" class="answer-container"></div>
            <hr />
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-12 text-center">
            <button type="submit" id="submit" class="btn btn-success"><i class="bi bi-download"></i> Kérdés hozzáadása</button>
        </div>
    </div>
</form>

<script src="app/static/js/question.js"></script>
