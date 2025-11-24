<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/models/Sach.php';

$sach_model = new app_models_Sach();

$maTheLoai = isset($_GET['maTheLoai']) ? $_GET['maTheLoai'] : null;
$maSach = isset($_GET['maSach']) ? $_GET['maSach'] : null;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;

if ($maTheLoai && $maSach) {
    $same_category_books = $sach_model->getSameCategoryBooks($maTheLoai, $maSach, $limit);
    echo json_encode($same_category_books);
} else {
    echo json_encode(['error' => 'maTheLoai and maSach are required']);
}
