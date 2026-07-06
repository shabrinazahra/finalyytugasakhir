function handleClick(el) {
    const id = el.dataset.id;
    const index = parseInt(el.dataset.index);
    moveIndicator(id, index);

    // Tampilkan kembali indicator jika tersembunyi
    const indicator = document.getElementById("indicator-" + id);
    if (indicator) {
        indicator.classList.remove("hidden");
    }
}

function moveIndicator(id, index) {
    if (index === null || index === undefined || isNaN(index)) return;

    const indicator = document.getElementById("indicator-" + id);
    if (!indicator) return;
    const container = indicator.parentElement;

    const width = container.offsetWidth;
    const usableWidth = width - 16 - 24; // p-2 padding (8px * 2) and indicator width (24px)
    const step = usableWidth / 16;

    indicator.style.left = `${8 + index * step}px`;
    indicator.style.transform = "none";
}

function updateAllIndicators() {
    document.querySelectorAll("[id^='indicator-']").forEach(function (el) {
        const indexAttr = el.getAttribute("data-index");
        if (indexAttr !== "" && indexAttr !== null) {
            const index = parseInt(indexAttr);
            const id = el.id.replace("indicator-", "");
            moveIndicator(id, index);
        }
    });
}

// POSISI AWAL & RESIZE EVENT
document.addEventListener("DOMContentLoaded", function () {
    setTimeout(updateAllIndicators, 150);
    window.addEventListener("resize", updateAllIndicators);
});

// Make handleClick globally available
window.handleClick = handleClick;
