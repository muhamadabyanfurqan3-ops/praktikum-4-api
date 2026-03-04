<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["message" => "Method tidak diizinkan."]);
    exit;
}

include_once '../config/Database.php';
include_once '../models/Mahasiswa.php';

$database = new Database();
$db = $database->getConnection();

$mahasiswa = new Mahasiswa($db);

if(isset($_GET['keyword']) && !empty($_GET['keyword'])) {

    $stmt = $mahasiswa->search($_GET['keyword']);
    $num = $stmt->rowCount();

    if($num > 0) {
        $result = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $result[] = [
                "id" => $id,
                "npm" => $npm,
                "nama" => $nama,
                "jurusan" => $jurusan
            ];
        }

        http_response_code(200);
        echo json_encode($result);

    } else {
        http_response_code(404);
        echo json_encode(["message" => "Data tidak ditemukan."]);
    }

} else {
    http_response_code(400);
    echo json_encode(["message" => "Parameter keyword wajib diisi."]);
}

?>