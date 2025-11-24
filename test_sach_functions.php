<?php
require_once 'app/libs/DBConnection.php';
require_once 'app/models/Sach.php';
require_once 'app/config.php';

// Khởi tạo đối tượng Sach
$sachModel = new app_models_Sach();

// --- Kiểm tra các hàm ---

echo "<h1>Kiểm tra các hàm trong Sach Model</h1>";

// 1. Lấy những cuốn sách có nhiều lượt mua nhất
echo "<h2>1. getBestSellingBooks(5)</h2>";
$bestSellingBooks = $sachModel->getBestSellingBooks(5);
echo "<pre>";
print_r($bestSellingBooks);
echo "</pre>";

// 2. Lấy những cuốn sách có doanh số cao nhất
echo "<h2>2. getHighestRevenueBooks(5)</h2>";
$highestRevenueBooks = $sachModel->getHighestRevenueBooks(5);
echo "<pre>";
print_r($highestRevenueBooks);
echo "</pre>";

// 3. Lấy những cuốn sách đang giảm giá
echo "<h2>3. getDiscountedBooks(5)</h2>";
$discountedBooks = $sachModel->getDiscountedBooks(5);
echo "<pre>";
print_r($discountedBooks);
echo "</pre>";

// 4. Lấy những cuốn sách mới theo id
echo "<h2>4. getNewBooks(10, 'maSach')</h2>";
$newBooksById = $sachModel->getNewBooks(10, 'maSach');
echo "<pre>";
print_r($newBooksById);
echo "</pre>";

// 5. Lấy những cuốn sách mới theo ngày cập nhật
echo "<h2>5. getNewBooks(10, 'ngayCapNhat')</h2>";
$newBooksByDate = $sachModel->getNewBooks(10, 'ngayCapNhat');
echo "<pre>";
print_r($newBooksByDate);
echo "</pre>";

// 6. Lấy những cuốn được đánh giá cao
echo "<h2>6. getTopRatedBooks(10)</h2>";
$topRatedBooks = $sachModel->getTopRatedBooks(10);
echo "<pre>";
print_r($topRatedBooks);
echo "</pre>";

// 7. Lấy những tác giả có nhiều id sách
echo "<h2>7. getAuthorsWithMostBooks(10)</h2>";
$authorsWithMostBooks = $sachModel->getFeaturedAuthors(10);
echo "<pre>";
print_r($authorsWithMostBooks);
echo "</pre>";

echo "<h2>8. test getBookByFilters()</h2>";
$filters = [
    'theLoai' => [1, 2], // Giả sử thể loại 1 và 2
    'tacGia' => [3],     // Giả sử tác giả 3
    'nhaXuatBan' => [4], // Giả sử nhà xuất bản 4
    'giaMin' => 10000,
    'giaMax' => 50000,
    'namXuatBanMin' => 2000,
    'namXuatBanMax' => 2023,
    'trangThai' => 'Đang bán'
];
$filteredBooks = $sachModel->getBookByFilters($filters);
echo "<pre>";
print_r($filteredBooks);
echo "</pre>";



?>
