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

<!-- Partners Section Start -->
<section class="partners-section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-10 col-lg-8">
        <div class="section-title">
          <h2 class="title">Our Trusted Partners</h2>
          <p>We collaborate with industry leaders to deliver the highest quality water purification solutions across the Maldives.</p>
          <div class="text-center mt-3">
            <a href="/Waterfillter-system-web/dashboard/" class="btn btn-sm btn-outline-primary">Admin Dashboard</a>
          </div>
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
