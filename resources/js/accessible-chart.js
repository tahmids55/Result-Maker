/**
 * ResultMaker — Accessible Chart Alpine Component
 * 
 * Provides Chart.js integration with:
 * - Full keyboard navigation (Arrow Left/Right + Enter)
 * - Screen-reader live announcements via ARIA
 * - Hidden data table fallback for assistive tech
 * - Smooth highlight transitions on focus
 */
document.addEventListener('alpine:init', () => {

    Alpine.data('accessibleChart', (config) => ({
        chart: null,
        focusIndex: -1,
        announcement: '',
        showFocusHint: false,

        get ariaLabel() {
            return `${config.title}. Chart with ${config.dataPoints.length} data points. Use left and right arrow keys to navigate.`;
        },

        get title() { return config.title; },
        get xLabel() { return config.xLabel; },
        get yLabel() { return config.yLabel; },

        init() {
            // Wait for Chart.js CDN to load
            this.$nextTick(() => {
                if (typeof Chart === 'undefined') {
                    console.warn('Chart.js not loaded yet, retrying...');
                    setTimeout(() => this.init(), 200);
                    return;
                }
                this._createChart();
            });
        },

        _createChart() {
            const ctx = this.$refs.canvas;
            if (!ctx) return;

            // Premium color palette fallback
            const defaultColors = [
                '#4F46E5', '#7C3AED', '#2563EB', '#059669',
                '#D97706', '#DC2626', '#0891B2', '#BE185D',
                '#65A30D', '#9333EA', '#EA580C', '#0D9488',
            ];

            const colors = config.colors.length > 0
                ? config.colors
                : defaultColors.slice(0, config.labels.length);

            const isCircular = ['doughnut', 'pie', 'polarArea'].includes(config.type);

            const chartConfig = {
                type: config.type,
                data: {
                    labels: config.labels,
                    datasets: [{
                        label: config.yLabel,
                        data: config.values,
                        backgroundColor: isCircular ? colors : colors[0] || defaultColors[0],
                        borderColor: isCircular ? '#ffffff' : 'transparent',
                        borderWidth: isCircular ? 2 : 0,
                        borderRadius: isCircular ? 0 : config.borderRadius,
                        hoverOffset: isCircular ? 8 : 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 600,
                        easing: 'easeOutQuart',
                    },
                    plugins: {
                        legend: {
                            display: config.legend && isCircular,
                            position: 'right',
                            labels: {
                                font: { family: 'Inter, sans-serif', size: 12 },
                                padding: 12,
                                usePointStyle: true,
                                pointStyleWidth: 10,
                            },
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15,17,23,0.9)',
                            titleFont: { family: 'Inter, sans-serif', weight: '600' },
                            bodyFont: { family: 'Inter, sans-serif' },
                            cornerRadius: 8,
                            padding: 10,
                        },
                    },
                    scales: isCircular ? {} : {
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { family: 'Inter, sans-serif', size: 11 },
                                color: '#6B7280',
                            },
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.04)',
                                drawBorder: false,
                            },
                            ticks: {
                                font: { family: 'Inter, sans-serif', size: 11 },
                                color: '#6B7280',
                                stepSize: 1,
                            },
                        },
                    },
                },
            };

            // For doughnut: add cutout and center text
            if (config.type === 'doughnut') {
                chartConfig.options.cutout = '65%';
            }

            this.chart = new Chart(ctx, chartConfig);
        },

        prevDatapoint() {
            if (config.dataPoints.length === 0) return;
            this.focusIndex = this.focusIndex <= 0
                ? config.dataPoints.length - 1
                : this.focusIndex - 1;
            this._highlightAndAnnounce();
        },

        nextDatapoint() {
            if (config.dataPoints.length === 0) return;
            this.focusIndex = this.focusIndex >= config.dataPoints.length - 1
                ? 0
                : this.focusIndex + 1;
            this._highlightAndAnnounce();
        },

        announceValue() {
            if (this.focusIndex >= 0) {
                this._highlightAndAnnounce();
            } else {
                // Announce summary
                const total = config.values.reduce((a, b) => a + b, 0);
                this.announcement = `${config.title}. Total: ${total}. ${config.dataPoints.length} categories.`;
            }
        },

        _highlightAndAnnounce() {
            const point = config.dataPoints[this.focusIndex];
            if (!point) return;

            this.announcement = `${point.label}: ${point.value}. Item ${this.focusIndex + 1} of ${config.dataPoints.length}.`;

            // Visually highlight the focused element on the chart
            if (this.chart) {
                this.chart.setActiveElements([{
                    datasetIndex: 0,
                    index: this.focusIndex,
                }]);
                this.chart.tooltip.setActiveElements([{
                    datasetIndex: 0,
                    index: this.focusIndex,
                }]);
                this.chart.update('none'); // no animation for instant feedback
            }
        },

        destroy() {
            if (this.chart) {
                this.chart.destroy();
                this.chart = null;
            }
        },
    }));
});
