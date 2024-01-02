const addGameForm = document.getElementById('create_game_form');
const gameName = document.getElementById('game_name');
const gameDescription = document.getElementById('game_description');
const gameImage = document.getElementById('game_image');
const meta = document.getElementById('meta_checkbox');
const pico = document.getElementById('pico_checkbox');
const pcvr = document.getElementById('pcvr_checkbox');

const nameError = document.getElementById('name_error');
const descriptionError = document.getElementById('description_error');
const imageError = document.getElementById('image_error');
const checkboxError = document.getElementById('checkbox_error');


function isGameAlreadyExist(name) {
    var xmlhttp = new XMLHttpRequest();

    xmlhttp.open("GET", "includes/check_game.inc.php?q=" + name, true);

    xmlhttp.onload = function() {
        if (this.status == 200) {
            if (this.response == '') {
                nameError.innerHTML = '';
            } else {
                nameError.innerHTML = this.responseText;
            }
        }
    };

    xmlhttp.send();
}

addGameForm.addEventListener('submit', (e) => {
    nameError.innerHTML = '';
    descriptionError.innerHTML = '';
    imageError.innerHTML = '';
    checkboxError.innerHTML = '';


    errorOccured = false;



    if (gameName.value == '') {
        errorOccured = true;
        nameError.innerHTML = "name can not be empty string";
    }
    if (gameDescription.value == '') {
        errorOccured = true;
        descriptionError.innerHTML = "description can not be empty string";
    }
    if (gameImage.value == '') {
        errorOccured = true;
        imageError.innerHTML = "you should upload an image";
    }
    // if (gameImage.value['type'] != 'image/jpeg') {
    //     console.log(gameImage.value)
    //     errorOccured = true;
    //     imageError.innerHTML = "only jpegs are allowed";
    // }


    // exp = gameImage.value.split('.').slice(-1)[0];
    // if (exp != 'jpg' && exp != 'jpeg') {
    //         console.log(gameImage.value.split('.').slice(-1)[0])
    //         errorOccured = true;
    //         imageError.innerHTML = "only jpegs are allowed";
    // }

    if (meta.checked == false && pico.checked == false && pcvr.checked == false) {
        errorOccured = true;
        checkboxError.innerHTML = "you should select at least one platform";
    }




    if (errorOccured) {
        e.preventDefault()
    }
})

gameName.addEventListener('keyup', (e) => {
    isGameAlreadyExist(gameName.value);
})