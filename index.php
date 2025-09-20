<?php
// Koneksi ke database
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

// Fungsi untuk mendapatkan daftar bandara
function getAirports($pdo) {
    $stmt = $pdo->query("SELECT * FROM airports ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fungsi untuk menghitung jarak antara dua titik koordinat (Haversine formula)
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // Radius bumi dalam kilometer

    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);

    $latDelta = $lat2 - $lat1;
    $lonDelta = $lon2 - $lon1;

    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        cos($lat1) * cos($lat2) * pow(sin($lonDelta / 2), 2)));
    
    return $angle * $earthRadius;
}

// Fungsi untuk menghitung jejak karbon
function calculateCarbonFootprint($distance, $flightClass = 'economy') {
    // Faktor emisi berdasarkan kelas penerbangan (kg CO2 per km per penumpang)
    $emissionFactors = [
        'economy' => 0.085,
        'premium' => 0.130,
        'business' => 0.195,
        'first' => 0.260
    ];
    
    $factor = $emissionFactors[$flightClass] ?? $emissionFactors['economy'];
    
    // Hitung jejak karbon
    $carbon = $distance * $factor;
    
    return round($carbon, 2);
}

// Fungsi untuk mendapatkan analogi dampak karbon
function getCarbonAnalogy($carbon) {
    $analogies = [];
    
    // Setara dengan mengendarai mobil (berdasarkan emisi rata-rata 0.192 kg CO2/km)
    $carDistance = round($carbon / 0.192);
    $analogies[] = "Mengendarai mobil sejauh $carDistance km";
    
    // Setara dengan menyalakan bola lampu LED (berdasarkan 0.015 kg CO2 per jam)
    $bulbHours = round($carbon / 0.015);
    if ($bulbHours > 24) {
        $bulbDays = round($bulbHours / 24);
        $analogies[] = "Menyalakan bola lampu LED selama $bulbDays hari";
    } else {
        $analogies[] = "Menyalakan bola lampu LED selama $bulbHours jam";
    }
    
    // Setara dengan penggunaan smartphone (berdasarkan 0.008 kg CO2 per jam)
    $phoneHours = round($carbon / 0.008);
    if ($phoneHours > 24) {
        $phoneDays = round($phoneHours / 24);
        $analogies[] = "Menggunakan smartphone selama $phoneDays hari";
    } else {
        $analogies[] = "Menggunakan smartphone selama $phoneHours jam";
    }
    
    // Setara dengan menonton TV (berdasarkan 0.088 kg CO2 per jam)
    $tvHours = round($carbon / 0.088);
    if ($tvHours > 24) {
        $tvDays = round($tvHours / 24);
        $analogies[] = "Menonton TV selama $tvDays hari";
    } else {
        $analogies[] = "Menonton TV selama $tvHours jam";
    }
    
    // Setara dengan jumlah pohon yang dibutuhkan untuk menyerap CO2 (1 pohon menyerap 21.77 kg CO2 per tahun)
    $trees = round($carbon / 21.77, 1);
    $analogies[] = "Membutuhkan $trees pohon untuk menyerap CO2 tersebut dalam setahun";
    
    return $analogies;
}

