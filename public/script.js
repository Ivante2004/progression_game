document.addEventListener("DOMContentLoaded", () => {
    const playButton = document.getElementById("play-button");
    const checkAnswerButton = document.getElementById("check-answer-button");
    const reloadGamesButton = document.getElementById("reload-games-button");
    const playerNameInput = document.getElementById("player_name");
    const gamesList = document.getElementById("games-list");
    const progressionDiv = document.getElementById("progression");
    const gameResult = document.getElementById("game-result");
    const answerInput = document.getElementById("answer-input");
    const gameStatusSection = document.getElementById("game-status");

    let currentGameId = null;

    async function loadGames() {
        const response = await fetch('/games');
        const data = await response.json();

        gamesList.innerHTML = "";
        data.games.forEach(game => {
            const listItem = document.createElement("tr");

            const playerNameCell = document.createElement("td");
            playerNameCell.textContent = game.player_name;

            const gameDateCell = document.createElement("td");
            gameDateCell.textContent = game.game_date;

            const missedNumberCell = document.createElement("td");
            missedNumberCell.textContent = game.missed_number;

            const resultCell = document.createElement("td");
            resultCell.textContent = game.result ? game.result : "Не завершена";

            const playerAnswerCell = document.createElement("td");
            playerAnswerCell.textContent = game.player_answer ? game.player_answer : "Не ответил";

            const detailsLink = document.createElement("a");
            detailsLink.href = "#";
            detailsLink.textContent = "Посмотреть детали";
            detailsLink.addEventListener("click", () => showGameDetails(game.id));

            const detailsCell = document.createElement("td");
            detailsCell.appendChild(detailsLink);

            listItem.appendChild(playerNameCell);
            listItem.appendChild(gameDateCell);
            listItem.appendChild(missedNumberCell);
            listItem.appendChild(resultCell);
            listItem.appendChild(playerAnswerCell);
            listItem.appendChild(detailsCell);

            gamesList.appendChild(listItem);
        });
    }

    async function showGameDetails(gameId) {
        const response = await fetch(`/games/${gameId}`);
        const data = await response.json();

        const gameDetailsContent = document.getElementById("game-details-content");
        gameDetailsContent.innerHTML = `
        <p><strong>Игрок:</strong> ${data.game.player_name}</p>
        <p><strong>Дата игры:</strong> ${data.game.game_date}</p>
        <p><strong>Пропущенное число:</strong> ${data.game.missed_number}</p>
        <p><strong>Результат:</strong> ${data.game.result ? data.game.result : "Не завершена"}</p>
        <p><strong>Ответ игрока:</strong> ${data.game.player_answer ? data.game.player_answer : "Не ответил"}</p>
    `;

        const gameDetailsSection = document.getElementById("game-details");
        gameDetailsSection.style.display = "block";
    }

    async function startNewGame() {
        const playerName = playerNameInput.value.trim();
        if (playerName === "") {
            alert("Пожалуйста, введите имя игрока");
            return;
        }

        const response = await fetch('/games', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ player_name: playerName })
        });

        const data = await response.json();
        currentGameId = data.game_id;

        progressionDiv.innerHTML = data.progression.join(", ");
        gameStatusSection.style.display = "block";
        gameResult.textContent = "";
        answerInput.disabled = false;
        checkAnswerButton.style.display = "inline-block";
    }

    async function checkAnswer() {
        const answered = answerInput.value.trim();
        if (!answered) {
            alert("Введите число для ответа");
            return;
        }

        const response = await fetch(`/step/${currentGameId}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                answered: parseInt(answered)
            })
        });

        const data = await response.json();

        if (data.status === "win") {
            gameResult.textContent = "Вы выиграли!";
        } else if (data.status === "lose") {
            gameResult.textContent = "Вы проиграли!";
        }

        checkAnswerButton.style.display = "none";

        loadGames();
    }

    playButton.addEventListener("click", startNewGame);

    checkAnswerButton.addEventListener("click", checkAnswer);

    reloadGamesButton.addEventListener("click", loadGames);

    loadGames();
});
