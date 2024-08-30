<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

include "connection.php"; // Ensure this connection uses PDO

// Handle different request methods
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        try {
            // Fetch all breeds with their associated species name
            $stmt = $conn->query('
                SELECT Breeds.BreedID, Breeds.BreedName, Species.SpeciesName 
                FROM Breeds 
                INNER JOIN Species ON Breeds.SpeciesID = Species.SpeciesID
            ');
            $breeds = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($breeds);
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Failed to fetch breeds: ' . $e->getMessage()]);
        }
        break;
    
    case 'POST':
        try {
            // Create a new breed
            $data = json_decode(file_get_contents('php://input'), true);
            $breedName = $data['BreedName'];
            $speciesID = (int)$data['SpeciesID'];

            // Validate inputs
            if (empty($breedName) || empty($speciesID)) {
                echo json_encode(['error' => 'Breed name and SpeciesID are required']);
                exit();
            }

            // Check if the SpeciesID exists in the Species table
            $stmt = $conn->prepare("SELECT COUNT(*) FROM Species WHERE SpeciesID = :speciesID");
            $stmt->bindParam(':speciesID', $speciesID, PDO::PARAM_INT);
            $stmt->execute();
            $speciesExists = $stmt->fetchColumn();

            if (!$speciesExists) {
                echo json_encode(['error' => 'Invalid SpeciesID']);
                exit();
            }

            // Prepare and execute the insert query
            $stmt = $conn->prepare("INSERT INTO Breeds (BreedName, SpeciesID) VALUES (:breedName, :speciesID)");
            $stmt->bindParam(':breedName', $breedName, PDO::PARAM_STR);
            $stmt->bindParam(':speciesID', $speciesID, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo json_encode(['success' => 'Breed added successfully']);
            } else {
                $errorInfo = $stmt->errorInfo();
                echo json_encode(['error' => 'Failed to add breed', 'details' => $errorInfo]);
            }
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

$conn = null; // Close the connection
?>