// Proses form submission
$carbonResult = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $departureCode = $_POST['departure'] ?? '';
    $arrivalCode = $_POST['arrival'] ?? '';
    $flightDate = $_POST['date'] ?? '';
    $flightClass = $_POST['class'] ?? 'economy';
    $passengers = isset($_POST['passengers']) ? intval($_POST['passengers']) : 1;
    
    if (empty($departureCode) || empty($arrivalCode) || empty($flightDate)) {
        $error = "Semua field harus diisi!";
    } elseif ($departureCode === $arrivalCode) {
        $error = "Bandara keberangkatan dan tujuan tidak boleh sama!";
    } elseif ($passengers < 1) {
        $error = "Jumlah penumpang harus minimal 1!";
    } else {
        // Dapatkan data bandara
        $airports = getAirports($pdo);
        $airportData = [];
        
        foreach ($airports as $airport) {
            $airportData[$airport['iata_code']] = $airport;
        }
        
        if (!isset($airportData[$departureCode]) || !isset($airportData[$arrivalCode])) {
            $error = "Kode bandara tidak valid!";
        } else {
            $departure = $airportData[$departureCode];
            $arrival = $airportData[$arrivalCode];
            
            // Hitung jarak
            $distance = calculateDistance(
                $departure['latitude'], 
                $departure['longitude'],
                $arrival['latitude'], 
                $arrival['longitude']
            );
            
            // Hitung jejak karbon
            $carbon = calculateCarbonFootprint($distance, $flightClass);
            $totalCarbon = $carbon * $passengers;
            
            // Dapatkan analogi
            $analogies = getCarbonAnalogy($totalCarbon);
            
            $carbonResult = [
                'distance' => round($distance),
                'carbon' => $carbon,
                'total_carbon' => $totalCarbon,
                'class' => $flightClass,
                'passengers' => $passengers,
                'departure' => $departure['name'],
                'arrival' => $arrival['name'],
                'analogies' => $analogies
            ];
        }

        $passengerId = $_POST['passenger'] ?? '';

        if ($carbonResult && !empty($passengerId)) {
            $stmt = $pdo->prepare("INSERT INTO flight_logs 
                (passenger_id, departure_code, arrival_code, flight_date, flight_class, passengers, distance, carbon, total_carbon) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $passengerId,
                $departureCode,
                $arrivalCode,
                $flightDate,
                $flightClass,
                $passengers,
                $carbonResult['distance'],
                $carbonResult['carbon'],
                $carbonResult['total_carbon']
            ]);
        }
        
    }

        if ($carbonResult && !empty($passengerId)) {
            $stmt = $pdo->prepare("INSERT INTO flight_logs 
                (passenger_id, departure_code, arrival_code, flight_date, flight_class, passengers, distance, carbon, total_carbon) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $passengerId,
                $departureCode,
                $arrivalCode,
                $flightDate,
                $flightClass,
                $passengers,
                $carbonResult['distance'],
                $carbonResult['carbon'],
                $carbonResult['total_carbon']
            ]);
        
            // Simpan flag untuk alert
            $_SESSION['success'] = true;
        }
    
}

// Dapatkan daftar bandara untuk dropdown
$airports = getAirports($pdo);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Carbon Footprint Calculator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center text-green-600 mb-2">
            <i class="fas fa-plane-departure mr-2"></i>Flight Carbon Footprint Calculator
        </h1>
        <p class="text-center text-gray-600 mb-8">Hitung jejak karbon dari penerbangan Anda di Indonesia</p>
        
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6 mb-8">
            <form method="POST" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label for="departure" class="block text-sm font-medium text-gray-700">
            <i class="fas fa-plane-departure mr-1 text-blue-500"></i>Bandara Keberangkatan (Ketik Nama Kota)
        </label>
        <input type="text" id="departureInput" name="departure" 
            value="<?= $_POST['departure'] ?? '' ?>"
            placeholder="Contoh: Jakarta, Surabaya, Denpasar"
            class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
            autocomplete="off">
        <div id="departureSuggestions" class="absolute bg-white border rounded-md shadow-md mt-1 max-h-40 overflow-y-auto hidden z-10"></div>
    </div>

    <div>
        <label for="arrival" class="block text-sm font-medium text-gray-700">
            <i class="fas fa-plane-arrival mr-1 text-blue-500"></i>Bandara Tujuan (Ketik Nama Kota)
        </label>
        <input type="text" id="arrivalInput" name="arrival" 
            value="<?= $_POST['arrival'] ?? '' ?>"
            placeholder="Contoh: Medan, Makassar, Yogyakarta"
            class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
            autocomplete="off">
        <div id="arrivalSuggestions" class="absolute bg-white border rounded-md shadow-md mt-1 max-h-40 overflow-y-auto hidden z-10"></div>
    </div>
