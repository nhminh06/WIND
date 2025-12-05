<?php
session_start();
include '../../db/db.php';

// Kiểm tra quyền admin hoặc staff
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff')) {
    header('Location: ../../index.php');
    exit();
}

// Thống kê trọng điểm
$sql_stats = "SELECT 
    (SELECT COUNT(*) FROM user WHERE trang_thai = 1) as active_users,
    (SELECT COUNT(*) FROM tour WHERE trang_thai = 1) as active_tours,
    (SELECT COUNT(*) FROM dat_tour WHERE trang_thai = 'pending') as pending_bookings,
    (SELECT SUM(tong_tien) FROM dat_tour WHERE trang_thai = 'confirmed' AND MONTH(ngay_dat) = MONTH(CURRENT_DATE)) as monthly_revenue,
    (SELECT COUNT(*) FROM khieu_nai WHERE trang_thai = 0) as pending_complaints,
    (SELECT COUNT(*) FROM bai_viet WHERE trang_thai = 1) as active_posts,
    (SELECT COUNT(*) FROM khampha WHERE trang_thai = 1) as active_explore,
    (SELECT COUNT(*) FROM binh_luan) as total_comments";
$result_stats = $conn->query($sql_stats);
$stats = $result_stats->fetch_assoc();

// Doanh thu 6 tháng gần nhất
$sql_revenue = "SELECT 
    DATE_FORMAT(ngay_dat, '%m/%Y') as month,
    SUM(tong_tien) as revenue
    FROM dat_tour
    WHERE trang_thai = 'confirmed'
    AND ngay_dat >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(ngay_dat, '%Y-%m')
    ORDER BY DATE_FORMAT(ngay_dat, '%Y-%m') ASC";
$result_revenue = $conn->query($sql_revenue);

$months = [];
$revenues = [];
while($row = $result_revenue->fetch_assoc()) {
    $months[] = "'" . $row['month'] . "'";
    $revenues[] = $row['revenue'];
}

// Trạng thái đặt tour
$sql_status = "SELECT 
    CASE 
        WHEN trang_thai = 'confirmed' THEN 'Đã xác nhận'
        WHEN trang_thai = 'pending' THEN 'Chờ xác nhận'
        WHEN trang_thai = 'cancelled' THEN 'Đã hủy'
    END as status_name,
    COUNT(*) as count
    FROM dat_tour
    GROUP BY trang_thai";
$result_status = $conn->query($sql_status);

$status_labels = [];
$status_data = [];
while($row = $result_status->fetch_assoc()) {
    $status_labels[] = "'" . $row['status_name'] . "'";
    $status_data[] = $row['count'];
}

// Top 5 tour doanh thu
$sql_top = "SELECT 
    t.ten_tour,
    SUM(d.tong_tien) as revenue
    FROM tour t
    INNER JOIN dat_tour d ON t.id = d.tour_id
    WHERE d.trang_thai = 'confirmed'
    GROUP BY t.id
    ORDER BY revenue DESC
    LIMIT 5";
$result_top = $conn->query($sql_top);

$tour_names = [];
$tour_revenues = [];
while($row = $result_top->fetch_assoc()) {
    $tour_names[] = "'" . addslashes(substr($row['ten_tour'], 0, 30)) . "'";
    $tour_revenues[] = $row['revenue'];
}

// Thống kê bài viết khám phá theo loại
$sql_explore = "SELECT 
    kl.ten_loai,
    COUNT(k.khampha_id) as count
    FROM khampha_loai kl
    LEFT JOIN khampha k ON kl.loai_id = k.loai_id AND k.trang_thai = 1
    GROUP BY kl.loai_id, kl.ten_loai
    ORDER BY kl.loai_id";
$result_explore = $conn->query($sql_explore);

$explore_labels = [];
$explore_counts = [];
while($row = $result_explore->fetch_assoc()) {
    $explore_labels[] = "'" . $row['ten_loai'] . "'";
    $explore_counts[] = $row['count'];
}

// Thống kê bình luận theo tháng (6 tháng)
$sql_comments = "SELECT 
    DATE_FORMAT(ngay_tao, '%m/%Y') as month,
    COUNT(*) as count
    FROM binh_luan
    WHERE ngay_tao >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(ngay_tao, '%Y-%m')
    ORDER BY DATE_FORMAT(ngay_tao, '%Y-%m') ASC";
$result_comments = $conn->query($sql_comments);

