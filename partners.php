<?php
// Include database connection
require_once "config/db.php";

// Fetch all partner logos
$partners = [];
$sql = "SELECT id, name, logo_path FROM partners ORDER BY id DESC";
if($result = $mysqli->query($sql)){
    while($row = $result->fetch_assoc()){
        $partners[] = $row;
    }
    $result->free();
}

// Close connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Partners</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .partners-section {
            padding: 60px 0;
            background-color: #f8f9fa;
            overflow: hidden;
            position: relative;
        }
        
        .logo-carousel-container {
            width: 100%;
            overflow: hidden;
            position: relative;
            padding: 30px 0;
        }
        
        .logo-carousel-container::before,
        .logo-carousel-container::after {
            content: "";
            position: absolute;
            top: 0;
            width: 100px;
            height: 100%;
            z-index: 2;
        }
        
        .logo-carousel-container::before {
            left: 0;
            background: linear-gradient(to right, #f8f9fa, transparent);
        }
        
        .logo-carousel-container::after {
            right: 0;
            background: linear-gradient(to left, #f8f9fa, transparent);
        }
        
        .logo-track {
            display: flex;
            width: fit-content;
        }
        
        .logo-slide {
            flex: 0 0 200px;
            height: 100px;
            margin: 0 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .logo-slide:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .logo-slide img {
            max-width: 80%;
            max-height: 60px;
            filter: grayscale(100%);
            opacity: 0.7;
            transition: all 0.3s ease;
        }
        
        .logo-slide:hover img {
            filter: grayscale(0%);
            opacity: 1;
        }
        
        @media (max-width: 768px) {
            .logo-slide {
                flex: 0 0 160px;
                height: 80px;
                margin: 0 15px;
            }
            
            .logo-slide img {
                max-height: 50px;
            }
        }
    </style>
</head>
<body>
    <!-- Partners Section Start -->
    <section class="partners-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-8">
                    <div class="section-title text-center">
                        <h2 class="title">Our Trusted Partners</h2>
                        <p>We collaborate with industry leaders to deliver the highest quality water purification solutions across the Maldives.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="logo-carousel-container">
            <div class="logo-track" id="logoTrack">
                <?php if(empty($partners)): ?>
                    <!-- Default logos if no partners in database -->
                    <div class="logo-slide"><img src="assets/images/partners/partner1.png" alt="Partner 1"></div>
                    <div class="logo-slide"><img src="assets/images/partners/partner2.png" alt="Partner 2"></div>
                    <div class="logo-slide"><img src="assets/images/partners/partner3.png" alt="Partner 3"></div>
                    <div class="logo-slide"><img src="assets/images/partners/partner4.png" alt="Partner 4"></div>
                    <div class="logo-slide"><img src="assets/images/partners/partner5.png" alt="Partner 5"></div>
                    <div class="logo-slide"><img src="assets/images/partners/partner6.png" alt="Partner 6"></div>
                <?php else: ?>
                    <!-- Dynamic logos from database -->
                    <?php foreach($partners as $partner): ?>
                        <div class="logo-slide">
                            <img src="<?php echo htmlspecialchars($partner['logo_path']); ?>" alt="<?php echo htmlspecialchars($partner['name']); ?>">
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="container mt-4">
            <div class="row">
                <div class="col-12 text-center">
                    <a href="index.html" class="btn btn-primary">Back to Home</a>
                </div>
            </div>
        </div>
    </section>
    <!-- Partners Section End -->
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the logo track element
            const logoTrack = document.getElementById('logoTrack');
            const slides = logoTrack.querySelectorAll('.logo-slide');
            
            // Clone slides to create an infinite loop
            slides.forEach(function(slide) {
                const clone = slide.cloneNode(true);
                logoTrack.appendChild(clone);
            });
            
            // Set up the animation
            const slideWidth = slides[0].offsetWidth + 40; // Width + margin
            const totalWidth = slideWidth * slides.length;
            let position = 0;
            
            // Animate the carousel
            function animateCarousel() {
                position -= 1;
                
                // Reset position when we've moved half the width (original set of slides)
                if (Math.abs(position) >= totalWidth) {
                    position = 0;
                }
                
                logoTrack.style.transform = `translateX(${position}px)`;
                requestAnimationFrame(animateCarousel);
            }
            
            // Start the animation
            requestAnimationFrame(animateCarousel);
            
            // Pause animation on hover
            logoTrack.addEventListener('mouseenter', function() {
                logoTrack.style.animationPlayState = 'paused';
            });
            
            logoTrack.addEventListener('mouseleave', function() {
                logoTrack.style.animationPlayState = 'running';
            });
        });
    </script>
</body>
</html>
