<?php
error_reporting(E_ERROR); // Affiche uniquement les erreurs fatales
ini_set('display_errors', 0); // N'affiche pas les erreurs à l'écran

require_once('../attractions_data.php');
require_once('../../config/config.php'); 

// Détermine l'attraction à afficher. 'goudurix' par défaut.
$attraction_id = 'goudurix'; // ID par défaut
if (isset($_GET['attraction']) && array_key_exists($_GET['attraction'], $attractions)) {
    $attraction_id = $_GET['attraction'];
}
$current_attraction = $attractions[$attraction_id];

// Récupère le nombre de lancements pour aujourd'hui depuis la BDD
$todays_launch_count = 0;
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $stmt = $conn->prepare("SELECT COUNT(*) as launchCount FROM Launches WHERE attraction_id = ? AND DATE(launch_time) = CURDATE()");
    $stmt->bind_param("s", $attraction_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $todays_launch_count = $row['launchCount'] ?? 0;
    $stmt->close();
} catch (Exception $e) {
    // En cas d'erreur de BDD, on continue avec 0, mais on log l'erreur.
    error_log("Erreur de récupération du compteur de lancements: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Tableau de bord pour la gestion des manèges.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/Forloopix/public/assets/css/styles.css">
    <link rel="stylesheet" href="/Forloopix/public/assets/css/dashboard.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@700&display=swap" rel="stylesheet">
    <title>Tableau de Bord - <?= htmlspecialchars($current_attraction['name']) ?></title>
    <?php include('../templates/head.php'); ?>
</head>

<body>
<?php include('../templates/header.php'); ?>    

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1>Panneau de Contrôle - <?= htmlspecialchars($current_attraction['name']) ?></h1>
        <button id="sonometer-mode-toggle" class="mode-toggle"></button>
    </div>

    <div class="control-panel">
        <div class="panel-main">
            <div class="segment-display-container">
                <p class="panel-title" id="display-title">COMPTEUR LANCEMENTS</p>
                <div class="segment-display" id="segment-display">--</div>
            </div>
            <div class="seats-container">
                <p class="panel-title">PLACES OCCUPÉES</p>
                <div class="seats-grid">
                    <?php for ($i = 0; $i < 8; $i++): ?>
                        <div class="seat <?= $i < 5 ? 'occupied' : '' ?>"></div>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="ride-mockup">
                <div class="track-title">POSITION DU WAGON</div>
                <img src="<?= htmlspecialchars($current_attraction['image']) ?>" alt="Maquette du manège" class="ride-image">
                <div id="sensor-1" class="sensor" style="top: <?= $current_attraction['led_positions']['sensor-1']['top'] ?>; left: <?= $current_attraction['led_positions']['sensor-1']['left'] ?>;"></div>
                <div id="sensor-2" class="sensor" style="top: <?= $current_attraction['led_positions']['sensor-2']['top'] ?>; left: <?= $current_attraction['led_positions']['sensor-2']['left'] ?>;"></div>
                <div id="motor-status-display" class="motor-status">Moteur: --</div>
            </div>
        </div>
        <div class="panel-bottom">
            <div class="sonometer">
                <p class="panel-title">CRI-O-MÈTRE</p>
                <div class="sonometer-bar">
                    <div class="sonometer-level" style="width: 0%;"></div>
        </div>
                <div class="sonometer-value">-- dB</div>
            </div>

            <div class="controls">
                <button id="start-btn" class="btn-control start">START</button>
                <button id="stop-btn" class="btn-control stop">STOP</button>
            </div>
        </div>
    </div>
</div>

<script>
    // --- CONFIGURATION & DOM ELEMENTS ---
    const attractionId = '<?= $attraction_id ?>';
    let currentLaunchCount = <?= $todays_launch_count ?>;

    const display = document.getElementById('segment-display');
    const displayTitle = document.getElementById('display-title');
    const startBtn = document.getElementById('start-btn');
    const stopBtn = document.getElementById('stop-btn');
    const sensors = {
        1: document.getElementById('sensor-1'),
        2: document.getElementById('sensor-2')
    };
    const sonometerLevel = document.querySelector('.sonometer-level');
    const sonometerValue = document.querySelector('.sonometer-value');
    const sonometerModeToggleBtn = document.getElementById('sonometer-mode-toggle');
    const motorStatusDisplay = document.getElementById('motor-status-display');

    // --- STATE MANAGEMENT ---
    let rideState = {
        isRunning: false,
        isCountingDown: false,
        countdownInterval: null,
        rideEndTimeout: null
    };

    let sonometerMode = {
        isReal: true
    };

    // --- DATA SIMULATION & API CALLS ---
    function getSonometerValue() {
        // Simulates fetching a value from a sensor
        return Math.floor(Math.random() * ((-5) - (-25) + 1)) + (-25);
    }
    
    function fetchRealSonometerValue() {
        return fetch('/Forloopix/src/api/get_sonometer_value.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    return data.value;
                } else {
                    console.error('Erreur de récupération de la valeur réelle du sonomètre:', data.message);
                    return 0; // Fallback value
                }
            })
            .catch(error => {
                console.error('Erreur de communication avec l\'API du sonomètre:', error);
                return 0; // Fallback value
            });
    }

    function fetchMotorStatus() {
        fetch('/Forloopix/src/api/get_motor_status.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const speed = data.speed;
                    if (speed > 0) {
                        motorStatusDisplay.textContent = `Moteur: ON (${speed})`;
                        motorStatusDisplay.style.backgroundColor = '#28a745';
                    } else {
                        motorStatusDisplay.textContent = 'Moteur: OFF';
                        motorStatusDisplay.style.backgroundColor = '#dc3545';
                    }
                } else {
                    motorStatusDisplay.textContent = 'Moteur: Erreur';
                    motorStatusDisplay.style.backgroundColor = '#6c757d';
                }
            }).catch(() => {
                motorStatusDisplay.textContent = 'Moteur: Erreur';
                motorStatusDisplay.style.backgroundColor = '#6c757d';
            });
    }

    function setMotorSpeed(speed) {
        console.log(`Réglage de la vitesse du moteur sur : ${speed}`);

        fetch('/Forloopix/src/api/set_motor_speed.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ speed: speed })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                console.log('Vitesse moteur mise à jour avec succès :', data.message);
            } else {
                console.error('Erreur lors de la mise à jour de la vitesse du moteur :', data.message);
            }
        })
        .catch(error => {
            console.error('Erreur de communication avec l\'API du moteur:', error);
        });
    }

    function resetSensorStatuses() {
        console.log("Demande de réinitialisation du statut des capteurs...");
        fetch('/Forloopix/src/api/reset_sensor_status.php', { method: 'POST' })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    console.log('Statut des capteurs réinitialisé avec succès.');
                } else {
                    console.error('Échec de la réinitialisation du statut des capteurs:', data.message);
                }
            })
            .catch(error => console.error('Erreur de communication avec l\'API de réinitialisation:', error));
    }
    
    function saveLaunch(passengerCount) {
        console.log(`Enregistrement de ${passengerCount} passagers pour ${attractionId}...`);

        fetch('/Forloopix/src/api/record_launch.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                attraction_id: attractionId,
                passenger_count: passengerCount 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                console.log('Sauvegarde réussie, ID du lancement:', data.launch_id);
            } else {
                console.error('Erreur de sauvegarde:', data.message);
                alert('Erreur lors de la sauvegarde du lancement : ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur de communication avec le serveur:', error);
            alert('Erreur de communication avec le serveur.');
        });
    }

    // --- SENSOR STATUS (from Database) ---
    function fetchSensorStatus() {
        fetch('/Forloopix/src/api/get_sensor_status.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    const sensorStatuses = data.data;
                    // Mettre à jour l'état visuel des capteurs
                    for (const sensorId in sensorStatuses) {
                        const sensorNumber = sensorId.split('-')[1];
                        if (sensors[sensorNumber]) {
                            if (sensorStatuses[sensorId] == '1') {
                                sensors[sensorNumber].classList.add('active');
                            } else {
                                sensors[sensorNumber].classList.remove('active');
                            }
                        }
                    }

                    // Déclenche l'arrêt du manège si le capteur du milieu est actif
                    if (rideState.isRunning && sensorStatuses['sensor-2'] == '1' && rideState.rideEndTimeout === null) {
                        const rideEndDelay = parseInt(localStorage.getItem('rideEndDelay') || '15', 10) * 1000;
                        console.log(`Capteur Milieu activé, arrêt du manège dans ${rideEndDelay / 1000} secondes.`);
                        
                        // Empêche de lancer plusieurs minuteurs
                        rideState.rideEndTimeout = setTimeout(() => {
                            console.log(`Fin du timer de ${rideEndDelay / 1000}s. Arrêt du manège.`);
                            resetRide();
                        }, rideEndDelay);
                    }
                } else {
                    console.error('Erreur de récupération des statuts des capteurs:', data.message);
                }
            })
            .catch(error => {
                console.error('Erreur de communication pour les capteurs:', error);
            });
    }

    // --- CORE FUNCTIONS ---
    function setDisplayValue(value) {
        // Pad with '0' if it's a single digit number
        display.textContent = String(value).padStart(2, '0');
    }

    function resetRide() {
        console.log("Ride is resetting.");
        resetSensorStatuses(); // Réinitialise les capteurs côté serveur
        setMotorSpeed(0); // Réinitialise la vitesse du moteur

        // Clear all intervals and timeouts
        clearInterval(rideState.countdownInterval);
        clearTimeout(rideState.rideEndTimeout);

        // Reset visuals
        displayTitle.textContent = 'COMPTEUR PASSAGES';
        setDisplayValue(currentLaunchCount);
        updateSonometerGauge(0, true); // Reset sonometer display
        
        // Reset state
        rideState.isRunning = false;
        rideState.isCountingDown = false;
        rideState.rideEndTimeout = null; // Important de réinitialiser le minuteur
        startBtn.disabled = false;
        stopBtn.disabled = false;
    }
    
    function startCountdown() {
        if (rideState.isRunning) return;
        
        const occupiedSeats = document.querySelectorAll('.seat.occupied').length;
        if (occupiedSeats === 0) {
            alert("Impossible de lancer un manège vide !");
            return;
        }

        resetSensorStatuses(); // Réinitialise les capteurs avant de démarrer
        rideState.isRunning = true;
        rideState.isCountingDown = true;
        startBtn.disabled = true;
        
        let countdown = 10;
        displayTitle.textContent = 'COMPTE À REBOURS';
        setDisplayValue(countdown);
        console.log("Countdown started at:", countdown);
        
        rideState.countdownInterval = setInterval(() => {
            console.log("Countdown tick, current value:", countdown);
            
            if (countdown <= 1) {
                console.log("Countdown finished, starting ride simulation");
                clearInterval(rideState.countdownInterval);
                startRideSimulation(occupiedSeats);
                return;
            }
            
            countdown--;
            setDisplayValue(countdown);
            console.log("Countdown decremented to:", countdown);
        }, 1000);
    }

    function updateSonometerGauge(value, isReset = false) {
        if (isReset) {
            sonometerValue.textContent = '-- dB';
            sonometerLevel.style.width = '0%';
            return;
        }
    
        let percentage = 0;
        if (sonometerMode.isReal) {
            // Mode réel: on mappe une plage de 60-120 dB à 0-100%
            const minDb = 60;
            const maxDb = 120;
            percentage = Math.max(0, Math.min(100, ((value - minDb) / (maxDb - minDb)) * 100));
            sonometerValue.textContent = value.toFixed(1) + ' dB';
        } else {
            // Mode simulé: on mappe une plage de -25 à -5 dBFS à 0-100%
            const minSim = -25;
            const maxSim = -5;
            percentage = Math.max(0, Math.min(100, ((value - minSim) / (maxSim - minSim)) * 100));
            sonometerValue.textContent = value + ' dBFS';
        }
        sonometerLevel.style.width = percentage + '%';
    }

    function startRideSimulation(passengerCount) {
        console.log("Ride simulation started.");
        rideState.isCountingDown = false;
        
        // Démarre le moteur
        const motorSpeed = parseInt(localStorage.getItem('motorSpeed') || '150', 10);
        setMotorSpeed(motorSpeed);

        currentLaunchCount++; 
        saveLaunch(passengerCount);

        displayTitle.textContent = 'SONOMÈTRE';

        // La logique des capteurs est maintenant gérée par `fetchSensorStatus` et n'est plus liée au cycle du manège.
        // La logique du sonomètre est maintenant gérée par un intervalle global.
    }

    // --- EVENT LISTENERS ---
    startBtn.addEventListener('click', startCountdown);
    stopBtn.addEventListener('click', resetRide);

    sonometerModeToggleBtn.addEventListener('click', () => {
        sonometerMode.isReal = !sonometerMode.isReal;
        sonometerModeToggleBtn.textContent = sonometerMode.isReal ? 'Sonomètre Réel' : 'Sonomètre Simulé';
        console.log(`Basculement vers le mode sonomètre: ${sonometerMode.isReal ? 'Réel' : 'Simulé'}`);
    });

    // --- INITIALIZATION ---
    document.addEventListener('DOMContentLoaded', () => {
        resetRide();
        // Récupération de l'état des capteurs en continu
        fetchSensorStatus(); // Premier appel immédiat
        setInterval(fetchSensorStatus, 2000); // Puis toutes les 2 secondes
        
        fetchMotorStatus(); // Appel initial pour l'état du moteur
        setInterval(fetchMotorStatus, 2000); // Puis toutes les 2 secondes

        // Intervalle global pour le sonomètre
        setInterval(() => {
            const valuePromise = sonometerMode.isReal 
                ? fetchRealSonometerValue()
                : Promise.resolve(getSonometerValue());

            valuePromise.then(value => {
                // 1. Toujours mettre à jour la jauge
                updateSonometerGauge(value);

                // 2. Mettre à jour l'affichage principal si l'attraction est en cours (et non en compte à rebours)
                if (rideState.isRunning && !rideState.isCountingDown) {
                    setDisplayValue(Math.round(value));
                }
            });
        }, 1500);
    });

</script>

<?php include('../templates/footer.php'); ?>    
</body>
</html>