$comment_months = [];
$comment_counts = [];
while($row = $result_comments->fetch_assoc()) {
    $comment_months[] = "'" . $row['month'] . "'";
    $comment_counts[] = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin</title>
    <link rel="stylesheet" href="../../css/Admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            border-radius: 10px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .stat-card h3 {
            font-size: 32px;
            font-weight: 700;
            color: #fff;
            margin: 0 0 5px 0;
        }

        .stat-card p {
            font-size: 13px;
            margin: 0;
            opacity: 0.9;
             color: #fff;
        }

        .stat-card i {
            position: absolute;
            right: 15px;
            top: 15px;
            font-size: 35px;
            opacity: 0.2;
        }

        .stat-card.blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-card.green { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .stat-card.orange { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
        .stat-card.purple { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .stat-card.red { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stat-card.teal { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); }
        .stat-card.pink { background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); }
        .stat-card.indigo { background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%); }

        .charts-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .chart-box {
            background: #2a2a2a;
            padding: 20px;
            border-radius: 10px;
        }

        .chart-title {
            margin: 0 0 15px 0;
            font-size: 16px;
            color: #fff;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            padding-bottom: 10px;
            border-bottom: 2px solid #444;
        }

        .chart-wrapper {
            position: relative;
            height: 280px;
        }

        .top-tour-box {
            background: #2a2a2a;
            padding: 20px;
            border-radius: 10px;
        }

        @media (max-width: 1024px) {
            .charts-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
  <aside class="sidebar">
    <h2 class="logo">WIND Admin</h2>
    <?php include '../../includes/Adminnav.php';?>
  </aside>

  <div class="main">
    <header class="header">
       <button class="menu-toggle">
        <span></span>
        <span></span>
        <span></span>
    </button>
      <h1>Thống Kê</h1>
      <div class="admin-info">
        <?php echo "<p>Xin chào " . (isset($_SESSION['ho_ten']) ? $_SESSION['ho_ten'] : 'Admin') . "</p>"; ?>
        <button onclick="window.location.href='../views/index/webindex.php'" class="logout">Trở lại</button>
      </div>
    </header>

    <section class="content">
      <!-- Cards thống kê chính -->
      <div class="stats-grid">
        <div class="stat-card blue">
          <i class="bi bi-people-fill"></i>
          <h3><?php echo number_format($stats['active_users']); ?></h3>
          <p>Người dùng</p>
        </div>

        <div class="stat-card green">
          <i class="bi bi-geo-alt-fill"></i>
          <h3><?php echo number_format($stats['active_tours']); ?></h3>
          <p>Tour hoạt động</p>
        </div>

        <div class="stat-card orange">
          <i class="bi bi-clock-history"></i>
          <h3><?php echo number_format($stats['pending_bookings']); ?></h3>
          <p>Chờ xác nhận</p>
        </div>

        <div class="stat-card purple">
          <i class="bi bi-cash-stack"></i>
          <h3><?php echo number_format($stats['monthly_revenue'] / 1000000, 1); ?>M</h3>
          <p>Doanh thu tháng</p>
        </div>

        <div class="stat-card red">
          <i class="bi bi-exclamation-triangle-fill"></i>
          <h3><?php echo number_format($stats['pending_complaints']); ?></h3>
          <p>Khiếu nại</p>
        </div>
      </div>

      <!-- Cards thống kê nội dung -->
      <div class="stats-grid">
        <div class="stat-card teal">
          <i class="bi bi-file-earmark-text-fill"></i>
          <h3><?php echo number_format($stats['active_posts']); ?></h3>
          <p>Bài viết</p>
        </div>

        <div class="stat-card pink">
          <i class="bi bi-compass-fill"></i>
          <h3><?php echo number_format($stats['active_explore']); ?></h3>
          <p>Khám phá</p>
        </div>

        <div class="stat-card indigo">
          <i class="bi bi-chat-dots-fill"></i>
          <h3><?php echo number_format($stats['total_comments']); ?></h3>
          <p>Bình luận</p>
        </div>
      </div>

      <!-- Biểu đồ doanh thu và trạng thái -->
      <div class="charts-row">
        <div class="chart-box">
          <h3 class="chart-title">
            <i class="bi bi-graph-up"></i>
            Doanh thu 6 tháng gần nhất
          </h3>
          <div class="chart-wrapper">
            <canvas id="revenueChart"></canvas>
          </div>
        </div>

        <div class="chart-box">
          <h3 class="chart-title">
            <i class="bi bi-pie-chart"></i>
            Trạng thái đơn
          </h3>
          <div class="chart-wrapper">
            <canvas id="statusChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Biểu đồ khám phá và bình luận -->
      <div class="charts-row">
        <div class="chart-box">
          <h3 class="chart-title">
            <i class="bi bi-bookmark-star-fill"></i>
            Nội dung khám phá theo loại
          </h3>
          <div class="chart-wrapper">
            <canvas id="exploreChart"></canvas>
          </div>
        </div>

        <div class="chart-box">
          <h3 class="chart-title">
            <i class="bi bi-chat-square-text-fill"></i>
            Bình luận 6 tháng
          </h3>
          <div class="chart-wrapper">
            <canvas id="commentsChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Top 5 tour -->
      <div class="top-tour-box">
        <h3 class="chart-title">
          <i class="bi bi-star-fill"></i>
          Top 5 Tour doanh thu cao
        </h3>
        <div class="chart-wrapper">
          <canvas id="topChart"></canvas>
        </div>
      </div>

    </section>
  </div>

<div class="sidebar-overlay"></div>
<script src="../../js/Main5.js"></script>
<script>
Chart.defaults.color = '#999';
Chart.defaults.borderColor = '#444';

// 1. Doanh thu 6 tháng
new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: [<?php echo implode(',', $months); ?>],
        datasets: [{
            label: 'Doanh thu',
            data: [<?php echo implode(',', $revenues); ?>],
            borderColor: '#3dcce2',
            backgroundColor: 'rgba(61, 204, 226, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointRadius: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: (ctx) => new Intl.NumberFormat('vi-VN').format(ctx.parsed.y) + ' ₫'
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: (val) => new Intl.NumberFormat('vi-VN', {notation: 'compact'}).format(val)
                },
                grid: { color: '#333' }
            },
            x: { grid: { color: '#333' } }
        }
    }
});

