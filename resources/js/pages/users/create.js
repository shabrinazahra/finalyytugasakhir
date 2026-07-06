import { togglePassword } from "../../components/togglePassword";
import { togglePosyandu } from "../../components/togglePosyandu";

document.addEventListener("DOMContentLoaded", function () {
    // ======================
    // POSYANDU
    // ======================
    const roleSelect = document.getElementById("roleSelect");
    const posyanduField = document.getElementById("posyanduField");

    if (roleSelect) {
        togglePosyandu(roleSelect, posyanduField);

        roleSelect.addEventListener("change", function () {
            togglePosyandu(roleSelect, posyanduField);
        });
    }

    // ======================
    // PASSWORD
    // ======================
    const password = document.getElementById("password");
    const eyeOpen = document.getElementById("eyeOpen");
    const eyeClosed = document.getElementById("eyeClosed");
    const button = document.getElementById("togglePasswordBtn");

    if (button) {
        button.addEventListener("click", function () {
            togglePassword(password, eyeOpen, eyeClosed);
        });
    }
});
