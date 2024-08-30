<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {

    header("HTTP/1.1 200 OK");
    exit();
}


include "connection.php";


$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        try {
        
            $stmt = $conn->query('SELECT * FROM Species');
            $species = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($species);
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Failed to fetch species: ' . $e->getMessage()]);
        }
        break;
    
        case 'POST':
            try {
                include "connection.php";
                
                $data = json_decode(file_get_contents('php://input'), true);
                $speciesName = $data['SpeciesName'];
        
                $stmt = $conn->prepare("INSERT INTO Species (SpeciesName) VALUES (:speciesName)");
                $stmt->bindParam(':speciesName', $speciesName, PDO::PARAM_STR);
        
                if ($stmt->execute()) {
                    echo json_encode(['success' => 'Species added successfully']);
                } else {
                    
                    $errorInfo = $stmt->errorInfo();
                    echo json_encode(['error' => 'Failed to add species', 'details' => $errorInfo]);
                }
            } catch (PDOException $e) {
                echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
            }
            break;
        

    default:
        echo json_encode(['error' => 'Method not allowed']);
        break;
}


$conn = null;
?>
