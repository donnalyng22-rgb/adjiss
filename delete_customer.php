<?php
/* ==========================================================
   DELETE CUSTOMER
   Tinatawag pag pinindot ang "Delete" sa delete-confirmation
   modal. Tumatanggap ng "id" bilang POST field.
========================================================== */

header("Content-Type: application/json");
require "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed."]);
    exit;
}

$id = isset($_POST["id"]) ? (int) $_POST["id"] : 0;

if ($id <= 0) {
    http_response_code(422);
    echo json_encode(["success" => false, "message" => "Invalid customer ID."]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM customers WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    if ($stmt->affected_rows === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Customer not found."]);
    } else {
        echo json_encode(["success" => true, "message" => "Customer deleted."]);
    }

} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to delete customer: " . $conn->error]);
}

$stmt->close();
$conn->close();
