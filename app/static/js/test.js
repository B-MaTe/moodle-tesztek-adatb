function resetGroup(groupName) {
    document.querySelectorAll('input[type="radio"][name="' + groupName + '"]').forEach(function (radio) {
        radio.checked = false;
    });
}

function toggleEdit(inputFieldName, editButtonName) {
    const titleInput = document.getElementById(inputFieldName);
    const editButton = document.getElementById(editButtonName);

    titleInput.readOnly = !titleInput.readOnly;

    if (titleInput.readOnly) {
        editButton.innerText = 'Szerkesztés';
    } else {
        editButton.innerText = 'Mentés';
    }
}

let questionCount = 1;


function addQuestion() {
    const questionsContainer = document.getElementById('questionsContainer');

    // Create a new question container
    const questionContainer = document.createElement('div');
    questionContainer.classList.add('question-container');
    questionContainer.innerHTML =
        `<label for="q-${questionCount}">Kérdés ${questionCount}:</label>
        <input type="text" class="w-50 my-3" id="q-${questionCount}" name="q-${questionCount}" required>
        <br />
        <label for="p-${questionCount}">Kérdés ${questionCount} pontszáma:</label>
        <input type="number" class="w-50 my-3" id="p-${questionCount}" name="p-${questionCount}" required>
        <br />
        <button type="button" class="btn btn-secondary" onclick="addAnswer(${questionCount})"><i class="bi bi-plus"></i> Válasz hozzáadása</button>
        <button type="button" class="btn btn-danger" onclick="deleteQuestion(${questionCount})"><i class="bi bi-trash"></i> Törlés</button>
        <div id="answersContainer${questionCount}" class="answer-container"></div>`;

    // Append the question container to the questions container
    questionsContainer.appendChild(questionContainer);

    // Enable the Save button when at least one question is added
    document.getElementById('submit').disabled = false;

    // Increment the question count
    questionCount++;
}

function addAnswer(questionNumber) {
    const answersContainer = document.getElementById(`answersContainer${questionNumber}`);

    // Create a new answer input with a radio button
    const answerInput = document.createElement('div');
    answerInput.classList.add('my-3');
    const answerId = `a-${questionNumber}-${answersContainer.children.length + 1}`;
    answerInput.innerHTML =
        `<label for="${answerId}">Helyes válasz: </label>
        <input type="radio" class="checkbox" ${answersContainer.children.length === 0 ? 'checked' : ''} name="ca-${questionNumber}" value="${answerId}" required>
        <input type="text" name="${answerId}" required>
        <button type="button" class="btn btn-danger" onclick="deleteAnswer(${questionNumber}, this)"><i class="bi bi-trash"></i> Törlés</button>`;

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
    }
}

function deleteQuestion(questionNumber) {
    const questionContainer = document.getElementById(`questionsContainer`);
    const questionToRemove = document.getElementById(`q-${questionNumber}`).parentNode;

    questionContainer.removeChild(questionToRemove);

    questionCount--;

    if (questionContainer.children.length === 0) {
        document.getElementById('submit').disabled = true;
    }
}