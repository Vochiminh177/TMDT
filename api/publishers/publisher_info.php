<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/models/NhaXuatBan.php';
require_once __DIR__ . '/../../app/models/Sach.php';

$nhaxuatban_model = new app_models_NhaXuatBan();

$maNXB = isset($_GET['maNXB']) ? $_GET['maNXB'] : null;

if ($maNXB) {
    $publisher_info = $nhaxuatban_model->getPublisherInfo($maNXB);
    echo json_encode($publisher_info);
} else {
    echo json_encode(['error' => 'maNXB is required']);
}
