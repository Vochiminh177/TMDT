<?php

require_once __DIR__ . '../../../app/config.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Nhận dữ liệu từ POST
$inputTicketId = isset($_POST['inputTicketId']) ? $_POST['inputTicketId'] : '1';
$bookId = isset($_POST['bookId']) ? $_POST['bookId'] : '1';
$price = isset($_POST['price']) ? $_POST['price'] : 0;
$quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 0;

// Kiểm tra inputTicketId và bookId có hợp lệ không
if (empty($inputTicketId)) {
    echo json_encode(["success" => false, "message" => "Thiếu ID phiếu nhập."]);
    exit;
}
if (empty($bookId)) {
    echo json_encode(["success" => false, "message" => "Thiếu ID sách."]);
    exit;
}


try {
    // Khởi tạo model tác giả
    $model = new app_models_ChiTietPhieuNhap();

    // Cập nhật trạng thái tác giả trong database
    $result = $model->insertInputTicketDetail(
        [
            "maPhieuNhap" => $inputTicketId,
            "maSach" => $bookId,
            "giaNhap" => $price,
            "soLuong" => $quantity,
        ]);

    
    if ($result) {
        echo json_encode(["success" => true, "message" => "Thêm chi tiết phiếu nhập thành công."]);
    } else {
        echo json_encode(["success" => false, "message" => "Không có thay đổi hoặc ID không tồn tại."]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Lỗi hệ thống: " . $e->getMessage()]);
}

exit;