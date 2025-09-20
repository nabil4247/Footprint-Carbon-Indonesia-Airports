<?php
$host = 'localhost';
$dbname = 'carbon_footprint';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// Ambil data total karbon per penumpang
$stmt = $pdo->query("
    SELECT p.name, SUM(f.total_carbon) as total_carbon
    FROM flight_logs f
    JOIN passengers p ON f.passenger_id = p.id
    GROUP BY p.id
    ORDER BY total_carbon DESC
");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil data detail riwayat perjalanan
$stmtLogs = $pdo->query("
    SELECT f.*, p.name as passenger_name, 
           a1.city as dep_city, a1.name as dep_name, 
           a2.city as arr_city, a2.name as arr_name
    FROM flight_logs f
    JOIN passengers p ON f.passenger_id = p.id
    JOIN airports a1 ON f.departure_code = a1.iata_code
    JOIN airports a2 ON f.arrival_code = a2.iata_code
    ORDER BY f.flight_date DESC, f.created_at DESC
");
$logs = $stmtLogs->fetchAll(PDO::FETCH_ASSOC);

$names = array_column($data, 'name');
$carbons = array_column($data, 'total_carbon');

function getCarbonAnalogy($carbon) {
    $analogies = [];

    // Mobil (0.192 kg CO2/km)
    $carDistance = round($carbon / 0.192);
    $analogies[] = "Mengendarai mobil sejauh {$carDistance} km";

    // Motor (0.103 kg CO2/km)
    $bikeDistance = round($carbon / 0.103);
    $analogies[] = "Mengendarai motor sejauh {$bikeDistance} km";

    // Lampu LED (0.015 kg CO2/jam)
    $bulbHours = round($carbon / 0.015);
    if ($bulbHours > 24) {
        $bulbDays = round($bulbHours / 24);
        $analogies[] = "Menyalakan bola lampu LED selama {$bulbDays} hari";
    } else {
        $analogies[] = "Menyalakan bola lampu LED selama {$bulbHours} jam";
    }

    // Smartphone charging (0.008 kg CO2/jam)
    $phoneHours = round($carbon / 0.008);
    $analogies[] = "Mengisi daya smartphone selama {$phoneHours} jam";

    // TV (0.088 kg CO2/jam)
    $tvHours = round($carbon / 0.088);
    $analogies[] = "Menonton TV selama {$tvHours} jam";

    // Kopi (0.21 kg CO2/cangkir)
    $cups = round($carbon / 0.21);
    $analogies[] = "Setara dengan membuat {$cups} cangkir kopi";

    // Daging sapi (27 kg CO2/kg)
    $beefKg = round($carbon / 27, 1);
    $analogies[] = "Setara dengan mengonsumsi {$beefKg} kg daging sapi";

    // Plastik sekali pakai (6 kg CO2/kg)
    $plasticKg = round($carbon / 6, 1);
    $analogies[] = "Setara dengan memproduksi {$plasticKg} kg plastik sekali pakai";

    // 🌙 Perjalanan ke Bulan (384.400 km)
    $moonTrips = round($carDistance / 384400, 2);
    $analogies[] = "Setara dengan {$moonTrips} kali perjalanan sejauh Bumi ke Bulan";

    // ☀️ Perjalanan ke Matahari (149.600.000 km)
    $sunTrips = round($carDistance / 149600000, 4);
    $analogies[] = "Setara dengan {$sunTrips} kali perjalanan sejauh Bumi ke Matahari";

    // Pohon (21.77 kg CO2/tahun)
    $trees = round($carbon / 21.77, 1);
    $analogies[] = "Membutuhkan {$trees} pohon untuk menyerap CO₂ dalam setahun";

    return $analogies;
}



// bangun data analogi per penumpang dari hasil agregasi
$analogyData = [];
foreach ($data as $d) {
    $name = $d['name'];
    $total = (float)$d['total_carbon'];
    $analogyData[$name] = getCarbonAnalogy($total);
}
?>



<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Jejak Karbon</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-6 py-8">
        <h1 class="text-3xl font-bold text-green-600 mb-6 flex items-center">
            <i class="fas fa-chart-line mr-2"></i> Dashboard Jejak Karbon
        </h1>

        <!-- Grafik -->
       <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Grafik -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">
                    <i class="fas fa-bar-chart mr-2 text-green-500"></i>Total Jejak Karbon per Penumpang
                </h2>
                <canvas id="carbonChart" height="120"></canvas>
            </div>

            <!-- Analogi -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">
                    <i class="fas fa-lightbulb mr-2 text-yellow-500"></i>Dampak Jejak Karbon Ini Setara Dengan
                </h2>
                <div class="space-y-4 max-h-96 overflow-y-auto">
                    <?php foreach ($analogyData as $name => $analogs): ?>
                        <div class="border rounded-lg p-4 hover:shadow-md transition">
                            <p class="font-semibold text-green-700 mb-2"><i class="fas fa-user mr-1"></i><?= htmlspecialchars($name) ?></p>
                            <ul class="list-disc pl-5 text-sm text-gray-700 space-y-1">
                                <?php foreach ($analogs as $a): ?>
                                    <li><?= $a ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>


        <!-- Tabel Riwayat -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold text-gray-700 mb-4"><i class="fas fa-table mr-2 text-green-500"></i>Riwayat Perjalanan</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 text-sm">
                    <thead class="bg-green-600 text-white">
                        <tr>
                            <th class="px-4 py-2 border">Tanggal</th>
                            <th class="px-4 py-2 border">Penumpang</th>
                            <th class="px-4 py-2 border">Rute</th>
                            <th class="px-4 py-2 border">Kelas</th>
                            <th class="px-4 py-2 border">Penumpang</th>
                            <th class="px-4 py-2 border">Jarak (km)</th>
                            <th class="px-4 py-2 border">Total Karbon (kg CO₂)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($logs) > 0): ?>
                            <?php foreach ($logs as $log): ?>
                                <tr class="hover:bg-green-50">
                                    <td class="px-4 py-2 border"><?= htmlspecialchars($log['flight_date']) ?></td>
                                    <td class="px-4 py-2 border font-medium"><?= htmlspecialchars($log['passenger_name']) ?></td>
                                    <td class="px-4 py-2 border">
                                        <?= htmlspecialchars($log['dep_city']) ?> (<?= $log['departure_code'] ?>) → 
                                        <?= htmlspecialchars($log['arr_city']) ?> (<?= $log['arrival_code'] ?>)
                                    </td>
                                    <td class="px-4 py-2 border capitalize"><?= htmlspecialchars($log['flight_class']) ?></td>
                                    <td class="px-4 py-2 border text-center"><?= $log['passengers'] ?></td>
                                    <td class="px-4 py-2 border text-right"><?= number_format($log['distance']) ?></td>
                                    <td class="px-4 py-2 border text-right text-green-600 font-semibold"><?= number_format($log['total_carbon'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="px-4 py-4 border text-center text-gray-500">Belum ada data perjalanan</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script>
    const ctx = document.getElementById('carbonChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($names) ?>,
            datasets: [{
                label: 'Total Jejak Karbon (kg CO₂)',
                data: <?= json_encode($carbons) ?>,
                backgroundColor: 'rgba(34,197,94,0.7)',
                borderColor: 'rgba(22,163,74,1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
    </script>
</body>
</html>
