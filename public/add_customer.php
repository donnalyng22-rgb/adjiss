<?php
/* ==========================================================
   ADD CUSTOMER
   Tinatanggap ang POST mula sa "Add New Customer" form.
   Parehong validation dito gaya ng nasa script.js (email
   format at 09XXXXXXXXX na contact number) - importante
   ito dahil hindi dapat umasa sa JS validation lang ang
   server, baka i-bypass ng iba.
========================================================== */

header("Content-Type: application/json");
require "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed."]);
    exit;
}

$fullName = trim($_POST["full_name"] ?? "");
$email = trim($_POST["email"] ?? "");
$contactNumber = trim($_POST["contact_number"] ?? "");
$address = trim($_POST["address"] ?? "");

$errors = [];

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
   PROFILE IMAGE UPLOAD (optional)
   Kung walang na-upload, gagamitin na lang ang default
   profile image, gaya ng ginagawa sa script.js.
========================================================== */

$profileImagePath = "images/default-profile.png";

if (isset($_FILES["profile_image"]) && $_FILES["profile_image"]["error"] === UPLOAD_ERR_OK) {

    $allowedTypes = ["image/jpeg", "image/png", "image/webp", "image/gif"];
    $fileType = mime_content_type($_FILES["profile_image"]["tmp_name"]);

    if (!in_array($fileType, $allowedTypes)) {
        http_response_code(422);
        echo json_encode(["success" => false, "message" => "Profile image must be JPG, PNG, WEBP, or GIF."]);
        exit;
    }

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

/* ==========================================================
   INSERT
========================================================== */

$stmt = $conn->prepare(
    "INSERT INTO customers (full_name, email, contact_number, address, profile_image)
     VALUES (?, ?, ?, ?, ?)"
);
$stmt->bind_param("sssss", $fullName, $email, $contactNumber, $address, $profileImagePath);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Customer added successfully!",
        "id" => $stmt->insert_id
    ]);
} else {
    http_response_code(500);
    // Duplicate email hits the UNIQUE constraint on the customers table
    if ($conn->errno === 1062) {
        echo json_encode(["success" => false, "message" => "A customer with that email already exists."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to add customer: " . $conn->error]);
    }
}

$stmt->close();
$conn->close();
