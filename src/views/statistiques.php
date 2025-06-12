<?php
require_once('../stats_data.php');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Statistiques des manèges">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/Forloopix/public/assets/css/styles.css">
    <link rel="stylesheet" href="/Forloopix/public/assets/css/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment"></script>
    <title>Statistiques</title>
    <?php include('../templates/head.php'); ?>
</head>
<body>
<?php include('../templates/header.php'); ?>

<div class="stats-container">
    <div class="stats-header">
        <h1>Statistiques d'Opération</h1>
    </div>

    <div class="stats-filters">
        <div class="filter-group">
            <button class="filter-btn active" data-filter="attraction" data-value="global">Global</button>
            <?php foreach ($attractions as $id => $attraction): ?>
                <button class="filter-btn" data-filter="attraction" data-value="<?= $id ?>"><?= $attraction['name'] ?></button>
            <?php endforeach; ?>
        </div>
        <div class="filter-group">
            <button class="filter-btn active" data-filter="time" data-value="year">Année</button>
            <button class="filter-btn" data-filter="time" data-value="month">Mois</button>
            <button class="filter-btn" data-filter="time" data-value="week">Semaine</button>
            <button class="filter-btn" data-filter="time" data-value="today">Aujourd'hui</button>
        </div>
    </div>

    <div class="stats-grid">
        <div class="chart-container">
            <h3 class="chart-title">Visiteurs</h3>
            <canvas id="visitorsChart"></canvas>
        </div>
        <div class="chart-container">
            <h3 class="chart-title">Moyenne du Cri-o-mètre (dB)</h3>
            <canvas id="sonometerChart"></canvas>
        </div>
        <div class="chart-container">
            <h3 class="chart-title">Lancements</h3>
            <canvas id="launchesChart"></canvas>
        </div>
        <div class="chart-container">
            <h3 class="chart-title">Taux d'occupation moyen</h3>
            <canvas id="seatsChart"></canvas>
        </div>
    </div>
     <div class="stats-grid">
        <div class="chart-container-full">
             <h3 class="chart-title">Relevés du Cri-o-mètre (temps réel simulé)</h3>
             <canvas id="sonometerDetailChart"></canvas>
        </div>
    </div>
</div>

