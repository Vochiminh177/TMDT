<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/models/Sach.php';

$sach_model = new app_models_Sach();
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 8;
$discounted_books = $sach_model->getDiscountedBooks($limit);

echo json_encode($discounted_books);