// 2. Trạng thái đơn
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: [<?php echo implode(',', $status_labels); ?>],
        datasets: [{
            data: [<?php echo implode(',', $status_data); ?>],
            backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { 
                    color: '#fff', 
                    font: { size: 11 },
                    padding: 10
                }
            }
        }
    }
});

// 3. Nội dung khám phá theo loại
new Chart(document.getElementById('exploreChart'), {
    type: 'pie',
    data: {
        labels: [<?php echo implode(',', $explore_labels); ?>],
        datasets: [{
            data: [<?php echo implode(',', $explore_counts); ?>],
            backgroundColor: ['#ff6b6b', '#4ecdc4', '#45b7d1'],
            borderWidth: 2,
            borderColor: '#2a2a2a'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { 
                    color: '#fff', 
                    font: { size: 12 },
                    padding: 15
                }
            },
            tooltip: {
                callbacks: {
                    label: function(ctx) {
                        const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((ctx.parsed / total) * 100).toFixed(1);
                        return ctx.label + ': ' + ctx.parsed + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// 4. Bình luận theo tháng
new Chart(document.getElementById('commentsChart'), {
    type: 'bar',
    data: {
        labels: [<?php echo implode(',', $comment_months); ?>],
        datasets: [{
            label: 'Số bình luận',
            data: [<?php echo implode(',', $comment_counts); ?>],
            backgroundColor: 'rgba(67, 233, 123, 0.8)',
            borderColor: 'rgba(67, 233, 123, 1)',
            borderWidth: 2,
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 },
                grid: { color: '#333' }
            },
            x: {
                grid: { display: false }
            }
        }
    }
});

// 5. Top 5 tour
new Chart(document.getElementById('topChart'), {
    type: 'bar',
    data: {
        labels: [<?php echo implode(',', $tour_names); ?>],
        datasets: [{
            data: [<?php echo implode(',', $tour_revenues); ?>],
            backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff'],
            borderRadius: 6
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: (ctx) => new Intl.NumberFormat('vi-VN').format(ctx.parsed.x) + ' ₫'
                }
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                ticks: {
                    callback: (val) => new Intl.NumberFormat('vi-VN', {notation: 'compact'}).format(val)
                },
                grid: { color: '#333' }
            },
            y: {
                ticks: { font: { size: 11 } },
                grid: { display: false }
            }
        }
    }
});
</script>
</body>
</html>
<?php $conn->close(); ?>