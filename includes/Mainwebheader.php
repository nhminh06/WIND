<?php
// Lấy thống kê từ database
include '../../../db/db.php';

// 1. Đếm tổng số tour đang hoạt động
$sql_tours = "SELECT COUNT(*) as total FROM tour WHERE trang_thai = 1";
$result_tours = mysqli_query($conn, $sql_tours);
$total_tours = 0;
if ($result_tours) {
    $row = mysqli_fetch_assoc($result_tours);
    $total_tours = (int)$row['total'];
}

// 2. Đếm tổng số khách hàng (từ bảng dat_tour)
$sql_customers = "SELECT SUM(so_nguoi_lon + so_tre_em + so_tre_nho) as total FROM dat_tour WHERE trang_thai != 'cancelled'";
$result_customers = mysqli_query($conn, $sql_customers);
$total_customers = 0;
if ($result_customers) {
    $row = mysqli_fetch_assoc($result_customers);
    $total_customers = (int)$row['total'];
}

// 3. Đếm số điểm đến (số vi_tri unique)
$sql_destinations = "SELECT COUNT(DISTINCT vi_tri) as total FROM tour WHERE trang_thai = 1 AND vi_tri IS NOT NULL AND vi_tri != ''";
$result_destinations = mysqli_query($conn, $sql_destinations);
$total_destinations = 0;
if ($result_destinations) {
    $row = mysqli_fetch_assoc($result_destinations);
    $total_destinations = (int)$row['total'];
}

// 4. Đếm tổng số đánh giá
$sql_reviews = "SELECT COUNT(*) as total FROM danh_gia";
$result_reviews = mysqli_query($conn, $sql_reviews);
$total_reviews = 0;
if ($result_reviews) {
    $row = mysqli_fetch_assoc($result_reviews);
    $total_reviews = (int)$row['total'];
}

// 5. Lấy dữ liệu slider từ database
$sql_slider = "SELECT * FROM banner_slider ORDER BY thu_tu LIMIT 4";
$result_slider = mysqli_query($conn, $sql_slider);

// Tạo mảng slider với dữ liệu mặc định nếu không có trong DB
$slider_images = [];
$default_slides = [
    [
        'hinh_anh' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1200&h=520&fit=crop',
        'tieu_de' => 'Khám Phá Thiên Nhiên',
        'mo_ta' => 'Trải nghiệm những cảnh đẹp tuyệt vời'
    ],
    [
        'hinh_anh' => 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=1200&h=520&fit=crop',
        'tieu_de' => 'Hành Trình Mới',
        'mo_ta' => 'Bắt đầu cuộc phiêu lưu của bạn'
    ],
    [
        'hinh_anh' => 'https://images.unsplash.com/photo-1501594907352-04cda38ebc29?w=1200&h=520&fit=crop',
        'tieu_de' => 'Kỷ Niệm Đáng Nhớ',
        'mo_ta' => 'Tạo những khoảnh khắc khó quên'
    ],
    [
        'hinh_anh' => 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=1200&h=520&fit=crop',
        'tieu_de' => 'Điểm Đến Tuyệt Vời',
        'mo_ta' => 'Khám phá vẻ đẹp thế giới'
    ]
];

// Lấy dữ liệu từ DB
while ($row = mysqli_fetch_assoc($result_slider)) {
    $slider_images[] = $row;
}

// Nếu không có dữ liệu từ DB, dùng dữ liệu mặc định
if (empty($slider_images)) {
    $slider_images = $default_slides;
}

// Đảm bảo luôn có đủ 4 slide
while (count($slider_images) < 4) {
    $slider_images[] = $default_slides[count($slider_images) % 4];
}
?>

<div class="mainheader box fade-up3">
    <div class="slider-container">
        <?php foreach ($slider_images as $index => $slide): ?>
        <!-- Slide <?php echo $index + 1; ?> -->
        <div class="slide <?php echo $index === 0 ? 'active' : ''; ?>">
            <img src="<?php echo "../../../" . htmlspecialchars($slide['hinh_anh']); ?>" 
                 alt="<?php echo htmlspecialchars($slide['tieu_de']); ?>"
                 onerror="this.src='https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1200&h=520&fit=crop'">
            <div class="slide-overlay">
                <h2 class="slide-title"><?php echo htmlspecialchars($slide['tieu_de']); ?></h2>
                <p class="slide-description"><?php echo htmlspecialchars($slide['mo_ta']); ?></p>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- Navigation Buttons -->
        <button class="nav-button prev-button" onclick="prevSlide()" aria-label="Previous slide">❮</button>
        <button class="nav-button next-button" onclick="nextSlide()" aria-label="Next slide">❯</button>

        <!-- Dots Indicator -->
        <div class="dots-container">
            <?php for ($i = 0; $i < count($slider_images); $i++): ?>
            <span class="dot <?php echo $i === 0 ? 'active' : ''; ?>" 
                  onclick="goToSlide(<?php echo $i; ?>)"
                  aria-label="Go to slide <?php echo $i + 1; ?>"></span>
            <?php endfor; ?>
        </div>
    </div>
