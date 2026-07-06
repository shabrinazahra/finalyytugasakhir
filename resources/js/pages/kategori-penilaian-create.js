document.addEventListener('DOMContentLoaded', function() {
    const body = document.getElementById('kategori-body');
    const btnTambah = document.getElementById('btn-tambah-baris');
    const template = document.getElementById('row-template');

    if (!body || !btnTambah || !template) return;

    let rowCount = 1;

    function reindexRows() {
        const rows = body.querySelectorAll('.kategori-row');
        rows.forEach((row, i) => {
            row.querySelector('.row-number').textContent = i + 1;

            const input = row.querySelector('input[type="text"]');
            const select = row.querySelector('select');

            if (input) input.name = `kategoris[${i}][nama_kategori]`;
            if (select) select.name = `kategoris[${i}][nilai]`;
        });
    }

    btnTambah.addEventListener('click', function() {
        const clone = template.content.cloneNode(true);
        const row = clone.querySelector('tr');

        row.querySelector('.btn-hapus-baris').addEventListener('click', function() {
            row.remove();
            reindexRows();
        });

        body.appendChild(row);
        rowCount++;
        reindexRows();

        body.querySelector('.kategori-row:last-child input[type="text"]').focus();
    });
});
