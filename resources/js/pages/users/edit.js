document.addEventListener("DOMContentLoaded", function () {
    // ======================
    // POSYANDU
    // ======================
    const roleSelect = document.getElementById("roleSelect");
    const posyanduField = document.getElementById("posyanduField");

    if (roleSelect && posyanduField) {
        function togglePosyandu() {
            if (roleSelect.value === "kader") {
                posyanduField.classList.remove("hidden");
            } else {
                posyanduField.classList.add("hidden");
            }
        }

        togglePosyandu();
        roleSelect.addEventListener("change", togglePosyandu);
    }

    // ======================
    // PASSWORD TOGGLE
    // ======================
    const password = document.getElementById("password");
    const button = document.getElementById("togglePasswordBtn");
    const eyeOpen = document.getElementById("eyeOpen");
    const eyeClosed = document.getElementById("eyeClosed");

    if (button && password) {
        button.addEventListener("click", function () {
            const isPassword = password.getAttribute("type") === "password";

            // ubah type (INI FIX UTAMA)
            password.setAttribute("type", isPassword ? "text" : "password");

            // ubah icon
            if (eyeOpen && eyeClosed) {
                eyeOpen.classList.toggle("hidden", !isPassword);
                eyeClosed.classList.toggle("hidden", isPassword);
            }
        });
    }
});