</div>

<script>
    const airports = <?= json_encode($airports) ?>;

    function setupAutocomplete(inputId, suggestionsId) {
        const input = document.getElementById(inputId);
        const suggestions = document.getElementById(suggestionsId);

        input.addEventListener('input', function () {
            const query = this.value.toLowerCase();
            suggestions.innerHTML = '';
            if (query.length < 3) {
                suggestions.classList.add('hidden');
                return;
            }

            const matches = airports.filter(a =>
                a.city.toLowerCase().includes(query) ||
                a.name.toLowerCase().includes(query) ||
                a.iata_code.toLowerCase().includes(query)
            ).slice(0, 10); // batasi max 10 hasil

            if (matches.length === 0) {
                suggestions.classList.add('hidden');
                return;
            }

            matches.forEach(a => {
                const option = document.createElement('div');
                option.className = 'p-2 cursor-pointer hover:bg-green-100';
                option.textContent = `${a.city} - ${a.name} (${a.iata_code})`;
                option.addEventListener('click', () => {
                    input.value = a.iata_code; // isi dengan IATA code
                    suggestions.classList.add('hidden');
                });
                suggestions.appendChild(option);
            });

            suggestions.classList.remove('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!suggestions.contains(e.target) && e.target !== input) {
                suggestions.classList.add('hidden');
            }
        });
    }

    setupAutocomplete('departureInput', 'departureSuggestions');
    setupAutocomplete('arrivalInput', 'arrivalSuggestions');
