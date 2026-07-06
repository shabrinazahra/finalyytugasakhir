import { togglePassword } from '../components/togglePassword';

document.addEventListener("DOMContentLoaded", function () {

    const password = document.getElementById('password');
    const eyeOpen = document.getElementById('eyeOpen');
    const eyeClosed = document.getElementById('eyeClosed');
    const toggleBtn = document.getElementById('togglePasswordBtn'); // ← tambahkan ini

    // Ganti window.togglePassword dengan addEventListener
    toggleBtn.addEventListener('click', function () {
        togglePassword(password, eyeOpen, eyeClosed);
    });

});