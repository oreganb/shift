
const getRandomPromptData = () => {
    return Array.from({ length: 7 }, () => Math.floor(Math.random() * (300 - 100 + 1)) + 100);
};

const promptsChart = new CustomChartJs({
    selector: '#promptsChart',
    options: () => {
        return {
            type: 'bar',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    data: getRandomPromptData(),
                    backgroundColor: ins('chart-primary'),
                    borderRadius: 4,
                    borderSkipped: false
                }]
            },
            options: {
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                },
                scales: {
                    x: {
                        display: false,
                        grid: { display: false }
                    },
                    y: {
                        display: false,
                        grid: { display: false }
                    }
                }
            }
        };
    }
});


const accuracyChart = new CustomChartJs({
    selector: '#accuracyChart',
    options: () => {
        return {
            type: 'pie',
            data: {
                labels: ['Correct', 'Partially Correct', 'Incorrect', 'Unclear'],
                datasets: [{
                    data: [65, 20, 10, 5],
                    backgroundColor: [
                        ins('chart-primary'),
                        ins('chart-secondary'),
                        ins('chart-gray'),
                        ins('chart-dark')
                    ],
                    borderColor: '#fff',
                    borderWidth: 0
                }]
            },
            options: {
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function (ctx) {
                                return `${ctx.label}: ${ctx.parsed}%`;
                            }
                        }
                    }
                },
                scales: {
                    x: { display: false, grid: { display: false }, ticks: { display: false } },
                    y: { display: false, grid: { display: false }, ticks: { display: false } }
                }
            }
        };
    }
});


// Token Consumption Chart (Line Chart)
const tokenChart = new CustomChartJs({
    selector: '#tokenChart',
    options: () => {
        return {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    data: [82000, 95000, 103000, 112000, 121500, 135200, 148000],
                    backgroundColor: ins('chart-primary-rgb', 0.1),
                    borderColor: ins('chart-primary'),
                    tension: 0.4,
                    fill: true,
                    pointRadius: 0,
                    borderWidth: 2
                }]
            },
            options: {
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                },
                scales: {
                    x: {
                        display: false,
                        grid: { display: false }
                    },
                    y: {
                        display: false,
                        grid: { display: false }
                    }
                }
            }
        };
    }
});
