function initTableSearch(inputId, tableBodyId) {
    const input = document.getElementById(inputId);
    const tableBody = document.getElementById(tableBodyId);

    if (!input || !tableBody) return;

    input.addEventListener("keyup", function () {
        const keyword = this.value.toLowerCase();
        const rows = tableBody.querySelectorAll("tr");

        rows.forEach((row) => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(keyword) ? "" : "none";
        });
    });
}

document.addEventListener("DOMContentLoaded", () => {
    initTableSearch("searchBalita", "tableBalita");
    initTableSearch("searchPenilaian", "tablePenilaian");
    initTableSearch("searchUser", "tableUser");
    initTableSearch("searchKriteria", "tableKriteria");
    initTableSearch("searchKategori", "tableKategori");
    initTableSearch("searchPosyandu", "tablePosyandu");
});
