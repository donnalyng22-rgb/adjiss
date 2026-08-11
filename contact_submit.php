<?php
/* ==========================================================
   CONTACT SUBMIT
   Iniimbak sa contact_messages table ang mga mensaheng
   ipinapadala gamit ang "Contact Us" form.
========================================================== */

header("Content-Type: application/json");
require "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed."]);
    exit;
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$subject = trim($_POST["subject"] ?? "");
$message = trim($_POST["message"] ?? "");

$errors = [];

if ($name === "") {
    $errors["name"] = "Name is required.";
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors["email"] = "Please enter a valid email address.";
}
if ($subject === "") {
    $errors["subject"] = "Subject is required.";
}
if ($message === "" || mb_strlen($message) > 500) {
    $errors["message"] = "Message is required and must be 500 characters or fewer.";
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(["success" => false, "message" => "Validation failed.", "errors" => $errors]);
    exit;
}

$stmt = $conn->prepare(
    "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("ssss", $name, $email, $subject, $message);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Thanks {$name}! We received your message about \"{$subject}\" and will get back to you soon."
    ]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to send message: " . $conn->error]);
}

$stmt->close();
$conn->close();
