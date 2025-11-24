<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/models/Sach.php';

$sach_model = new app_models_Sach();

$maTacGia = isset($_GET['maTacGia']) ? $_GET['maTacGia'] : null;
$maSach = isset($_GET['maSach']) ? $_GET['maSach'] : null;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;

if ($maTacGia && $maSach) {
    $same_author_books = $sach_model->getSameAuthorBooks($maTacGia, $maSach, $limit);
    echo json_encode($same_author_books);
} else {
    echo json_encode(['error' => 'maTacGia and maSach are required']);
}
