<?php

function generate_stats_data($attractions) {
    $stats = [];
    $today = new DateTime();

    foreach (array_keys($attractions) as $attraction_id) {
        $stats[$attraction_id] = [];
        for ($i = 0; $i < 365; $i++) {
            $date = (clone $today)->sub(new DateInterval("P{$i}D"));
            $date_string = $date->format('Y-m-d');
            
            // Simuler plus de monde le week-end
            $is_weekend = in_array($date->format('N'), [6, 7]);
            
            $launches = rand($is_weekend ? 50 : 20, $is_weekend ? 100 : 60);
            $total_visitors = 0;
            $total_seats_occupied = 0;
            $sonometer_readings = [];

            for ($j=0; $j < $launches; $j++) {
                $seats_occupied = rand(2, $attractions[$attraction_id]['seats']);
                $total_visitors += $seats_occupied;
                $total_seats_occupied += $seats_occupied;
            }

            for ($k=0; $k < 60; $k++) { // 60 lectures par jour
                 $sonometer_readings[] = rand(-25, -5);
            }
            
            $stats[$attraction_id][$date_string] = [
                'visitors' => $total_visitors,
                'launches' => $launches,
                'avg_seats' => $launches > 0 ? round($total_seats_occupied / $launches, 2) : 0,
                'sonometer_avg' => count($sonometer_readings) > 0 ? round(array_sum($sonometer_readings) / count($sonometer_readings), 2) : -15,
                'sonometer_readings' => $sonometer_readings
            ];
        }
    }
    return $stats;
}

require_once('attractions_data.php');
$fake_stats = generate_stats_data($attractions); 