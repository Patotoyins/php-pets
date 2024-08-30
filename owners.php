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
            // Fetch all owners
            $stmt = $conn->query('SELECT * FROM Owners');
            $owners = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($owners);
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Failed to fetch owners: ' . $e->getMessage()]);
        }
        break;
    
    case 'POST':
        try {
            // Create a new owner
            $data = json_decode(file_get_contents('php://input'), true);
            $name = $data['Name'];
            $contactDetails = $data['ContactDetails'];
            $address = $data['Address'];

            // Validate inputs
            if (empty($name) || empty($contactDetails) || empty($address)) {
                echo json_encode(['error' => 'All fields are required']);
                exit();
            }

            // Prepare and execute the insert query
            $stmt = $conn->prepare("INSERT INTO Owners (Name, ContactDetails, Address) VALUES (:name, :contactDetails, :address)");
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':contactDetails', $contactDetails, PDO::PARAM_STR);
            $stmt->bindParam(':address', $address, PDO::PARAM_STR);

            if ($stmt->execute()) {
                echo json_encode(['success' => 'Owner added successfully']);
            } else {
                $errorInfo = $stmt->errorInfo();
                echo json_encode(['error' => 'Failed to add owner', 'details' => $errorInfo]);
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