</script>

                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700">
                            <i class="far fa-calendar-alt mr-1 text-blue-500"></i>Tanggal Penerbangan
                        </label>
                        <input type="date" id="date" name="date" value="<?= $_POST['date'] ?? '' ?>" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
                    </div>
                    
                    <div>
                        <label for="class" class="block text-sm font-medium text-gray-700">
                            <i class="fas fa-chair mr-1 text-blue-500"></i>Kelas Penerbangan
                        </label>
                        <select id="class" name="class" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
                            <option value="economy" <?= isset($_POST['class']) && $_POST['class'] === 'economy' ? 'selected' : '' ?>>Ekonomi</option>
                            <option value="premium" <?= isset($_POST['class']) && $_POST['class'] === 'premium' ? 'selected' : '' ?>>Premium Economy</option>
                            <option value="business" <?= isset($_POST['class']) && $_POST['class'] === 'business' ? 'selected' : '' ?>>Bisnis</option>
                            <option value="first" <?= isset($_POST['class']) && $_POST['class'] === 'first' ? 'selected' : '' ?>>First Class</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="passengers" class="block text-sm font-medium text-gray-700">
                            <i class="fas fa-users mr-1 text-blue-500"></i>Jumlah Penumpang
                        </label>
                        <input type="number" id="passengers" name="passengers" min="1" value="<?= $_POST['passengers'] ?? 1 ?>" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>

                <div>
                        <label for="passenger" class="block text-sm font-medium text-gray-700">
                            <i class="fas fa-user mr-1 text-blue-500"></i>Nama Penumpang
                        </label>
                        <select id="passenger" name="passenger" required
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
                            <option value="">Pilih Penumpang</option>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM passengers ORDER BY name");
                            $passengersList = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($passengersList as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= isset($_POST['passenger']) && $_POST['passenger'] == $p['id'] ? 'selected' : '' ?>>
                                    <?= $p['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                
                <div>
                    <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition duration-200">
                        <i class="fas fa-calculator mr-2"></i>Hitung Jejak Karbon
                    </button>
                </div>
            </form>
            
            <?php if ($error): ?>
                <div class="mt-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                    <i class="fas fa-exclamation-circle mr-2"></i><?= $error ?>
                </div>
            <?php elseif ($carbonResult): ?>
                <div class="mt-6 p-6 bg-green-50 rounded-lg border border-green-200">
                    <h2 class="text-xl font-semibold text-green-800 mb-4">
                        <i class="fas fa-leaf mr-2"></i>Hasil Perhitungan
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="bg-white p-4 rounded-lg shadow-sm">
                            <p class="text-sm text-gray-600">Rute Penerbangan</p>
                            <p class="font-medium text-lg"><?= $carbonResult['departure'] ?> → <?= $carbonResult['arrival'] ?></p>
                        </div>
                        
                        <div class="bg-white p-4 rounded-lg shadow-sm">
                            <p class="text-sm text-gray-600">Jarak Tempuh</p>
                            <p class="font-medium text-lg"><?= $carbonResult['distance'] ?> km</p>
                        </div>
                        
                        <div class="bg-white p-4 rounded-lg shadow-sm">
                            <p class="text-sm text-gray-600">Kelas Penerbangan</p>
                            <p class="font-medium text-lg">
                                <?= 
                                    $carbonResult['class'] === 'economy' ? 'Ekonomi' :
                                    ($carbonResult['class'] === 'premium' ? 'Premium Economy' :
                                    ($carbonResult['class'] === 'business' ? 'Bisnis' : 'First Class'))
                                ?>
                            </p>
                        </div>
                        
                        <div class="bg-white p-4 rounded-lg shadow-sm">
                            <p class="text-sm text-gray-600">Jumlah Penumpang</p>
                            <p class="font-medium text-lg"><?= $carbonResult['passengers'] ?> orang</p>
                        </div>
                    </div>
                    
                    <div class="bg-white p-4 rounded-lg shadow-sm mb-4">
                        <p class="text-sm text-gray-600">Jejak Karbon per Penumpang</p>
                        <p class="font-medium text-xl text-green-600"><?= $carbonResult['carbon'] ?> kg CO₂</p>
                    </div>
                    
                    <div class="bg-white p-4 rounded-lg shadow-sm mb-4">
                        <p class="text-sm text-gray-600">Total Jejak Karbon</p>
                        <p class="font-medium text-2xl text-green-600"><?= $carbonResult['total_carbon'] ?> kg CO₂</p>
                    </div>
                    
                    <div class="mt-6 pt-4 border-t border-green-200">
                        <h3 class="text-lg font-semibold text-green-800 mb-3">
                            <i class="fas fa-lightbulb mr-2"></i>Dampak Jejak Karbon Ini Setara Dengan:
                        </h3>
                        <ul class="space-y-2">
                            <?php foreach ($carbonResult['analogies'] as $analogy): ?>
                                <li class="flex items-start">
                                    <i class="fas fa-arrow-right text-green-500 mt-1 mr-2"></i>
                                    <span><?= $analogy ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <div class="mt-6 pt-4 border-t border-green-200">
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-info-circle mr-1"></i>* Perhitungan berdasarkan faktor emisi dari DEFRA (Department for Environment, Food & Rural Affairs, UK) dan data lainnya.
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-green-800 mb-4">
                <i class="fas fa-database mr-2"></i>Database Bandara Indonesia
            </h2>
            <p class="text-gray-600 mb-4">Aplikasi ini mencakup <?= count($airports) ?> bandara di Indonesia:</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-60 overflow-y-auto">
                <?php foreach ($airports as $airport): ?>
                    <div class="flex items-center p-2 border rounded">
                        <i class="fas fa-plane mr-2 text-blue-500"></i>
                        <span class="text-sm"><?= $airport['name'] ?> (<?= $airport['iata_code'] ?>)</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="mt-8 text-center text-sm text-gray-500">
            <p>© 2023 Flight Carbon Footprint Calculator | Khusus Bandara Indonesia</p>
        </div>
    </div>

    <?php if (!empty($_SESSION['success'])): ?>
<script>
    alert("Data sudah dihitung dan disimpan!");
</script>
<?php unset($_SESSION['success']); endif; ?>

</body>
</html>