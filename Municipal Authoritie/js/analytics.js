// analytics.js
(() => {
    'use strict';

    // Chart Configuration
    const CHART_CONFIG = {
        maintainAspectRatio: false,
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                backgroundColor: '#1F2937',
                titleColor: '#FFF',
                bodyColor: '#FFF',
                padding: 12
            }
        }
    };

    // Numeric Data Repository
    const numericData = {
        summary: {
            title: 'Key Performance Indicators',
            metrics: [
                { label: 'Total Waste Collected', value: '245T', change: '+8% vs last month' },
                { label: 'Avg. Daily Waste', value: '8.2T', change: '+1.1T daily avg' },
                { label: 'Recycling Rate', value: '68%', change: '12% improvement' },
                { label: 'Route Efficiency', value: '84%', change: 'Optimal performance' }
            ]
        },
        trend: {
            title: 'Trend Analysis',
            metrics: [
                { label: 'Current Month Total', value: '245T' },
                { label: 'Previous Month Total', value: '227T' },
                { label: 'YTD Average', value: '252T/mo' },
                { label: 'Peak Day', value: '14.2T (Dec 12)' }
            ]
        },
        composition: {
            title: 'Waste Composition',
            metrics: [
                { label: 'Organic Waste', value: '45%', volume: '110T' },
                { label: 'Recyclables', value: '30%', volume: '73.5T' },
                { label: 'Hazardous', value: '15%', volume: '36.8T' },
                { label: 'Other', value: '10%', volume: '24.5T' }
            ]
        },
        zones: {
            title: 'Zone Performance',
            metrics: [
                { label: 'Most Efficient Zone', value: 'Zone 3 (52T)' },
                { label: 'Collection Target', value: '98% achieved' },
                { label: 'Priority Zones', value: '5 zones' },
                { label: 'Avg. Collection Time', value: '4.2h/day' }
            ]
        },
        recycling: {
            title: 'Recycling Metrics',
            metrics: [
                { label: 'Plastic Recycling', value: '65%' },
                { label: 'Paper Recycling', value: '59%' },
                { label: 'Glass Recycling', value: '70%' },
                { label: 'E-Waste Recycling', value: '30%' }
            ]
        }
    };

    // Chart Data Templates
    const chartTemplates = {
        trend: {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                datasets: [{
                    label: 'Total Waste (T)',
                    data: [65, 59, 80, 81, 56, 55, 40],
                    borderColor: '#3B82F6',
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(59, 130, 246, 0.1)'
                }]
            }
        },
        composition: {
            type: 'doughnut',
            data: {
                labels: ['Organic', 'Recyclable', 'Hazardous', 'Other'],
                datasets: [{
                    data: [45, 30, 15, 10],
                    backgroundColor: ['#10B981', '#3B82F6', '#F59E0B', '#94A3B8']
                }]
            }
        },
        zones: {
            type: 'bar',
            data: {
                labels: ['Zone 1', 'Zone 2', 'Zone 3', 'Zone 4', 'Zone 5'],
                datasets: [{
                    label: 'Waste Collected (T)',
                    data: [45, 38, 52, 41, 49],
                    backgroundColor: '#3B82F6'
                }]
            }
        },
        recycling: {
            type: 'radar',
            data: {
                labels: ['Plastic', 'Paper', 'Glass', 'Metal', 'E-Waste'],
                datasets: [{
                    label: 'Recycling Rate (%)',
                    data: [65, 59, 70, 45, 30],
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    borderColor: '#10B981'
                }]
            }
        }
    };

    // Chart Management
    let charts = {};
    let chartSelector;
    let dataPanel;

    function init() {
        // DOM Elements
        chartSelector = document.getElementById('chartSelector');
        dataPanel = document.querySelector('.data-panel');
        
        // Initialize Charts
        initCharts();
        
        // Event Listeners
        chartSelector.addEventListener('change', handleViewChange);
        window.addEventListener('resize', handleResize);
        
        // Initial View
        updateView('summary');
    }

    function initCharts() {
        Object.keys(chartTemplates).forEach(chartKey => {
            const config = chartTemplates[chartKey];
            const ctx = document.getElementById(`${chartKey}Chart`)?.getContext('2d');
            
            if (!ctx) {
                showChartError(chartKey);
                return;
            }

            try {
                charts[chartKey] = new Chart(ctx, {
                    type: config.type,
                    data: config.data,
                    options: { ...CHART_CONFIG, ...config.options }
                });
            } catch (error) {
                console.error(`Chart ${chartKey} init failed:`, error);
                showChartError(chartKey);
            }
        });
    }

    function updateView(viewType) {
        // Update Data Panel
        updateNumericData(viewType);
        
        // Update Chart Visibility
        updateChartVisibility(viewType);
    }

    function updateNumericData(viewType) {
        const data = numericData[viewType];
        if (!data) return;

        dataPanel.innerHTML = `
            <h4 class="col-12 mb-3">${data.title}</h4>
            ${data.metrics.map(metric => `
                <div class="col-6 col-md-3">
                    <div class="data-card">
                        <div class="data-value">${metric.value}</div>
                        <div class="data-label">${metric.label}</div>
                        ${metric.change ? `<div class="data-change">${metric.change}</div>` : ''}
                        ${metric.volume ? `<div class="data-volume">${metric.volume}</div>` : ''}
                    </div>
                </div>
            `).join('')}
        `;
    }

    function updateChartVisibility(viewType) {
        // Hide all charts
        document.querySelectorAll('.chart-container').forEach(container => {
            container.classList.add('d-none');
        });

        // Show selected chart
        if (viewType !== 'summary') {
            const container = document.getElementById(`${viewType}ChartContainer`);
            if (container) {
                container.classList.remove('d-none');
                resizeChart(charts[viewType]);
            }
        }
    }

    function resizeChart(chart) {
        if (chart) {
            setTimeout(() => chart.resize(), 100);
        }
    }

    function handleViewChange() {
        const viewType = chartSelector.value;
        updateView(viewType);
    }

    function handleResize() {
        Object.values(charts).forEach(chart => chart?.resize());
    }

    function showChartError(chartId) {
        const container = document.getElementById(`${chartId}ChartContainer`);
        if (container) {
            container.innerHTML = `
                <div class="chart-error text-center p-4">
                    <i class="bi bi-bar-chart fs-1 text-muted"></i>
                    <p class="text-muted mt-2">Unable to load chart data</p>
                </div>
            `;
        }
    }

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', init);
})();