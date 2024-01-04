const form = document.getElementById('edit_profile_form');
const name = document.getElementById('new_name');
const info = document.getElementById('new_info');
const infoError = document.getElementById('info_error');
const nameError = document.getElementById('name_error');
const submit = document.getElementById('submit');
const id = document.getElementById('id');


if (name !== null) {
    name.addEventListener('keyup', (e) => {
        submit.disabled = false;
        nameError.innerHTML = '';

        let xmlhttp = new XMLHttpRequest();

        xmlhttp.open("GET", "includes/check_username.inc.php?q=" + name.value + "&id=" + id.value, true);

        xmlhttp.onload = function() {
            if (this.status === 200) {
                if (this.response !== '') {
                    submit.disabled = true;
                    nameError.innerHTML = this.responseText;
                }
            }
        };
        xmlhttp.send();

        if (name.value.trim() === '') {
            submit.disabled = true;
            nameError.innerHTML = 'name cannot be empty'
        }
        if (name.value.trim().length < 5) {
            submit.disabled = true;
            nameError.innerHTML = 'name must be at least 5 symbols'
        }
    })
}
if (info !== null) {
    info.addEventListener('keyup', (e) => {
        submit.disabled = false;
        infoError.innerHTML = '';

        if (info.value.trim() === '') {
            submit.disabled = true;
            infoError.innerHTML = 'info section cannot be empty'
        }
    })
}