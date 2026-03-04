<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

include_once '../config/Database.php';
include_once '../models/Mahasiswa.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method tidak diizinkan"]);
    exit;
}

$database = new Database();
$db = $database->getConnection();
$mahasiswa = new Mahasiswa($db);

$keyword = $_GET['keyword'] ?? null;
$jurusan = $_GET['jurusan'] ?? null;
$sort = $_GET['sort'] ?? 'id';
$order = $_GET['order'] ?? 'DESC';

$page = isset($_GET['page']) ? (int)$_GET['page'] : null;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;

$offset = null;
if ($page && $limit) {
    $offset = ($page - 1) * $limit;
}

$stmt = $mahasiswa->searchFilter($keyword, $jurusan, $sort, $order, $limit, $offset);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($data) {
    echo json_encode([
        "status" => "success",
        "total" => count($data),
        "data" => $data
    ]);
} else {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "message" => "Data tidak ditemukan"
    ]);
}