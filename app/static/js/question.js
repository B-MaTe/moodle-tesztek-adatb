
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