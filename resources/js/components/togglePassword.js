export function togglePassword(password, eyeOpen, eyeClosed) {
    if (!password || !eyeOpen || !eyeClosed) return;

    const isPassword = password.type === "password";
    password.type = isPassword ? "text" : "password";

    // isPassword true = tadi tersembunyi, sekarang tampil → tunjukkan eyeOpen (eye)
    // isPassword false = tadi tampil, sekarang tersembunyi → tunjukkan eyeClosed (eye-off)
    if (isPassword) {
        eyeOpen.classList.remove("hidden");
        eyeClosed.classList.add("hidden");
    } else {
        eyeOpen.classList.add("hidden");
        eyeClosed.classList.remove("hidden");
    }
}
