<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/models/TacGia.php';
require_once __DIR__ . '/../../app/models/Sach.php';

$tacgia_model = new app_models_TacGia();

$maTacGia = isset($_GET['maTacGia']) ? $_GET['maTacGia'] : null;

if ($maTacGia) {
    $author_info = $tacgia_model->getAuthorInfo($maTacGia);
    echo json_encode($author_info);
} else {
    echo json_encode(['error' => 'maTacGia is required']);
}