</div>

<nav class="quick-nav box fade-up3">
    <div class="quick-nav-container">
        <div class="nav-item">
            <a href="#about-section" class="nav-link">
                <span class="nav-icon"><i class="bi bi-star-fill"></i></span>
                <span>Giới Thiệu</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#content-section" class="nav-link">
                <span class="nav-icon"><i class="bi bi-newspaper"></i></span>
                <span>Nội Dung</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#destination-section" class="nav-link">
                <span class="nav-icon"><i class="bi bi-map-fill"></i></span>
                <span>Điểm Đến</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#featured-tours" class="nav-link">
                <span class="nav-icon"><i class="bi bi-airplane-fill"></i></span>
                <span>Tour Nổi Bật</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#guide-team" class="nav-link">
                <span class="nav-icon"><i class="bi bi-people"></i></span>
                <span>Đội Ngũ</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#reviews-section" class="nav-link">
                <span class="nav-icon"><i class="bi bi-chat-fill"></i></span>
                <span>Đánh Giá</span>
            </a>
        </div>
    </div>
</nav>

<section class="quick-stats box fade-up3">
    <div class="stats-container">
        <div class="stat-item">
            <span class="stat-icon"><i class="bi bi-airplane-fill"></i></span>
            <span class="stat-number">
                <span class="counter" data-target="<?php echo $total_tours; ?>">0</span>
                <span class="stat-plus">+</span>
            </span>
            <span class="stat-label">Tour Du Lịch</span>
        </div>
        
        <div class="stat-item">
            <span class="stat-icon"><i class="bi bi-emoji-smile"></i></span>
            <span class="stat-number">
                <span class="counter" data-target="<?php echo $total_customers; ?>">0</span>
                <span class="stat-plus">+</span>
            </span>
            <span class="stat-label">Khách Hàng</span>
        </div>
        
        <div class="stat-item">
            <span class="stat-icon"><i class="bi bi-map-fill"></i></span>
            <span class="stat-number">
                <span class="counter" data-target="<?php echo $total_destinations; ?>">0</span>
                <span class="stat-plus">+</span>
            </span>
            <span class="stat-label">Điểm Đến</span>
        </div>
        
        <div class="stat-item">
            <span class="stat-icon"><i class="bi bi-star-fill"></i></span>
            <span class="stat-number">
                <span class="counter" data-target="<?php echo $total_reviews; ?>">0</span>
                <span class="stat-plus">+</span>
            </span>
            <span class="stat-label">Đánh Giá</span>
        </div>
    </div>
</section>

