<?php
/* ==========================================================
   GET CUSTOMERS
   Ibinabalik lahat ng customer records bilang JSON, kasing-
   hugis ng "customers" array na dati nasa script.js lang
   (in-memory). Suportado rin ang ?search= at ?sort=&dir=
   para hindi na kailangang gawin sa JS side ang filtering.

   Halimbawa:
   get_customers.php
   get_customers.php?search=juan
   get_customers.php?sort=full_name&dir=asc
========================================================== */

header("Content-Type: application/json");
require "db_connect.php";

$allowedSortColumns = ["id", "full_name", "email", "contact_number", "address", "created_at"];

$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";
$sortColumn = isset($_GET["sort"]) && in_array($_GET["sort"], $allowedSortColumns) ? $_GET["sort"] : "id";
$sortDir = (isset($_GET["dir"]) && strtolower($_GET["dir"]) === "desc") ? "DESC" : "ASC";

$sql = "SELECT id, full_name, email, contact_number, address, profile_image, created_at
        FROM customers";

$params = [];
$types = "";

if ($search !== "") {
    $sql .= " WHERE full_name LIKE ? OR email LIKE ?";
    $likeTerm = "%" . $search . "%";
    $params[] = $likeTerm;
    $params[] = $likeTerm;
    $types .= "ss";
}

// safe dahil galing sa whitelist ($allowedSortColumns), hindi direktang user input
$sql .= " ORDER BY {$sortColumn} {$sortDir}";

$stmt = $conn->prepare($sql);

if ($types !== "") {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$customers = [];

while ($row = $result->fetch_assoc()) {
    $customers[] = [
        "id" => (int) $row["id"],
        "fullName" => $row["full_name"],
        "email" => $row["email"],
        "contactNumber" => $row["contact_number"],
        "address" => $row["address"],
        "image" => $row["profile_image"],
        "createdAt" => $row["created_at"]
    ];
}

echo json_encode([
    "success" => true,
    "count" => count($customers),
    "customers" => $customers
]);

$stmt->close();
$conn->close();
