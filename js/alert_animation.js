const alert = document.getElementById('alert');

if (alert !== null) {
    alert.addEventListener('click', (e) => {
        alert.style.display = 'none';
    })
}
