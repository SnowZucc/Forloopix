<?php
error_reporting(E_ERROR);
ini_set('display_errors', 0);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Gestion des capteurs">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/Forloopix/public/assets/css/styles.css">
    <link rel="stylesheet" href="/Forloopix/public/assets/css/dashboard.css">
    <title>Gestion des Capteurs</title>
    <?php include('../templates/head.php'); ?>
</head>
<body>
<?php include('../templates/header.php'); ?>

<div class="settings-container">
    <div class="settings-header">
        <h1>Gestion des Capteurs</h1>
    </div>

    <div class="settings-panel">
        <div class="setting-item">
            <div class="setting-info">
                <h3 class="setting-title">Temps avant arrêt après capteur final</h3>
                <p class="setting-description">Définit le délai en secondes entre l'activation du capteur de milieu de parcours et l'arrêt complet de l'attraction.</p>
            </div>
            <div class="setting-control">
                <input type="number" class="setting-input" id="ride-end-delay" value="15">
                <span style="margin-left: 5px;">secondes</span>
            </div>
        </div>
        <div class="setting-item">
            <div class="setting-info">
                <h3 class="setting-title">Vitesse du Moteur</h3>
                <p class="setting-description">Définit la vitesse du moteur au démarrage de l'attraction (0-255).</p>
            </div>
            <div class="setting-control">
                <input type="number" class="setting-input" id="motor-speed" value="150" min="0" max="255">
            </div>
        </div>
        <div class="setting-item">
            <div class="setting-info">
                <h3 class="setting-title">Contrôle Moteur Manuel</h3>
                <p class="setting-description" id="motor-status-text">État actuel : Inconnu</p>
            </div>
            <div class="setting-control">
                <button id="motor-on-btn" class="btn-control" style="background-color: #28a745; box-shadow: 0 4px 0 #218838; margin-right: 10px;">ON</button>
                <button id="motor-off-btn" class="btn-control" style="background-color: #dc3545; box-shadow: 0 4px 0 #c82333;">OFF</button>
            </div>
        </div>

        <div class="setting-item">
            <div class="setting-info">
                <h3 class="setting-title">Diagnostic Capteur Tracking</h3>
                <p class="setting-description">Affiche le dernier état connu pour chaque tronçon et permet de réinitialiser les capteurs principaux.</p>
            </div>
            <div class="setting-control">
                <button id="reset-sensors-btn" class="btn-control" style="background-color: #e74c3c; box-shadow: 0 5px 0 #c0392b;">Reset Capteurs</button>
            </div>
        </div>
        <div id="tracking-data-container" class="diagnostic-container">
            <!-- Les données de diagnostic seront insérées ici -->
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const delayInput = document.getElementById('ride-end-delay');
    const motorSpeedInput = document.getElementById('motor-speed');
    const trackingContainer = document.getElementById('tracking-data-container');
    const resetSensorsBtn = document.getElementById('reset-sensors-btn');
    const motorOnBtn = document.getElementById('motor-on-btn');
    const motorOffBtn = document.getElementById('motor-off-btn');
    const motorStatusText = document.getElementById('motor-status-text');

    // --- LOGIQUE POUR LE DÉLAI ---
    const savedDelay = localStorage.getItem('rideEndDelay');
    if (savedDelay) {
        delayInput.value = savedDelay;
    }
    delayInput.addEventListener('change', () => {
        localStorage.setItem('rideEndDelay', delayInput.value);
        console.log(`Délai sauvegardé : ${delayInput.value} secondes`);
    });

    // --- LOGIQUE POUR LA VITESSE MOTEUR ---
    const savedMotorSpeed = localStorage.getItem('motorSpeed');
    if (savedMotorSpeed) {
        motorSpeedInput.value = savedMotorSpeed;
    }
    motorSpeedInput.addEventListener('change', () => {
        localStorage.setItem('motorSpeed', motorSpeedInput.value);
        console.log(`Vitesse moteur sauvegardée : ${motorSpeedInput.value}`);
    });

    // --- LOGIQUE POUR LE CONTRÔLE MOTEUR ---
    function fetchMotorStatus() {
        fetch('/Forloopix/src/api/get_motor_status.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const speed = data.speed;
                    motorStatusText.textContent = speed > 0 ? `État actuel : ON (Vitesse ${speed})` : 'État actuel : OFF';
                } else {
                    motorStatusText.textContent = 'État actuel : Erreur';
                }
            }).catch(() => motorStatusText.textContent = 'État actuel : Erreur');
    }

    function setMotorSpeed(speed) {
        return fetch('/Forloopix/src/api/set_motor_speed.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ speed: speed })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                fetchMotorStatus(); // Rafraîchir l'état après la commande
            } else {
                alert('Erreur lors de la mise à jour de la vitesse.');
            }
        }).catch(() => alert('Erreur de communication.'));
    }

    motorOnBtn.addEventListener('click', () => {
        const motorSpeed = parseInt(localStorage.getItem('motorSpeed') || '150', 10);
        setMotorSpeed(motorSpeed);
    });

    motorOffBtn.addEventListener('click', () => setMotorSpeed(0));

    // --- LOGIQUE POUR LE DIAGNOSTIC ---
    function fetchTrackingData() {
        fetch('/Forloopix/src/api/get_capteur_tracking_data.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    trackingContainer.innerHTML = ''; // Clear existing data
                    if (data.data.length === 0) {
                        trackingContainer.innerHTML = '<p>Aucune donnée de capteur disponible.</p>';
                        return;
                    }
                    data.data.forEach(row => {
                        const diagnosticItem = document.createElement('div');
                        diagnosticItem.className = 'setting-item';
                        
                        const statusClass = row.statut == '1' ? 'status-active' : 'status-inactive';
                        const statusText = row.statut == '1' ? 'Actif' : 'Inactif';

                        diagnosticItem.innerHTML = `
                            <div class="setting-info">
                                <h3 class="setting-title">${row.tronçon}</h3>
                            </div>
                            <div class="setting-control">
                                <span class="status-indicator ${statusClass}">${statusText}</span>
                            </div>
                        `;
                        trackingContainer.appendChild(diagnosticItem);
                    });
                } else {
                    console.error('Erreur de récupération des données de tracking:', data.message);
                    trackingContainer.innerHTML = '<p>Erreur de chargement des données.</p>';
                }
            })
            .catch(error => {
                console.error('Erreur de communication pour les données de tracking:', error);
                trackingContainer.innerHTML = '<p>Erreur de communication avec le serveur.</p>';
            });
    }

    function resetSensorStatuses() {
        console.log("Demande de réinitialisation du statut des capteurs...");
        if (!confirm("Voulez-vous vraiment réinitialiser le statut des derniers capteurs 'Départ' et 'Milieu' ?")) {
            return;
        }
        fetch('/Forloopix/src/api/reset_sensor_status.php', { method: 'POST' })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    console.log('Statut des capteurs réinitialisé avec succès.');
                    alert('Les derniers capteurs "Départ" et "Milieu" ont été réinitialisés.');
                    fetchTrackingData(); // Refresh the table
                } else {
                    console.error('Échec de la réinitialisation du statut des capteurs:', data.message);
                    alert('Erreur lors de la réinitialisation: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur de communication avec l\'API de réinitialisation:', error);
                alert('Erreur de communication avec le serveur.');
            });
    }

    // Event Listeners
    resetSensorsBtn.addEventListener('click', resetSensorStatuses);

    // Initial data load
    fetchTrackingData();
    fetchMotorStatus();
    setInterval(fetchMotorStatus, 5000); // Rafraîchit l'état du moteur toutes les 5s
});
</script>

<?php include('../templates/footer.php'); ?>
</body>
</html> 