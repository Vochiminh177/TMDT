<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/models/TheLoai.php';
require_once __DIR__ . '/../../app/models/Sach.php';

$theloai_model = new app_models_TheLoai();

$maTheLoai = isset($_GET['maTheLoai']) ? $_GET['maTheLoai'] : null;

if ($maTheLoai) {
    $category_info = $theloai_model->getCategoryInfo($maTheLoai);
    echo json_encode($category_info);
} else {
    echo json_encode(['error' => 'maTheLoai is required']);
}
