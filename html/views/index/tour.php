<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour</title>
    <link rel="stylesheet" href="../../../css/Main5.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            background: url('https://images.unsplash.com/photo-1750779941284-09ee2d6a619c?q=80&w=1742&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D') no-repeat center center fixed;
            background-size: cover;
        }
    </style>
</head>
<body>
<?php 
include '../../../includes/header.php';
include '../../../db/db.php';

// ========================================
// LẤY CÁC THAM SỐ LỌC
// ========================================
$_key = isset($_GET['key']) ? trim($_GET['key']) : '';
$tour_type = isset($_GET['tour_type']) ? $_GET['tour_type'] : '';
$locations = isset($_GET['locations']) ? $_GET['locations'] : [];
$min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 10000000;
$duration = isset($_GET['duration']) ? $_GET['duration'] : '';
$rating = isset($_GET['rating']) ? (int)$_GET['rating'] : 0;

// ========================================
// PHÂN TRANG
// ========================================
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);
$tours_per_page = 6;
$offset = ($page - 1) * $tours_per_page;

// ========================================
// XÂY DỰNG QUERY LỌC
// ========================================
$sql_count = "SELECT COUNT(*) as total FROM tour WHERE trang_thai = 1";
$sql_httour = "SELECT * FROM tour WHERE trang_thai = 1";

// Tìm kiếm theo từ khóa
if (!empty($_key)) {
    $key_escaped = mysqli_real_escape_string($conn, $_key);
    $sql_count .= " AND ten_tour LIKE '%$key_escaped%'";
    $sql_httour .= " AND ten_tour LIKE '%$key_escaped%'";
}

// Lọc theo loại tour
if (!empty($tour_type)) {
    if ($tour_type == 'trong_ngay') {
        $sql_count .= " AND so_ngay = 1";
        $sql_httour .= " AND so_ngay = 1";
    } elseif ($tour_type == 'dai_ngay') {
        $sql_count .= " AND so_ngay > 1";
        $sql_httour .= " AND so_ngay > 1";
    }
}

// Lọc theo địa điểm (cột vi_tri)
if (!empty($locations) && is_array($locations)) {
    $location_conditions = [];
    foreach ($locations as $loc) {
        $loc_escaped = mysqli_real_escape_string($conn, $loc);
        $location_conditions[] = "vi_tri = '$loc_escaped'";
    }
    if (!empty($location_conditions)) {
        $location_filter = " AND (" . implode(' OR ', $location_conditions) . ")";
        $sql_count .= $location_filter;
        $sql_httour .= $location_filter;
    }
}

// Lọc theo giá
if ($min_price > 0 || $max_price < 10000000) {
    $sql_count .= " AND gia BETWEEN $min_price AND $max_price";
    $sql_httour .= " AND gia BETWEEN $min_price AND $max_price";
}

// Lọc theo thời gian
if (!empty($duration)) {
    switch ($duration) {
        case '1':
            $sql_count .= " AND so_ngay = 1";
            $sql_httour .= " AND so_ngay = 1";
            break;
        case '2-3':
            $sql_count .= " AND so_ngay BETWEEN 2 AND 3";
            $sql_httour .= " AND so_ngay BETWEEN 2 AND 3";
            break;
        case '4-5':
            $sql_count .= " AND so_ngay BETWEEN 4 AND 5";
            $sql_httour .= " AND so_ngay BETWEEN 4 AND 5";
            break;
        case '6+':
            $sql_count .= " AND so_ngay >= 6";
            $sql_httour .= " AND so_ngay >= 6";
            break;
    }
}

// ========================================
// TÍNH TỔNG SỐ TOUR VÀ TRANG
// ========================================
$result_count = mysqli_query($conn, $sql_count);
$row_count = mysqli_fetch_assoc($result_count);
$total_tours = $row_count['total'];
$total_pages = ceil($total_tours / $tours_per_page);

// Thêm LIMIT cho query
$sql_httour .= " ORDER BY id DESC LIMIT $offset, $tours_per_page";

// ========================================
// HÀM TẠO URL VỚI THAM SỐ LỌC
// ========================================
function build_filter_url($params = []) {
    global $_key, $tour_type, $locations, $min_price, $max_price, $duration, $rating;
    
    $url_params = [];
    
    if (!empty($_key)) $url_params['key'] = $_key;
    if (!empty($tour_type)) $url_params['tour_type'] = $tour_type;
    if (!empty($locations)) $url_params['locations'] = $locations;
    if ($min_price > 0) $url_params['min_price'] = $min_price;
    if ($max_price < 10000000) $url_params['max_price'] = $max_price;
    if (!empty($duration)) $url_params['duration'] = $duration;
    if ($rating > 0) $url_params['rating'] = $rating;
    
    $url_params = array_merge($url_params, $params);
    
    return '?' . http_build_query($url_params);
}
?>

