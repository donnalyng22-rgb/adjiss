<?php
/* ==========================================================
   DB CONNECT
   Ito yung iisang koneksyon sa MySQL na gagamitin ng lahat
   ng ibang PHP file (get, add, update, delete, contact).
   I-adjust lang dito kung iba ang username/password mo sa
   XAMPP (default: root, walang password).
========================================================== */

$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "adjiss_db";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    http_response_code(500);
    header("Content-Type: application/json");
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed: " . $conn->connect_error
    ]);
    exit;
}

$conn->set_charset("utf8mb4");
