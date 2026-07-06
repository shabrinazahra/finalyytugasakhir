export function togglePosyandu(roleSelect, posyanduField) {
    if (!roleSelect || !posyanduField) return;

    if (roleSelect.value === 'kader') {
        posyanduField.classList.remove('hidden');
    } else {
        posyanduField.classList.add('hidden');
    }
}