<div class="tour_container">
    <!-- ========================================
         SIDEBAR LỌC
         ======================================== -->
    <div class="tour_container1 box fade-left">
        <form method="GET" action="" id="filterForm">
            <!-- Header -->
            <div class="filter-header">
                <h3>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    Lọc Tour
                </h3>
                <button type="button" class="reset-btn" onclick="window.location.href='?'">Đặt lại</button>
            </div>

            <!-- Giữ lại từ khóa tìm kiếm -->
            <?php if (!empty($_key)): ?>
                <input type="hidden" name="key" value="<?php echo htmlspecialchars($_key); ?>">
            <?php endif; ?>

            <!-- Loại Tour -->
            <div class="filter-section">
                <h4>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Loại Tour
                </h4>
                <div class="tour-type-buttons">
                    <button type="button" class="tour-type-btn <?php echo empty($tour_type) ? 'active' : ''; ?>" onclick="setTourType('')">Tất cả</button>
                    <button type="button" class="tour-type-btn <?php echo $tour_type == 'trong_ngay' ? 'active' : ''; ?>" onclick="setTourType('trong_ngay')">Trong Ngày</button>
                    <button type="button" class="tour-type-btn <?php echo $tour_type == 'dai_ngay' ? 'active' : ''; ?>" onclick="setTourType('dai_ngay')">Dài Ngày</button>
                </div>
                <input type="hidden" name="tour_type" id="tour_type" value="<?php echo htmlspecialchars($tour_type); ?>">
            </div>

            <!-- Địa điểm -->
            <div class="filter-section">
                <h4>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Địa điểm
                </h4>
                <div class="checkbox-list">
                    <?php
                    // Lấy danh sách vị trí tự động từ database
                    $sql_vitri = "SELECT vi_tri, COUNT(*) as so_luong 
                                  FROM tour 
                                  WHERE trang_thai = 1 AND vi_tri IS NOT NULL AND vi_tri != ''
                                  GROUP BY vi_tri 
                                  ORDER BY so_luong DESC, vi_tri ASC";
                    $result_vitri = mysqli_query($conn, $sql_vitri);

                    $index = 0;
                    while($row_vitri = mysqli_fetch_array($result_vitri)) {
                        $vitri_value = $row_vitri['vi_tri'];
                        $so_luong = $row_vitri['so_luong'];
                        $vitri_id = 'loc-' . $index;
                        $is_checked = in_array($vitri_value, $locations) ? 'checked' : '';
                        ?>
                        <div class="checkbox-item">
                            <input type="checkbox" id="<?php echo $vitri_id; ?>" name="locations[]" value="<?php echo htmlspecialchars($vitri_value); ?>" <?php echo $is_checked; ?>>
                            <label for="<?php echo $vitri_id; ?>"><?php echo htmlspecialchars($vitri_value); ?></label>
                            <span class="count"><?php echo $so_luong; ?></span>
                        </div>
                        <?php
                        $index++;
                    }

                    if (mysqli_num_rows($result_vitri) == 0) {
                        echo '<p style="color: #999; font-size: 14px; padding: 10px 0;">Chưa có vị trí nào</p>';
                    }
                    ?>
                </div>
            </div>

            <!-- Khoảng giá -->
            <div class="filter-section">
                <h4>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Khoảng Giá
                </h4>
                <div class="price-range">
                    <div class="price-inputs">
                        <div class="price-input-group">
                            <label>Tối thiểu</label>
                            <input type="number" name="min_price" placeholder="0" value="<?php echo $min_price; ?>" min="0">
                        </div>
                        <div class="price-input-group">
                            <label>Tối đa</label>
                            <input type="number" name="max_price" placeholder="10,000,000" value="<?php echo $max_price; ?>" max="10000000">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thời gian -->
            <div class="filter-section">
                <h4>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Thời gian
                </h4>
                <div class="duration-options">
                    <button type="button" class="duration-btn <?php echo $duration == '1' ? 'active' : ''; ?>" onclick="setDuration('1')">1 ngày</button>
                    <button type="button" class="duration-btn <?php echo $duration == '2-3' ? 'active' : ''; ?>" onclick="setDuration('2-3')">2-3 ngày</button>
                    <button type="button" class="duration-btn <?php echo $duration == '4-5' ? 'active' : ''; ?>" onclick="setDuration('4-5')">4-5 ngày</button>
                    <button type="button" class="duration-btn <?php echo $duration == '6+' ? 'active' : ''; ?>" onclick="setDuration('6+')">6+ ngày</button>
                </div>
                <input type="hidden" name="duration" id="duration" value="<?php echo htmlspecialchars($duration); ?>">
            </div>

            <!-- Đánh giá -->
            <div class="filter-section">
                <h4>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                    Đánh Giá
                </h4>
                <div class="rating-options">
                    <label class="rating-item">
                        <input type="radio" name="rating" value="5" <?php echo $rating == 5 ? 'checked' : ''; ?>>
                        <span class="sosao">5 sao</span>
                        <span class="stars">★★★★★</span>
                    </label>
                    <label class="rating-item">
                        <input type="radio" name="rating" value="4" <?php echo $rating == 4 ? 'checked' : ''; ?>>
                        <span class="sosao">4 sao trở lên</span>
                        <span class="stars">★★★★☆</span>
                    </label>
                    <label class="rating-item">
                        <input type="radio" name="rating" value="3" <?php echo $rating == 3 ? 'checked' : ''; ?>>
                        <span class="sosao">3 sao trở lên</span>
                        <span class="stars">★★★☆☆</span>
                    </label>
                </div>
            </div>

            <!-- Nút Áp dụng -->
            <button type="submit" class="apply-filter-btn">Áp dụng lọc</button>
        </form>
    </div>

    <!-- ========================================
         DANH SÁCH TOUR
         ======================================== -->
    <div class="tour_container2">
        <h1>Tour Ưu Đãi Tốt Nhất Hôm Nay</h1>
        
        <?php
        $result_httour = mysqli_query($conn, $sql_httour);

        if (mysqli_num_rows($result_httour) > 0) {
            while($row_httour = mysqli_fetch_array($result_httour)) {
        ?>
                <div class="tour_box box fade-right">
                    <div class="tour_box_img">
                        <img src="../../../uploads/<?php echo $row_httour['hinh_anh']; ?>" alt="">
                    </div>
                    <div class="tour_box_tt">
                        <div class="nameandtime">
                            <div class="name_tour"><?php echo htmlspecialchars($row_httour['ten_tour']); ?></div>
                            <div class="tour_time">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                                </svg>
                                <p>14-09-2025</p>
                            </div>
                        </div>
                        <div class="cmtandgia">
                            <div class="cmt">
                                <p><span class="dg">9.2</span><span class="dg2">Tuyệt Vời </span>| 23 Đánh Giá</p>
                            </div>
                            <div class="gia"><?php echo number_format($row_httour['gia'], 0, ',', '.') . " VNĐ"; ?></div>
                        </div>
                        <div class="cmtandgia">
                            <div class="cmt">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <p><?php echo $row_httour['so_ngay'] . " Ngày"; ?></p>
                            </div>
                            <button onclick="window.location.href='detailed_tour.php?id=<?php echo $row_httour['id']; ?>'" class="tour_button">Xem Chi Tiết</button>
                        </div>
                    </div>
                </div>
        <?php 
            }
        } else {
            echo '<p style="font-size: 170px; text-align: center; padding-top: 40px; color: #ffffffff;"><i class="bi bi-inbox-fill"></i></p><br>
                  <p style="font-family:\'Monsieur La Doulaise\', cursive;font-size: 20px; text-align: center; padding: 10px; color: #ffffffff;">Không tìm thấy tour nào phù hợp.</p>';
        }
        ?>

        <!-- ========================================
             PHÂN TRANG
             ======================================== -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <!-- Nút Trước -->
                <?php if ($page > 1): ?>
                    <a href="<?php echo build_filter_url(['page' => $page - 1]); ?>" class="prev-next">« Trước</a>
                <?php else: ?>
                    <span class="disabled prev-next">« Trước</span>
                <?php endif; ?>

                <!-- Số trang -->
                <?php
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);
                
                // Trang đầu
                if ($start_page > 1) {
                    echo '<a href="' . build_filter_url(['page' => 1]) . '">1</a>';
                    if ($start_page > 2) {
                        echo '<span class="disabled">...</span>';
                    }
                }
                
                // Các trang giữa
                for ($i = $start_page; $i <= $end_page; $i++) {
                    if ($i == $page) {
                        echo '<span class="active">' . $i . '</span>';
                    } else {
                        echo '<a href="' . build_filter_url(['page' => $i]) . '">' . $i . '</a>';
                    }
                }
                
                // Trang cuối
                if ($end_page < $total_pages) {
                    if ($end_page < $total_pages - 1) {
                        echo '<span class="disabled">...</span>';
                    }
                    echo '<a href="' . build_filter_url(['page' => $total_pages]) . '">' . $total_pages . '</a>';
                }
                ?>

                <!-- Nút Sau -->
                <?php if ($page < $total_pages): ?>
                    <a href="<?php echo build_filter_url(['page' => $page + 1]); ?>" class="prev-next">Sau »</a>
                <?php else: ?>
                    <span class="disabled prev-next">Sau »</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php include '../../../includes/footer.php'; ?>

<script src="../../../js/Main5.js"></script>
<script>
function setTourType(type) {
    document.getElementById('tour_type').value = type;
    document.getElementById('filterForm').submit();
}

function setDuration(duration) {
    const currentDuration = document.getElementById('duration').value;
    if (currentDuration === duration) {
        document.getElementById('duration').value = '';
    } else {
        document.getElementById('duration').value = duration;
    }
    document.getElementById('filterForm').submit();
}
</script>
</body>
</html>