const signupForm = document.getElementById('signupForm')

const username = document.getElementById('username')
const email = document.getElementById('email')
const pass1 = document.getElementById('password1')
const pass2 = document.getElementById('password2')
const confirmLicense = document.getElementById('confirmLicense')
const submit = document.getElementById('submit')
const matchPassError = document.getElementById('matchPassError')
const weakPassError = document.getElementById('weakPassError')
const usernameError = document.getElementById('usernameError')
const confirmError = document.getElementById('confirmError')
const emailError = document.getElementById('emailError')


function isUserAlreadyExist(username) {
    var xmlhttp = new XMLHttpRequest();

    xmlhttp.open("GET", "includes/check_username.inc.php?q=" + username, true);

    xmlhttp.onload = function() {
        if (this.status == 200) {
            if (this.response == '') {
                submit.disabled = false;
                document.getElementById("usernameError").innerHTML = '';
            } else {
                submit.disabled = true;
                document.getElementById("usernameError").innerHTML = this.responseText;
            }
        }
    };

    xmlhttp.send();
}

signupForm.addEventListener('submit', (e) => {
    usernameError.innerHTML = ""
    matchPassError.innerHTML = ""
    weakPassError.innerHTML = ""
    confirmError.innerHTML = ""
    emailError.innerHTML = ""
    errorOccured =  false

    if (pass1.value.length < 8 || pass1.value === pass1.value.toLowerCase()) {
        weakPassError.innerHTML = "Password must contain at least 8 symbols and 1 uppercase letter!"
        errorOccured = true
    }
    if (pass1.value !== pass2.value) {
        matchPassError.innerHTML = "The passwords are not the same"
        errorOccured = true
    }
    if (username.value.length < 5) {
        usernameError.innerHTML = "Nickname must be at least 5 symbols long"
        errorOccured = true
    }
    if (confirmLicense.checked == false) {
        confirmError.innerHTML = "confirm our politic please"
        errorOccured = true
    }
    if (!/^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i.test(email.value)) {
        emailError.innerHTML = "invalid email adress"
        errorOccured = true

    }

    isUserAlreadyExist(username);

    if (errorOccured) {
        e.preventDefault()
        pass1.value = ""
        pass2.value = ""
    }

})

username.addEventListener('keyup', (e) => {
    isUserAlreadyExist(username.value);
})