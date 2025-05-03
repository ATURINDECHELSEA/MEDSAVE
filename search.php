<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medsave_database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get search query
$search = isset($_GET['q']) ? $_GET['q'] : '';
$search = $conn->real_escape_string($search);

// Search across different tables
$results = array();

// Search in doctors table
$sql = "SELECT 'doctor' as type, name, specialization, 'doctor_profile.php?id=' as link, id 
        FROM doctors 
        WHERE name LIKE '%$search%' 
        OR specialization LIKE '%$search%'";
$result = $conn->query($sql);
if ($result) {
    while($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
}

// Search in services table
$sql = "SELECT 'service' as type, name, description, 'services.php?id=' as link, id 
        FROM services 
        WHERE name LIKE '%$search%' 
        OR description LIKE '%$search%'";
$result = $conn->query($sql);
if ($result) {
    while($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
}

// Search in medicines table
$sql = "SELECT 'medicine' as type, name, description, 'medicine_details.php?id=' as link, id 
        FROM medicines 
        WHERE name LIKE '%$search%' 
        OR description LIKE '%$search%'";
$result = $conn->query($sql);
if ($result) {
    while($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - MEDSAVE</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link rel="stylesheet" href="index.css" />
    <style>
        .search-result {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .search-result:hover {
            transform: translateY(-2px);
        }
        .result-type {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 5px;
        }
        .result-title {
            color: #205295;
            margin-bottom: 10px;
        }
        .no-results {
            text-align: center;
            padding: 40px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <!-- Video Background -->
    <video autoplay muted loop class="video-background">
        <source src="Video 1.mp4" type="video/mp4" />
    </video>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark bg-opacity-75">
        <div class="container">
            <a class="navbar-brand" href="index.html" style="color: red;">
                 MEDSAVE
            </a>
            <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.html">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="sevices.html">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.html">Contact Us</a></li>
                </ul>
                <form class="d-flex" action="search.php" method="GET">
                    <input class="form-control me-2" type="search" name="q" placeholder="Search MEDSAVE" value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn btn-outline-light" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container py-5">
        <h1 class="text-center page-title">Search Results</h1>
        
        <?php if (empty($search)): ?>
            <div class="no-results">
                <h3>Please enter a search term</h3>
            </div>
        <?php elseif (empty($results)): ?>
            <div class="no-results">
                <h3>No results found for "<?php echo htmlspecialchars($search); ?>"</h3>
                <p>Try different keywords or check your spelling</p>
            </div>
        <?php else: ?>
            <div class="search-results">
                <?php foreach ($results as $result): ?>
                    <div class="search-result">
                        <div class="result-type">
                            <i class="fas fa-<?php 
                                echo $result['type'] === 'doctor' ? 'user-md' : 
                                    ($result['type'] === 'service' ? 'flask' : 'pills'); 
                            ?>"></i>
                            <?php echo ucfirst($result['type']); ?>
                        </div>
                        <h3 class="result-title">
                            <a href="<?php echo $result['link'] . $result['id']; ?>" class="text-decoration-none">
                                <?php echo htmlspecialchars($result['name']); ?>
                            </a>
                        </h3>
                        <?php if (isset($result['description'])): ?>
                            <p><?php echo htmlspecialchars($result['description']); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5 style="color: red;">MEDSAVE</h5>
                    <p>Your trusted partner in healthcare services.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>&copy; 2025 MEDSAVE. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 