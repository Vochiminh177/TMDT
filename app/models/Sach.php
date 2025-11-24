<?php
class app_models_Sach extends app_libs_DBConnection
{
    protected $table_name = 'sach';

    // Lấy tất cả sách
    public function getAllBooks()
    {
        return $this->building_queryParam()->select();
    }

    // Lấy sách theo ID
    public function getBookById($maSach)
    {
        return $this->building_queryParam([
            'where' => 'maSach = ?',
            'params' => [$maSach]
        ])->select_one();
    }

    // Thêm sách mới
    public function insertBook($data)
    {
        return $this->building_queryParam([
            'field' => $data
        ])->insert();
    }

    // Cập nhật sách
    public function updateBook($maSach, $data)
    {
        $fieldValues = [];
        $params = [':maSach' => $maSach];

        foreach ($data as $field => $value) {
            $fieldValues[] = "$field = :$field";
            $params[":$field"] = $value;
        }
        // Câu SQL cập nhật
        $sql = "UPDATE " . $this->table_name . " SET " . implode(", ", $fieldValues) . " WHERE maSach = :maSach";

        // Thực thi câu lệnh SQL
        return $this->query($sql, $params);
    }




    // Xóa sách
    public function deleteBook($maSach)
    {
        return $this->building_queryParam([
            'where' => 'maSach = ?',
            'params' => [$maSach]
        ])->delete();
    }

    // Lấy sách theo mã loại
    public function getBooksByCategory($id)
    {
        return $this->building_queryParam([
            'where' => 'maTheLoai = ?',
            'params' => [$id]
        ])->select();
    }

    // Lấy sách theo khoảng giá
    public function getBookByPrice($minPrice = 0, $maxPrice = INF)
    {
        return $this->building_queryParam([
            'where' => 'giaBan >= ? and giaBan <= ?',
            'params' => [$minPrice, $maxPrice]
        ])->select();
    }



    public function getBookByFilters(
        $minPrice = 0,
        $maxPrice = null,
        $order_by = '',
        $category = '',
        $author = [],
        $id = '',
        $status = '',
        $name = '',
        $loaiBia = [],
        $nhaXuatBan = [],
        $namXuatBan = '',
        $pageSize = 10,
        $page = 1
    ) {
        $conditions = [];
        $params = [];

        if (!empty($id)) {
            return $this->building_queryParam([
                'where' => 'maSach = ?',
                'params' => [$id]
            ])->select_one();
        }

        if ($minPrice > 0) {
            $conditions[] = 'giaBan >= ?';
            $params[] = $minPrice;
        }

        if (!is_null($maxPrice)) {
            $conditions[] = 'giaBan <= ?';
            $params[] = $maxPrice;
        }

        if (!empty($category) && $category !== 'allproduct') {
            $conditions[] = 'maTheLoai = ?';
            $params[] = $category;
        }

        if (!empty($author)) {

            $authorList = is_array($author) ? $author : explode(',', $author);
            $placeholders = implode(',', array_fill(0, count($authorList), '?'));
            $conditions[] = "maTacGia IN ($placeholders)";
            $params = array_merge($params, $authorList);
        }

        if (!empty($status)) {
            $conditions[] = 'trangThai = ?';
            $params[] = $status;
        }

        if (!empty($name)) {
            $conditions[] = 'tenSach LIKE ?';
            $params[] = "%$name%";
        }

        if (!empty($loaiBia)) {
            $loaiBiaList = is_array($loaiBia) ? $loaiBia : explode(',', $loaiBia);
            $placeholders = implode(',', array_fill(0, count($loaiBiaList), '?'));
            $conditions[] = "maLoaiBia IN ($placeholders)";
            $params = array_merge($params, $loaiBiaList);
        }

        if (!empty($nhaXuatBan)) {
            $nhaXuatBanList = is_array($nhaXuatBan) ? $nhaXuatBan : explode(',', $nhaXuatBan);
            $placeholders = implode(',', array_fill(0, count($nhaXuatBanList), '?'));
            $conditions[] = "maNXB IN ($placeholders)";
            $params = array_merge($params, $nhaXuatBanList);
        }

        if (!empty($namXuatBan) && is_numeric($namXuatBan)) {
            $conditions[] = 'namXuatBan = ?';
            $params[] = $namXuatBan;
        }

        $whereClause = count($conditions) > 0 ? implode(' AND ', $conditions) : '1';

        // Phân trang
        $offset = ($page - 1) * $pageSize;

        $orderByClause = 'giaBan ' . (strtoupper($order_by) === 'DESC' ? 'DESC' : 'ASC');

        // Truy vấn
        $queryParams = [
            'where' => $whereClause,
            'params' => $params,
            'other' => "ORDER BY $orderByClause LIMIT $pageSize OFFSET $offset"
        ];

        // echo '<pre>';
        // print_r($queryParams);
        // echo '</pre>';
        // die();

        return $this->building_queryParam($queryParams)->select();
    }


