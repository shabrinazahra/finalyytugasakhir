// Tanpa export, langsung jalan
document.addEventListener("DOMContentLoaded", () => {
    const btn = document.getElementById("dropdownBtn");
    const menu = document.getElementById("dropdownMenu");

    if (!btn || !menu) return;

    btn.addEventListener("click", function (e) {
        e.stopPropagation();
        menu.classList.toggle("hidden");
    });

    document.addEventListener("click", function (e) {
        if (!menu.contains(e.target) && !btn.contains(e.target)) {
            menu.classList.add("hidden");
        }
    });
});