<script>
    // Data PHP -> JS
    const statsData = <?= json_encode($fake_stats) ?>;
    const attractions = <?= json_encode($attractions) ?>;

    let visitorsChart, sonometerChart, launchesChart, seatsChart, sonometerDetailChart;
    let currentAttraction = 'global';
    let currentTimeRange = 'year';

    function getFilteredData(attractionId, timeRange) {
        const endDate = moment();
        let startDate;
        let unit;

        switch(timeRange) {
            case 'year': startDate = moment().subtract(365, 'days'); unit = 'month'; break;
            case 'month': startDate = moment().subtract(30, 'days'); unit = 'day'; break;
            case 'week': startDate = moment().subtract(7, 'days'); unit = 'day'; break;
            case 'today': startDate = moment().startOf('day'); unit = 'hour'; break;
        }

        let filtered = {};
        let sourceData = (attractionId === 'global') ? aggregateGlobalData() : statsData[attractionId];
        
        Object.keys(sourceData).forEach(date => {
            if (moment(date).isBetween(startDate, endDate)) {
                filtered[date] = sourceData[date];
            }
        });
        return { data: filtered, unit: unit };
    }
    
    function aggregateGlobalData() {
        const globalData = {};
        for (const attractionId in statsData) {
            for (const date in statsData[attractionId]) {
                if (!globalData[date]) {
                    globalData[date] = { visitors: 0, launches: 0, sonometer_avg_sum: 0, sonometer_count: 0, avg_seats_sum: 0, avg_seats_count: 0, sonometer_readings: [] };
                }
                globalData[date].visitors += statsData[attractionId][date].visitors;
                globalData[date].launches += statsData[attractionId][date].launches;
                globalData[date].sonometer_avg_sum += statsData[attractionId][date].sonometer_avg * statsData[attractionId][date].launches;
                globalData[date].sonometer_count += statsData[attractionId][date].launches;
                globalData[date].avg_seats_sum += statsData[attractionId][date].avg_seats * statsData[attractionId][date].launches;
                globalData[date].avg_seats_count += statsData[attractionId][date].launches;
                if(currentTimeRange === 'today') {
                   globalData[date].sonometer_readings.push(...statsData[attractionId][date].sonometer_readings);
                }
            }
        }
        
        for (const date in globalData) {
             globalData[date].sonometer_avg = globalData[date].sonometer_count > 0 ? globalData[date].sonometer_avg_sum / globalData[date].sonometer_count : 0;
             globalData[date].avg_seats = globalData[date].avg_seats_count > 0 ? globalData[date].avg_seats_sum / globalData[date].avg_seats_count : 0;
        }
        return globalData;
    }

    function createChart(ctx, chartType, label, data, labels, unit) {
        return new Chart(ctx, {
            type: chartType,
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data,
                    borderColor: '#27ae60',
                    backgroundColor: 'rgba(39, 174, 96, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointBackgroundColor: '#27ae60',
                    pointRadius: 2,
                    pointHoverRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: unit
                        }
                    },
                    y: {
                        beginAtZero: chartType !== 'sonometerDetail'
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    function updateCharts() {
        const { data, unit } = getFilteredData(currentAttraction, currentTimeRange);
        
        const labels = Object.keys(data).sort();
        const visitorsData = labels.map(l => data[l].visitors);
        const sonometerData = labels.map(l => data[l].sonometer_avg);
        const launchesData = labels.map(l => data[l].launches);
        const seatsData = labels.map(l => data[l].avg_seats);

        if (visitorsChart) visitorsChart.destroy();
        visitorsChart = createChart(document.getElementById('visitorsChart').getContext('2d'), 'line', 'Visiteurs', visitorsData, labels, unit);
        
        if (sonometerChart) sonometerChart.destroy();
        sonometerChart = createChart(document.getElementById('sonometerChart').getContext('2d'), 'line', 'Cri-o-mètre (dB)', sonometerData, labels, unit);
        
        if (launchesChart) launchesChart.destroy();
        launchesChart = createChart(document.getElementById('launchesChart').getContext('2d'), 'line', 'Lancements', launchesData, labels, unit);

        if (seatsChart) seatsChart.destroy();
        seatsChart = createChart(document.getElementById('seatsChart').getContext('2d'), 'line', 'Occupation moyenne', seatsData, labels, unit);

        // Special chart for detailed sonometer
        if(currentTimeRange === 'today') {
            document.getElementById('sonometerDetailChart').parentElement.style.display = 'block';
            let sonometerDetailData;
            if(currentAttraction === 'global'){
                 sonometerDetailData = data[labels[0]] ? data[labels[0]].sonometer_readings.flat() : [];
            } else {
                 sonometerDetailData = data[labels[0]] ? statsData[currentAttraction][labels[0]].sonometer_readings : [];
            }
            
            const detailLabels = sonometerDetailData.map((_, i) => moment().startOf('day').add(i * (24*60*60/sonometerDetailData.length), 'seconds'));

            if (sonometerDetailChart) sonometerDetailChart.destroy();
            sonometerDetailChart = createChart(document.getElementById('sonometerDetailChart').getContext('2d'), 'line', 'Relevé par seconde', sonometerDetailData, detailLabels, 'minute');
        } else {
            document.getElementById('sonometerDetailChart').parentElement.style.display = 'none';
        }
    }

    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const filterType = btn.dataset.filter;
            const value = btn.dataset.value;

            document.querySelectorAll(`.filter-btn[data-filter="${filterType}"]`).forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            if (filterType === 'attraction') {
                currentAttraction = value;
            } else if (filterType === 'time') {
                currentTimeRange = value;
            }
            updateCharts();
        });
    });

    // Initial load
    updateCharts();
</script>

</body>
</html> 