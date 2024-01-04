const submitBtn = document.getElementById('submit');
const createEventForm = document.getElementById('create_event_form');
const games = document.getElementsByName("game");
const description = document.getElementById('event_description');
const datetime = document.getElementById('event_time')
const peopleCount = document.getElementById('event_people')
const descriptionError = document.getElementById('description_error');
const gameError = document.getElementById('game_error');
const dateError = document.getElementById('date_error');
const peopleCountError = document.getElementById('people_count_error');
if (submitBtn !== null) {
    submitBtn.addEventListener('click', (e) => {
        let errorOccured = false;
        gameError.innerHTML = '';
        descriptionError.innerHTML= '';
        dateError.innerHTML = '';
        peopleCountError.innerHTML = '';


        // check that game was selected
        let gameSelected = false;
        for (const game of games) {
            if (game.checked === true) {
                gameSelected = true;
                break;
            }
        }
        if (!gameSelected) {
            errorOccured = true;
            gameError.innerHTML = 'game must be selected'
        }
        // check that description not empty
        if (description.value === '') {
            errorOccured = true;
            descriptionError.innerHTML = 'description can not be empty'
        }
        // check that date is not in past
        if (datetime.value === '') {
            errorOccured = true;
            dateError.innerHTML = 'date and time must be selected';
        } else {
            let date = new Date(datetime.value);
            let now = new Date();
            if (date < now) {
                errorOccured = true;
                dateError.innerHTML = 'event cannot be held in the past';
            }
        }
        // check that people count is greater than 2 and less than 100
        if (peopleCount.value < 2 || peopleCount.value > 99) {
            peopleCountError.innerHTML = 'people count must be between 2 and 99'
            errorOccured = true;
        }


        if (errorOccured) {
            e.preventDefault();
        }
    })
}
