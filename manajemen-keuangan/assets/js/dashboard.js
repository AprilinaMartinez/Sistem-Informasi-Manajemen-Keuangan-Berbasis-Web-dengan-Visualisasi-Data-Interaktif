// Dashboard Charts using Chart.js

// Chart Color Palette (Pastel)
const chartColors = {
    mint: 'rgba(184, 243, 209, 0.8)',
    coral: 'rgba(255, 181, 181, 0.8)',
    lavender: 'rgba(200, 182, 226, 0.8)',
    peach: 'rgba(255, 213, 181, 0.8)',
    pink: 'rgba(255, 200, 221, 0.8)',
    blue: 'rgba(168, 216, 234, 0.8)',
    yellow: 'rgba(255, 245, 186, 0.8)',
    green: 'rgba(212, 244, 221, 0.8)'
};

// Initialize Monthly Comparison Bar Chart
function initMonthlyComparisonChart(labels, incomeData, expenseData) {
    const ctx = document.getElementById('monthlyComparisonChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Pemasukan',
                    data: incomeData,
                    backgroundColor: chartColors.mint,
                    borderColor: chartColors.green,
                    borderWidth: 2,
                    borderRadius: 8
                },
                {
                    label: 'Pengeluaran',
                    data: expenseData,
                    backgroundColor: chartColors.coral,
                    borderColor: chartColors.pink,
                    borderWidth: 2,
                    borderRadius: 8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: {
                            size: 14,
                            weight: 'bold'
                        },
                        padding: 15,
                        usePointStyle: true
                    }
                },
                title: {
                    display: true,
                    text: 'Perbandingan Pemasukan & Pengeluaran per Bulan',
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: 20
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function (context) {
                            return context.dataset.label + ': ' + formatRupiah(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// Initialize 12-Month Trend Line Chart
function initTrendChart(labels, incomeData, expenseData) {
    const ctx = document.getElementById('trendChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Pemasukan',
                    data: incomeData,
                    borderColor: chartColors.mint,
                    backgroundColor: 'rgba(184, 243, 209, 0.2)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: chartColors.green,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                },
                {
                    label: 'Pengeluaran',
                    data: expenseData,
                    borderColor: chartColors.coral,
                    backgroundColor: 'rgba(255, 181, 181, 0.2)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: chartColors.pink,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: {
                            size: 14,
                            weight: 'bold'
                        },
                        padding: 15,
                        usePointStyle: true
                    }
                },
                title: {
                    display: true,
                    text: 'Tren Keuangan 12 Bulan Terakhir',
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: 20
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function (context) {
                            return context.dataset.label + ': ' + formatRupiah(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// Initialize Category Pie Chart
function initCategoryPieChart(labels, data) {
    const ctx = document.getElementById('categoryPieChart');
    if (!ctx) return;

    const colors = [
        chartColors.coral,
        chartColors.mint,
        chartColors.lavender,
        chartColors.peach,
        chartColors.pink,
        chartColors.blue,
        chartColors.yellow,
        chartColors.green
    ];

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
                borderColor: '#fff',
                borderWidth: 3,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'right',
                    labels: {
                        font: {
                            size: 13,
                            weight: 'bold'
                        },
                        padding: 15,
                        usePointStyle: true,
                        generateLabels: function (chart) {
                            const data = chart.data;
                            if (data.labels.length && data.datasets.length) {
                                return data.labels.map((label, i) => {
                                    const value = data.datasets[0].data[i];
                                    const percentage = ((value / data.datasets[0].data.reduce((a, b) => a + b, 0)) * 100).toFixed(1);
                                    return {
                                        text: `${label} (${percentage}%)`,
                                        fillStyle: data.datasets[0].backgroundColor[i],
                                        hidden: false,
                                        index: i
                                    };
                                });
                            }
                            return [];
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'Komposisi Pengeluaran per Kategori',
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: 20
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function (context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return label + ': ' + formatRupiah(value) + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}
