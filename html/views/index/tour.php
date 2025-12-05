<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour - Tìm kiếm & Lọc</title>
    <link rel="stylesheet" href="../../../css/Main5.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            background: url('https://images.unsplash.com/photo-1750779941284-09ee2d6a619c?q=80&w=1742&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D') no-repeat center center fixed;
            background-size: cover;
        }
        .dg2.excellent    { color: #00b67a !important; font-weight: 600; } /* Xanh lá đậm - Tuyệt vời */
        .dg2.very-good    { color: #00a550 !important; font-weight: 600; } /* Xanh lá   - Rất tốt */
        .dg2.good         { color: #66b100 !important; font-weight: 600; } /* Xanh vàng - Tốt */
        .dg2.fair         { color: #ff8f00 !important; font-weight: 600; } /* Cam       - Khá */
        .dg2.poor         { color: #ff6b6b !important; font-weight: 600; } /* Đỏ nhạt   - Trung bình */
        
        .dg { 
            font-size: 1em; 
            font-weight: bold; 
            color: #fff; 
            background: rgb(78 215 169); 
            border-radius: 4px;
            padding: 2px 6px;
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
$_key       = isset($_GET['key'])       ? trim($_GET['key']) : '';
$tour_type  = isset($_GET['tour_type']) ? $_GET['tour_type'] : '';
$locations  = isset($_GET['locations']) ? (array)$_GET['locations'] : [];
$min_price  = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$max_price  = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 10000000;
$duration   = isset($_GET['duration'])  ? $_GET['duration'] : '';
$rating     = isset($_GET['rating'])    ? (float)$_GET['rating'] : 0;   // đổi thành float để so sánh chính xác

// ========================================
// PHÂN TRANG
// ========================================
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);
$tours_per_page = 6;
$offset = ($page - 1) * $tours_per_page;

// ========================================
// XÂY DỰNG QUERY CHÍNH
// ========================================
$sql_httour = "
    SELECT 
        t.*,
        COALESCE(ROUND(AVG(dg.diem), 1), 0) AS avg_rating,
        COUNT(dg.id) AS total_reviews
    FROM tour t
    LEFT JOIN danh_gia dg ON t.id = dg.tour_id AND dg.diem IS NOT NULL
    WHERE t.trang_thai = 1
";

$sql_count = "SELECT COUNT(*) as total FROM tour WHERE trang_thai = 1";

// Tìm kiếm theo từ khóa
if (!empty($_key)) {
    $key_escaped = mysqli_real_escape_string($conn, $_key);
    $sql_count  .= " AND ten_tour LIKE '%$key_escaped%'";
    $sql_httour .= " AND t.ten_tour LIKE '%$key_escaped%'";
}

// Lọc loại tour
if (!empty($tour_type)) {
    if ($tour_type == 'trong_ngay') {
        $sql_count  .= " AND so_ngay = 1";
        $sql_httour .= " AND t.so_ngay = 1";
    } elseif ($tour_type == 'dai_ngay') {
        $sql_count  .= " AND so_ngay > 1";
        $sql_httour .= " AND t.so_ngay > 1";
    }
}

// Lọc địa điểm
if (!empty($locations)) {
    $location_conditions = [];
    foreach ($locations as $loc) {
        $loc_escaped = mysqli_real_escape_string($conn, $loc);
        $location_conditions[] = "vi_tri = '$loc_escaped'";
    }
    if (!empty($location_conditions)) {
        $location_filter = " AND (" . implode(' OR ', $location_conditions) . ")";
        $sql_count  .= $location_filter;
        $sql_httour .= $location_filter;
    }
}

// Lọc giá
if ($min_price > 0 || $max_price < 10000000) {
    $sql_count  .= " AND gia BETWEEN $min_price AND $max_price";
    $sql_httour .= " AND t.gia BETWEEN $min_price AND $max_price";
}

// Lọc thời gian
if (!empty($duration)) {
    switch ($duration) {
        case '1':     $sql_count .= " AND so_ngay = 1"; $sql_httour .= " AND t.so_ngay = 1"; break;
        case '2-3':   $sql_count .= " AND so_ngay BETWEEN 2 AND 3"; $sql_httour .= " AND t.so_ngay BETWEEN 2 AND 3"; break;
        case '4-5':   $sql_count .= " AND so_ngay BETWEEN 4 AND 5"; $sql_httour .= " AND t.so_ngay BETWEEN 4 AND 5"; break;
        case '6+':    $sql_count .= " AND so_ngay >= 6"; $sql_httour .= " AND t.so_ngay >= 6"; break;
    }
}

// Lọc theo điểm đánh giá (HAVING)
$having_clause = "";
if ($rating > 0) {
    $having_clause = " HAVING avg_rating >= $rating";
}

// Tính tổng tour
if ($rating > 0) {
    $temp_sql = "SELECT COUNT(*) as total FROM ($sql_httour GROUP BY t.id $having_clause) as temp";
    $result_count = mysqli_query($conn, $temp_sql);
} else {
    $result_count = mysqli_query($conn, $sql_count);
}
$row_count = mysqli_fetch_assoc($result_count);
$total_tours = $row_count['total'];
$total_pages = ceil($total_tours / $tours_per_page);

// Hoàn thiện query chính
$sql_httour .= " GROUP BY t.id $having_clause ORDER BY t.id DESC LIMIT $offset, $tours_per_page";

// Hàm tạo URL giữ filter
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
    <!-- SIDEBAR LỌC -->
    <div class="tour_container1 box fade-left">
        <form method="GET" action="" id="filterForm">
            <div class="filter-header">
                <h3>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    Lọc Tour
                </h3>
                <button type="button" class="reset-btn" onclick="window.location.href='?'">Đặt lại</button>
            </div>

            <?php if (!empty($_key)): ?>
                <input type="hidden" name="key" value="<?php echo htmlspecialchars($_key); ?>">
            <?php endif; ?>

            <!-- Loại Tour -->
            <div class="filter-section">
                <h4><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
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
                <h4><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    Địa điểm
                </h4>
                <div class="checkbox-list">
                    <?php
                    $sql_vitri = "SELECT vi_tri, COUNT(*) as so_luong FROM tour WHERE trang_thai = 1 AND vi_tri IS NOT NULL AND vi_tri != '' GROUP BY vi_tri ORDER BY so_luong DESC, vi_tri ASC";
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
                        <?php $index++; }
                    if (mysqli_num_rows($result_vitri) == 0) {
                        echo '<p style="color: #999; font-size: 14px; padding: 10px 0;">Chưa có vị trí nào</p>';
                    }
                    ?>
                </div>
            </div>

            <!-- Khoảng giá -->
            <div class="filter-section">
                <h4><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
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
                <h4><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
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

            <!-- ĐÁNH GIÁ THEO ĐIỂM SỐ (≥ 9.0, ≥ 8.0, ≥ 7.0, ≥ 6.0) -->
            <div class="filter-section">
                <h4>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                    Đánh Giá (Điểm)
                </h4>
                <div class="rating-options">
                    <label class="rating-item">
                        <input type="radio" name="rating" value="9.0" <?php echo $rating == 9.0 ? 'checked' : ''; ?>>
                        <span class="sosao">≥ 9.0</span>
                        <span class="stars excellent">Tuyệt vời</span>
                    </label>
                    <label class="rating-item">
                        <input type="radio" name="rating" value="8.0" <?php echo $rating == 8.0 ? 'checked' : ''; ?>>
                        <span class="sosao">≥ 8.0</span>
                        <span class="stars very-good">Rất tốt</span>
                    </label>
                    <label class="rating-item">
                        <input type="radio" name="rating" value="7.0" <?php echo $rating == 7.0 ? 'checked' : ''; ?>>
                        <span class="sosao">≥ 7.0</span>
                        <span class="stars good">Tốt</span>
                    </label>
                    <label class="rating-item">
                        <input type="radio" name="rating" value="6.0" <?php echo $rating == 6.0 ? 'checked' : ''; ?>>
                        <span class="sosao">≥ 6.0</span>
                        <span class="stars fair">Khá</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="apply-filter-btn">Áp dụng lọc</button>
        </form>
    </div>

    <!-- DANH SÁCH TOUR -->
    <div class="tour_container2">
        <h1>Tour Ưu Đãi Tốt Nhất Hôm Nay</h1>

        <?php if (!empty($_key) || !empty($tour_type) || !empty($locations) || $min_price > 0 || $max_price < 10000000 || !empty($duration) || $rating > 0): ?>
            <div class="filter-info">
                <strong>Đang lọc:</strong>
                <?php if (!empty($_key)): ?>
                    <span class="filter-badge keyword"><i class="bi bi-search"></i> Từ khóa: <?php echo htmlspecialchars($_key); ?></span>
                <?php endif; ?>
                <?php if (!empty($tour_type)): ?>
                    <span class="filter-badge tour-type"><?php echo $tour_type == 'trong_ngay' ? '<i class="bi bi-alarm"></i> Trong ngày' : '<i class="bi bi-calendar"></i> Dài ngày'; ?></span>
                <?php endif; ?>
                <?php if (!empty($locations)): foreach ($locations as $loc): ?>
                    <span class="filter-badge location"><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($loc); ?></span>
                <?php endforeach; endif; ?>
                <?php if ($min_price > 0 || $max_price < 10000000): ?>
                    <span class="filter-badge price"><i class="bi bi-cash-coin"></i> <?php echo number_format($min_price,0,',','.'); ?> - <?php echo number_format($max_price,0,',','.'); ?> VNĐ</span>
                <?php endif; ?>
                <?php if (!empty($duration)): ?>
                    <span class="filter-badge duration"><i class="bi bi-clock"></i> 
                        <?php 
                            switch ($duration) {
                                case '1': echo '1 ngày'; break;
                                case '2-3': echo '2-3 ngày'; break;
                                case '4-5': echo '4-5 ngày'; break;
                                case '6+': echo '6+ ngày'; break;
                            }
                        ?>
                    </span>
                <?php endif; ?>
                <?php if ($rating > 0): ?>
                    <span class="filter-badge rating"><i class="bi bi-star-fill"></i> ≥ <?php echo $rating; ?> điểm</span>
                <?php endif; ?>
                <span class="filter-count"><?php echo $total_tours; ?> tour</span>
            </div>
        <?php endif; ?>

        <?php
        $result_httour = mysqli_query($conn, $sql_httour);
        if (mysqli_num_rows($result_httour) > 0):
            while($row_httour = mysqli_fetch_array($result_httour)):
                $avg_rating = $row_httour['avg_rating'] > 0 ? $row_httour['avg_rating'] : '0.0';
                $total_reviews = $row_httour['total_reviews'];
        ?>
                <div class="tour_box box fade-right">
                    <div class="tour_box_img">
                        <img src="../../../uploads/<?php echo $row_httour['hinh_anh']; ?>" alt="">
                    </div>
                    <div class="tour_box_tt">
                        <div class="nameandtime">
                            <div class="name_tour"><?php echo htmlspecialchars($row_httour['ten_tour']); ?></div>
                            <div class="tour_time">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" /></svg>
                                <p>14-09-2025</p>
                            </div>
                        </div>

                        <div class="cmtandgia">
                            <div class="cmt">
                                <p>
                                    <span class="dg"><?php echo number_format($row_httour['avg_rating'], 1); ?></span>
                                    <span class="dg2 
                                        <?php 
                                            if ($row_httour['avg_rating'] >= 9.0) echo 'excellent';
                                            elseif ($row_httour['avg_rating'] >= 8.0) echo 'very-good';
                                            elseif ($row_httour['avg_rating'] >= 7.0) echo 'good';
                                            elseif ($row_httour['avg_rating'] >= 6.0) echo 'fair';
                                            else echo 'poor';
                                        ?>">
                                        <?php 
                                            if ($row_httour['total_reviews'] == 0) echo 'Chưa có đánh giá';
                                            elseif ($row_httour['avg_rating'] >= 9.0) echo 'Tuyệt vời';
                                            elseif ($row_httour['avg_rating'] >= 8.0) echo 'Rất tốt';
                                            elseif ($row_httour['avg_rating'] >= 7.0) echo 'Tốt';
                                            elseif ($row_httour['avg_rating'] >= 6.0) echo 'Khá';
                                            else echo 'Trung bình';
                                        ?>
                                    </span> 
                                    | <?php echo $row_httour['total_reviews'] > 0 ? $row_httour['total_reviews'] . ' Đánh Giá' : 'Chưa có đánh giá'; ?>
                                </p>
                            </div>
                            <div class="gia"><?php echo number_format($row_httour['gia'], 0, ',', '.') . " VNĐ"; ?></div>
                        </div>

                        <div class="cmtandgia">
                            <div class="cmt">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                <p><?php echo $row_httour['so_ngay'] . " Ngày"; ?></p>
                            </div>
                            <button onclick="window.location.href='detailed_tour.php?id=<?php echo $row_httour['id']; ?>'" class="tour_button">Xem Chi Tiết</button>
                        </div>
                    </div>
                </div>
        <?php 
            endwhile;
        else: 
            echo '<p style="font-size: 170px; text-align: center; padding-top: 40px; color: #ffffff;"><i class="bi bi-inbox"></i></p><br>
                  <p style="font-family:\'Monsieur La Doulaise\', cursive; font-size: 20px; text-align: center; padding: 10px; color: #ffffff;">Không tìm thấy tour nào phù hợp.</p>';
        endif;
        ?>

        <!-- PHÂN TRANG -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="<?php echo build_filter_url(['page' => $page - 1]); ?>" class="prev-next"><i class="bi bi-caret-left-fill"></i></a>
                <?php else: ?>
                    <span class="disabled prev-next"><i class="bi bi-caret-left-fill"></i></span>
                <?php endif; ?>

                <?php
                $start_page = max(1, $page - 2);
                $end_page   = min($total_pages, $page + 2);
                if ($start_page > 1) {
                    echo '<a href="' . build_filter_url(['page' => 1]) . '">1</a>';
                    if ($start_page > 2) echo '<span class="disabled">...</span>';
                }
                for ($i = $start_page; $i <= $end_page; $i++) {
                    echo $i == $page ? '<span class="active">'.$i.'</span>' : '<a href="'.build_filter_url(['page' => $i]).'">'.$i.'</a>';
                }
                if ($end_page < $total_pages) {
                    if ($end_page < $total_pages - 1) echo '<span class="disabled">...</span>';
                    echo '<a href="'.build_filter_url(['page' => $total_pages]).'">'.$total_pages.'</a>';
                }
                ?>

                <?php if ($page < $total_pages): ?>
                    <a href="<?php echo build_filter_url(['page' => $page + 1]); ?>" class="prev-next"><i class="bi bi-caret-right-fill"></i></a>
                <?php else: ?>
                    <span class="disabled prev-next"><i class="bi bi-caret-right-fill"></i></span>
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
    const current = document.getElementById('duration').value;
    document.getElementById('duration').value = (current === duration) ? '' : duration;
    document.getElementById('filterForm').submit();
}

document.querySelectorAll('input[name="locations[]"], input[name="rating"], input[name="min_price"], input[name="max_price"]').forEach(el => {
    el.addEventListener('change', () => document.getElementById('filterForm').submit());
});
</script>
</body>
</html>