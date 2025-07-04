// Dashboard Earnings Chart
One.onLoad(function() {
    // Init Chart
    if (jQuery('#earnings-chart').length) {
        // Sample data for demonstration
        let earningsData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Earnings',
                backgroundColor: 'rgba(113, 88, 250, .15)',
                borderColor: 'rgba(113, 88, 250, 1)',
                borderWidth: 2,
                pointRadius: 4,
                pointBackgroundColor: 'rgba(113, 88, 250, 1)',
                pointBorderColor: '#fff',
                pointHoverRadius: 6,
                data: [3200, 4100, 3800, 4900, 5200, 6100]
            }]
        };

        // Chart configuration
        let earningsChart = new Chart(jQuery('#earnings-chart'), {
            type: 'line',
            data: earningsData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': $' + context.raw.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
});
