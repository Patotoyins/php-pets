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

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        try {
            // Fetch all pets with related data
            $stmt = $conn->query('
                SELECT 
                    Pets.PetID, 
                    Pets.Name AS PetName, 
                    Species.SpeciesName, 
                    Breeds.BreedName, 
                    Pets.DateOfBirth, 
                    Owners.Name AS OwnerName
                FROM Pets
                INNER JOIN Species ON Pets.SpeciesID = Species.SpeciesID
                INNER JOIN Breeds ON Pets.BreedID = Breeds.BreedID
                INNER JOIN Owners ON Pets.OwnerID = Owners.OwnerID
            ');
            $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($pets);
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Failed to fetch pets: ' . $e->getMessage()]);
        }
        break;

    
    case 'POST':
        try {
            // Create a new pet
            $data = json_decode(file_get_contents('php://input'), true);
            $name = $data['Name'];
            $speciesID = (int)$data['SpeciesID'];
            $breedID = (int)$data['BreedID'];
            $dateOfBirth = $data['DateOfBirth'];
            $ownerID = (int)$data['OwnerID'];

            // Validate inputs
            if (empty($name) || empty($speciesID) || empty($breedID) || empty($dateOfBirth) || empty($ownerID)) {
                echo json_encode(['error' => 'All fields are required']);
                exit();
            }

            // Prepare and execute the insert query
            $stmt = $conn->prepare("INSERT INTO Pets (Name, SpeciesID, BreedID, DateOfBirth, OwnerID) VALUES (:name, :speciesID, :breedID, :dateOfBirth, :ownerID)");
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':speciesID', $speciesID, PDO::PARAM_INT);
            $stmt->bindParam(':breedID', $breedID, PDO::PARAM_INT);
            $stmt->bindParam(':dateOfBirth', $dateOfBirth, PDO::PARAM_STR);
            $stmt->bindParam(':ownerID', $ownerID, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo json_encode(['success' => 'Pet added successfully']);
            } else {
                $errorInfo = $stmt->errorInfo();
                echo json_encode(['error' => 'Failed to add pet', 'details' => $errorInfo]);
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
