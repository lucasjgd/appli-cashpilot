document.getElementById('recurrente').addEventListener('change', function () {
    const recurrence = document.getElementById('recurrence');
    recurrence.style.display = this.checked ? 'block' : 'none';
});