function resetGroup(groupName) {
    document.querySelectorAll('input[type="radio"][name="' + groupName + '"]').forEach(function (radio) {
        radio.checked = false;
    });
}

let questionCount = 1;

function addQuestion() {
    const questionsContainer = document.getElementById('questionsContainer');
    const questionContainer = document.createElement('div');

    questionContainer.id = 'qc-' + questionCount;

    questionContainer.innerHTML =
        `${questionCount === 1 ? '<hr />' : ''}
        <div class="row m-3">
            <label class="px-2 my-auto col-2" for="q-${questionCount}">Kérdés ${questionCount}:</label>
            <input class="col-6 px-2 w-50 my-3" type="text" id="q-${questionCount}" name="q-${questionCount}" required>
        </div>
        <div class="row m-3">
            <label class="px-2 my-auto col-2" for="p-${questionCount}">Kérdés ${questionCount} pontszáma:</label>
            <input class="col-6 px-2 w-50 my-3" type="number" max="10000" id="p-${questionCount}" name="p-${questionCount}" required>
        </div>
        <button type="button" class="btn btn-secondary" id="add-answer-${questionCount}"  onclick="addAnswer(${questionCount})"><i class="bi bi-plus"></i> Válasz hozzáadása</button>
        <button type="button" class="btn btn-danger" onclick="deleteQuestion(${questionCount})"><i class="bi bi-trash"></i> Kérdés Törlése</button>
        <div id="answersContainer${questionCount}" class="answer-container"></div>
        <hr />`;

    questionsContainer.appendChild(questionContainer);
    document.getElementById('submit').disabled = false;
    questionCount++;
}

function toggleExistingQuestions() {
    const existingQuestionsContainer = document.getElementById('existingQuestionsContainer');
    const searchInput = document.getElementById('filterInput');
    existingQuestionsContainer.classList.toggle('hidden');
    searchInput.classList.toggle('hidden');

    const existingQuestionsButton = document.getElementById('existingQuestionsButton');
    existingQuestionsButton.innerHTML = existingQuestionsContainer.classList.contains('hidden') ? '<i class="bi bi-plus"></i> Létező kérdések megnyitása' : '<i class="bi bi-dash"></i> Létező kérdések elrejtése';
}

function addAnswer(questionNumber) {
    const answersContainer = document.getElementById(`answersContainer${questionNumber}`);

    if (answersContainer.children.length === 4) {
        const answerButton = document.getElementById(`add-answer-${questionNumber}`);
        answerButton.disabled = true;
    }

    const answerInput = document.createElement('div');
    const answerId = `a-${questionNumber}-${answersContainer.children.length + 1}`;

    answerInput.classList.add('my-3');
    answerInput.innerHTML =
        `<label for="${answerId}">Helyes válasz: </label>
        <input type="radio" class="checkbox" ${answersContainer.children.length === 0 ? 'checked' : ''} name="ca-${questionNumber}" value="${answerId}" required>
        <input type="text" name="${answerId}" required>
        <button type="button" class="btn btn-danger" onclick="deleteAnswer(${questionNumber}, this)"><i class="bi bi-trash"></i> Válasz Törlése</button>`;

    answersContainer.appendChild(answerInput);
}

function deleteAnswer(questionNumber, answerElement) {
    const answersContainer = document.getElementById(`answersContainer${questionNumber}`);

    answersContainer.removeChild(answerElement.parentNode);

    if (answersContainer.children.length === 0) {
        deleteQuestion(questionNumber);

    } else {
        const radioButtons = answersContainer.querySelectorAll('input[type="radio"]');
        const checkedRadioButtons = Array.from(radioButtons).filter(checkbox => checkbox.checked);

        if (checkedRadioButtons.length === 0) {
            radioButtons[radioButtons.length - 1].checked = true;
        }

        const answerButton = document.getElementById(`add-answer-${questionNumber}`);
        answerButton.disabled = false;
    }
}

function deleteQuestion(questionNumber) {
    const questionContainer = document.getElementById(`questionsContainer`);
    const questionToRemove = document.getElementById(`qc-${questionNumber}`);

    questionToRemove.remove();

    questionCount--;

    if (questionContainer.children.length === 0) {
        document.getElementById('submit').disabled = true;
    }
}

function filterItems() {
    const filterInput = document.getElementById('filterInput');
    const filterValue = filterInput.value.toUpperCase().trim();
    const itemsContainer = document.getElementById('existingQuestionsContainer');
    const searchableElement = itemsContainer.querySelectorAll('.searchable');

    for (let i = 0; i < searchableElement.length; i++) {
        const textContent = searchableElement[i].innerText || searchableElement.textContent;

        itemsContainer.children[i].style.display = textContent.toUpperCase().trim().indexOf(filterValue) > -1 ? '' : 'none';
    }
}

try {
    document.getElementById('filterInput').addEventListener('input', filterItems);
} catch (e) { }
