<?php
// Start the session
session_start();

// Get the entire HTML content
$html = file_get_contents('index.html');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RETAW Solutions - Water Purification Systems</title>
    <!-- Include all the head content from the original index.html file -->
    <?php
    // Extract head content
    preg_match('/<head>(.*?)<\/head>/s', $html, $matches);
    $head_content = $matches[1];
    
    // Remove the title tag as we've already added it
    $head_content = preg_replace('/<title>.*?<\/title>/', '', $head_content);
    
    echo $head_content;
    ?>
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
<body class="home9">
    <?php
    // Extract the body content up to the partners section
    if (preg_match('/<body class="home9">(.*?)<!-- Partners Section Start -->/s', $html, $matches)) {
        $body_start = $matches[1];
        echo $body_start;
    }
    
    // Include the dynamic partners section
    include 'includes/partners.php';
    
    // Get the body content after the partners section
    if (preg_match('/<!-- Partners Section End -->(.*?)<\/body>/s', $html, $matches)) {
        $body_end = $matches[1];
        echo $body_end;
    }
    ?>
</body>
</html>
