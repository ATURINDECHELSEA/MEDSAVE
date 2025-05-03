<?php
require_once 'config/database.php';

try {
    $conn = getDBConnection();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>MEDSAVE Database Documentation</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            .table-container {
                margin: 20px 0;
                padding: 20px;
                background: #f8f9fa;
                border-radius: 8px;
            }
            .table-title {
                color: #0d6efd;
                margin-bottom: 15px;
            }
            .column-info {
                font-size: 0.9em;
                color: #666;
            }
            .foreign-key {
                color: #198754;
            }
            .primary-key {
                color: #dc3545;
            }
        </style>
    </head>
    <body>
        <div class="container py-5">
            <h1 class="mb-4">MEDSAVE Database Documentation</h1>
            
            <div class="alert alert-info">
                <h4>Database Overview</h4>
                <p>This database manages users, appointments, and healthcare services for the MEDSAVE platform.</p>
            </div>

            <?php
            // Get all tables
            $tables = [
                'users' => 'Stores user information for both patients and doctors',
                'doctor_profiles' => 'Contains additional information specific to doctors',
                'patient_profiles' => 'Contains additional information specific to patients',
                'appointments' => 'Manages medical appointments between doctors and patients',
                'ratings' => 'Stores patient ratings and feedback for doctors',
                'services' => 'Lists available medical services',
                'doctor_services' => 'Links doctors with the services they provide'
            ];

            foreach ($tables as $table => $description) {
                // Get table structure
                $columns = $conn->query("SHOW COLUMNS FROM $table");
                ?>
                <div class="table-container">
                    <h3 class="table-title"><?php echo ucfirst($table); ?></h3>
                    <p class="text-muted"><?php echo $description; ?></p>
                    
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Column</th>
                                <th>Type</th>
                                <th>Null</th>
                                <th>Key</th>
                                <th>Default</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($column = $columns->fetch(PDO::FETCH_ASSOC)) { ?>
                                <tr>
                                    <td>
                                        <?php 
                                        echo $column['Field'];
                                        if ($column['Key'] == 'PRI') {
                                            echo ' <span class="badge bg-danger">PK</span>';
                                        }
                                        if (strpos($column['Field'], '_id') !== false && $column['Key'] != 'PRI') {
                                            echo ' <span class="badge bg-success">FK</span>';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo $column['Type']; ?></td>
                                    <td><?php echo $column['Null']; ?></td>
                                    <td><?php echo $column['Key']; ?></td>
                                    <td><?php echo $column['Default'] ?? 'NULL'; ?></td>
                                    <td class="column-info">
                                        <?php
                                        // Add descriptions for important columns
                                        switch ($table) {
                                            case 'users':
                                                if ($column['Field'] == 'user_type') {
                                                    echo "Type of user (patient/doctor)";
                                                } elseif ($column['Field'] == 'email') {
                                                    echo "Unique email address for login";
                                                }
                                                break;
                                            case 'appointments':
                                                if ($column['Field'] == 'status') {
                                                    echo "Appointment status (scheduled/completed/cancelled)";
                                                }
                                                break;
                                            case 'ratings':
                                                if ($column['Field'] == 'rating') {
                                                    echo "Rating from 1 to 5 stars";
                                                }
                                                break;
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <?php
                    // Show sample data (first 5 rows)
                    $sampleData = $conn->query("SELECT * FROM $table LIMIT 5");
                    if ($sampleData->rowCount() > 0) {
                        ?>
                        <h4 class="mt-4">Sample Data</h4>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <?php
                                        $firstRow = $sampleData->fetch(PDO::FETCH_ASSOC);
                                        foreach ($firstRow as $key => $value) {
                                            echo "<th>$key</th>";
                                        }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <?php
                                        foreach ($firstRow as $value) {
                                            echo "<td>" . (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) . "</td>";
                                        }
                                        ?>
                                    </tr>
                                    <?php
                                    while ($row = $sampleData->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<tr>";
                                        foreach ($row as $value) {
                                            echo "<td>" . (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) . "</td>";
                                        }
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
            ?>

            <div class="alert alert-success mt-4">
                <h4>Database Relationships</h4>
                <ul>
                    <li>Users can be either patients or doctors (user_type field)</li>
                    <li>Doctor profiles are linked to users through doctor_id</li>
                    <li>Patient profiles are linked to users through patient_id</li>
                    <li>Appointments connect patients and doctors</li>
                    <li>Ratings are given by patients to doctors</li>
                    <li>Doctor services link doctors with the services they provide</li>
                </ul>
            </div>
        </div>
    </body>
    </html>
    <?php
} catch (PDOException $e) {
    echo "<h2>Error: " . $e->getMessage() . "</h2>";
}
?> 