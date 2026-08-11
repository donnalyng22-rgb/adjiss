<?php
/* ==========================================================
   UPDATE CUSTOMER
   Ginagamit pag pinindot ang "Update Customer" button
   (matapos i-click ang Edit sa table). Kailangan ang "id"
   para malaman kung aling record ang babaguhin - kagaya
   ng ginagawa ng editingId sa script.js.
========================================================== */

header("Content-Type: application/json");
require "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed."]);
    exit;
}

$id = isset($_POST["id"]) ? (int) $_POST["id"] : 0;
$fullName = trim($_POST["full_name"] ?? "");
$email = trim($_POST["email"] ?? "");
$contactNumber = trim($_POST["contact_number"] ?? "");
$address = trim($_POST["address"] ?? "");

$errors = [];

if ($id <= 0) {
    $errors["id"] = "Invalid customer ID.";
}
if ($fullName === "") {
    $errors["full_name"] = "Full name is required.";
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors["email"] = "Please enter a valid email address.";
}
if (!preg_match("/^09\d{9}$/", $contactNumber)) {
    $errors["contact_number"] = "Contact number must be 11 digits starting with 09.";
}
if ($address === "") {
    $errors["address"] = "Address is required.";
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(["success" => false, "message" => "Validation failed.", "errors" => $errors]);
    exit;
}

/* ==========================================================
   OPTIONAL: bagong profile image
   Kung walang bagong na-upload, itatago na lang yung
   dati nang larawan (hindi babaguhin ang column).
========================================================== */

$newImageSql = "";
$profileImagePath = null;

if (isset($_FILES["profile_image"]) && $_FILES["profile_image"]["error"] === UPLOAD_ERR_OK) {

    $allowedTypes = ["image/jpeg", "image/png", "image/webp", "image/gif"];
    $fileType = mime_content_type($_FILES["profile_image"]["tmp_name"]);

    if (in_array($fileType, $allowedTypes)) {

        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION);
        $fileName = "customer_" . uniqid() . "." . $extension;
        $destination = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $destination)) {
            $profileImagePath = $destination;
        }
    }
}

if ($profileImagePath !== null) {
    $stmt = $conn->prepare(
        "UPDATE customers
         SET full_name = ?, email = ?, contact_number = ?, address = ?, profile_image = ?
         WHERE id = ?"
    );
    $stmt->bind_param("sssssi", $fullName, $email, $contactNumber, $address, $profileImagePath, $id);
} else {
    $stmt = $conn->prepare(
        "UPDATE customers
         SET full_name = ?, email = ?, contact_number = ?, address = ?
         WHERE id = ?"
    );
    $stmt->bind_param("ssssi", $fullName, $email, $contactNumber, $address, $id);
}

if ($stmt->execute()) {

    if ($stmt->affected_rows === 0) {
        // pwedeng dahil walang record sa ID, o walang aktwal na nabago
        echo json_encode(["success" => true, "message" => "No changes made or customer not found."]);
    } else {
        echo json_encode(["success" => true, "message" => "Customer updated successfully!"]);
    }

} else {
    http_response_code(500);
    if ($conn->errno === 1062) {
        echo json_encode(["success" => false, "message" => "Another customer already uses that email."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update customer: " . $conn->error]);
    }
}

$stmt->close();
$conn->close();