    public function countBooks(
        $min_price = 0,
        $max_price = null,
        $order_by = '',
        $categoryId = '',
        $authorId = [],  // Chuyển sang mảng
        $bookId = '',
        $status = '',
        $bookName = '',
        $coverType = [], // Chuyển sang mảng
        $publisher = [], // Chuyển sang mảng
        $publishYear = ''
    ) {
        $conditions = [];
        $params = [];

        if (!empty($bookId)) {
            $conditions[] = 'maSach = ?';
            $params[] = $bookId;
        }

        if ($min_price > 0) {
            $conditions[] = 'giaBan >= ?';
            $params[] = $min_price;
        }

        if (!is_null($max_price)) {
            $conditions[] = 'giaBan <= ?';
            $params[] = $max_price;
        }

        if (!empty($categoryId)) {
            $conditions[] = 'maTheLoai = ?';
            $params[] = $categoryId;
        }

        if (!empty($authorId)) {
            $authorList = is_array($authorId) ? $authorId : explode(',', $authorId);
            $placeholders = implode(',', array_fill(0, count($authorList), '?'));
            $conditions[] = "maTacGia IN ($placeholders)";
            $params = array_merge($params, $authorList);
        }

        if (!empty($status)) {
            $conditions[] = 'trangThai = ?';
            $params[] = $status;
        }

        if (!empty($bookName)) {
            $conditions[] = 'tenSach LIKE ?';
            $params[] = "%$bookName%";
        }

        if (!empty($coverType)) {
            $coverList = is_array($coverType) ? $coverType : explode(',', $coverType);
            $placeholders = implode(',', array_fill(0, count($coverList), '?'));
            $conditions[] = "maLoaiBia IN ($placeholders)";
            $params = array_merge($params, $coverList);
        }

        if (!empty($publisher)) {
            $publisherList = is_array($publisher) ? $publisher : explode(',', $publisher);
            $placeholders = implode(',', array_fill(0, count($publisherList), '?'));
            $conditions[] = "maNXB IN ($placeholders)";
            $params = array_merge($params, $publisherList);
        }

        if (!empty($publishYear) && is_numeric($publishYear)) {
            $conditions[] = 'namXuatBan = ?';
            $params[] = $publishYear;
        }

        $whereClause = count($conditions) > 0 ? implode(' AND ', $conditions) : '1';

        return $this->building_queryParam([
            'select' => 'COUNT(*) as total',
            'where' => $whereClause,
            'params' => $params
        ])->select_one()['total'];
    }

    // Lấy danh sách sách bán chạy nhất
    public function getBestSellingBooks($limit = 8)
    {
        $limit = intval($limit);

        $this->building_queryParam([
            'select' => '*',
            'where' => "trangThai = 'Đang bán' AND soLuongBan > 0",
            'other' => "ORDER BY soLuongBan DESC LIMIT $limit"
        ]);

        return $this->select();
    }

    // Lấy danh sách sách mới về
    public function getNewBooks($limit = 8, $orderBy = 'ngayCapNhat')
    {
        $limit = intval($limit);

        $orderClause = "ngayCapNhat DESC, maSach DESC";
        if ($orderBy == 'maSach') {
            $orderClause = "maSach DESC";
        }

        $this->building_queryParam([
            'select' => '*',
            'where' => "trangThai = 'Đang bán'",
            'other' => "ORDER BY $orderClause LIMIT $limit"
        ]);

        return $this->select();
    }

    // Lấy danh sách sách đang giảm giá
    public function getDiscountedBooks($limit = 8)
    {
        $limit = intval($limit);

        $this->building_queryParam([
            'select' => '*',
            'where' => "trangThai = 'Đang bán' AND phanTramGiamGia > 0",
            'other' => "ORDER BY phanTramGiamGia DESC, ngayCapNhat DESC LIMIT $limit"
        ]);

        return $this->select();
    }

    // Lấy danh sách sách được đánh giá cao
    public function getTopRatedBooks($limit = 8)
    {
        $limit = intval($limit);

        $this->building_queryParam([
            'select' => '*',
            'where' => "trangThai = 'Đang bán' AND tongDanhGia >= 1",
            'other' => "ORDER BY trungBinhDanhGia DESC, tongDanhGia DESC LIMIT $limit"
        ]);

        return $this->select();
    }

    // Lấy danh sách tác giả tiêu biểu
    public function getFeaturedAuthors($limit = 8)
    {
        $limit = intval($limit);

        $sql = "SELECT 
                    tg.maTacGia, 
                    tg.tenTacGia, 
                    COUNT(s.maSach) as soDauSach,
                    SUM(s.soLuongBan) as tongDaBan
                FROM tacgia tg
                JOIN sach s ON tg.maTacGia = s.maTacGia
                WHERE tg.trangThai = 'Hoạt động' AND s.trangThai = 'Đang bán'
                GROUP BY tg.maTacGia, tg.tenTacGia
                ORDER BY tongDaBan DESC, soDauSach DESC
                LIMIT $limit";

        return $this->query($sql)->fetchAll();
    }

    // Lấy sách có doanh thu cao nhất
    public function getHighestRevenueBooks($limit = 5)
    {
        $limit = intval($limit);

        $sql = "SELECT s.maSach, s.tenSach, s.hinhAnh, SUM(ctdh.tienThu) as total_revenue
                FROM sach s
                JOIN chitietdonhang ctdh ON s.maSach = ctdh.maSach
                JOIN donhang dh ON ctdh.maDonHang = dh.maDonHang
                WHERE dh.trangThai IN ('Đã xác nhận', 'Đã giao hàng')
                GROUP BY s.maSach
                ORDER BY total_revenue DESC
                LIMIT $limit";

        return $this->query($sql)->fetchAll();
    }

    // public function getBookByAuthor($maTacGia)
    // {
    //     return $this->building_queryParam([
    //         'where' => 'maTacGia = ?',
    //         'params' => [$maTacGia]
    //     ])->select();
    // }
}