<script>
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    const totalSlides = slides.length;
    let autoSlideInterval;

    // Hàm hiển thị slide
    function showSlide(index) {
        // Xóa class active khỏi tất cả slides và dots
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));

        // Thêm class active cho slide và dot hiện tại
        slides[index].classList.add('active');
        dots[index].classList.add('active');
    }

    // Chuyển sang slide tiếp theo
    function nextSlide() {
        currentSlide = (currentSlide + 1) % totalSlides;
        showSlide(currentSlide);
        resetAutoSlide();
    }

    // Quay lại slide trước
    function prevSlide() {
        currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
        showSlide(currentSlide);
        resetAutoSlide();
    }

    // Chuyển đến slide cụ thể
    function goToSlide(index) {
        currentSlide = index;
        showSlide(currentSlide);
        resetAutoSlide();
    }

    // Tự động chuyển slide sau 5 giây
    function startAutoSlide() {
        autoSlideInterval = setInterval(() => {
            nextSlide();
        }, 5000);
    }

    // Reset auto slide khi người dùng tương tác
    function resetAutoSlide() {
        clearInterval(autoSlideInterval);
        startAutoSlide();
    }

    // Bắt đầu auto slide khi trang load
    startAutoSlide();

    // Hỗ trợ keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            prevSlide();
        } else if (e.key === 'ArrowRight') {
            nextSlide();
        }
    });

    // Pause auto slide khi hover
    const sliderContainer = document.querySelector('.slider-container');
    if (sliderContainer) {
        sliderContainer.addEventListener('mouseenter', () => {
            clearInterval(autoSlideInterval);
        });
        
        sliderContainer.addEventListener('mouseleave', () => {
            startAutoSlide();
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Smooth scroll cho các link navigation
        const navLinks = document.querySelectorAll('.nav-link');
        const sections = document.querySelectorAll('section, .about, .demo, .destination-section, .tuor_list, .guide_team, .reviews-grid');
        
        // Thêm ID cho các section nếu chưa có
        const sectionIds = ['about-section', 'content-section', 'destination-section', 'featured-tours', 'guide-team', 'reviews-section'];
        
        // Gán ID cho các section
        if (document.querySelector('.about')) {
            document.querySelector('.about').id = 'about-section';
        }
        
        if (document.querySelector('.demo')) {
            document.querySelector('.demo').id = 'content-section';
        }
        
        if (document.querySelector('.destination-section')) {
            document.querySelector('.destination-section').id = 'destination-section';
        }
        
        const tourLists = document.querySelectorAll('.tuor_list');
        if (tourLists.length > 0) {
            tourLists[0].id = 'featured-tours';
        }
        
        if (document.querySelector('.guide_team')) {
            document.querySelector('.guide_team').id = 'guide-team';
        }
        
        if (document.querySelector('.reviews-grid')) {
            document.querySelector('.reviews-grid').id = 'reviews-section';
        }
        
        // Smooth scroll khi click
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href').substring(1);
                const targetSection = document.getElementById(targetId);
                
                if (targetSection) {
                    const navHeight = document.querySelector('.quick-nav') ? document.querySelector('.quick-nav').offsetHeight : 0;
                    const targetPosition = targetSection.offsetTop - navHeight - 20;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                    
                    // Update active state
                    navLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });
        
        // Highlight active section khi scroll
        window.addEventListener('scroll', function() {
            let current = '';
            const quickNav = document.querySelector('.quick-nav');
            const navHeight = quickNav ? quickNav.offsetHeight : 0;
            
            sectionIds.forEach(id => {
                const section = document.getElementById(id);
                if (section) {
                    const sectionTop = section.offsetTop - navHeight - 100;
                    const sectionBottom = sectionTop + section.offsetHeight;
                    
                    if (window.pageYOffset >= sectionTop && window.pageYOffset < sectionBottom) {
                        current = id;
                    }
                }
            });
            
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href').substring(1) === current) {
                    link.classList.add('active');
                }
            });
        });
        
        // Scroll to top button
        const scrollTopBtn = document.createElement('button');
        scrollTopBtn.innerHTML = '↑';
        scrollTopBtn.className = 'scroll-top-btn';
        scrollTopBtn.setAttribute('aria-label', 'Scroll to top');
        scrollTopBtn.style.cssText = `
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            font-size: 24px;
            cursor: pointer;
            display: none;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        `;
        
        document.body.appendChild(scrollTopBtn);
        
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                scrollTopBtn.style.display = 'block';
            } else {
                scrollTopBtn.style.display = 'none';
            }
        });
        
        scrollTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        scrollTopBtn.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
        });
        
        scrollTopBtn.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';  
        });
    });

    // Counter Animation khi scroll vào view
    function startCounterAnimation() {
        const counters = document.querySelectorAll('.counter');
        const speed = 200; // Tốc độ animation
        
        const animateCounter = (counter) => {
            const target = parseInt(counter.getAttribute('data-target'));
            const increment = target / speed;
            let current = 0;
            
            const updateCounter = () => {
                current += increment;
                if (current < target) {
                    counter.textContent = Math.ceil(current);
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target;
                }
            };
            
            updateCounter();
        };
        
        // Intersection Observer để chạy animation khi scroll vào view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counters = entry.target.querySelectorAll('.counter');
                    counters.forEach(counter => {
                        if (counter.textContent === '0') {
                            animateCounter(counter);
                        }
                    });
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        const statsSection = document.querySelector('.quick-stats');
        if (statsSection) {
            observer.observe(statsSection);
        }
    }

    // Chạy animation khi trang load xong
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', startCounterAnimation);
    } else {
        startCounterAnimation();
    }
</script>