document.addEventListener("DOMContentLoaded", () => {
    const donut = document.getElementById('donut');

    if (donut) {
        const labels = JSON.parse(donut.dataset.labels);
        const montants = JSON.parse(donut.dataset.montants);

        new Chart(donut, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Total dépenses ',
                        data: montants,
                        hoverOffset: 10
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
});
