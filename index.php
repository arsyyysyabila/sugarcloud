<?php 
session_start(); 
?>
<?php include('navbar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SugarCloudCafe — Home</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        /* Global Styles */
        :root {
            --primary-color: #e2c1a9;
            --bg-dark: #1b0f0a;
            --card-bg: #2b1a13;
            --text-light: #ffffffcc;
            --transition: 0.3s ease;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }

        body {
            font-family: 'Poppins', sans-serif; 
            background: var(--bg-dark); 
            color: white; 
            line-height: 1.6;
            overflow-x: hidden;
        }

        

    

        .login-btn {
            background: var(--primary-color); 
            color: var(--bg-dark) !important; 
            padding: 8px 20px; 
            border-radius: 20px; 
            font-weight: 600;
        }

        .logout-btn {
            background: #ff8e8e;
            color: #1b0f0a !important;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
        }

        /* Hero Slider */
        .hero {
            height: 100vh; 
            position: relative; 
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .slider {
            position: absolute; 
            top: 0; left: 0;
            width: 100%; height: 100%; 
            z-index: -1;
        }

        .slide {
            position: absolute; 
            width: 100%; height: 100%; 
            background-size: cover; 
            background-position: center; 
            opacity: 0; 
            transition: opacity 1.5s ease-in-out;
        }

        .slide.active { opacity: 1; }

        .hero-overlay {
            text-align: center; 
            background: rgba(0, 0, 0, 0.4); 
            padding: 40px;
            border-radius: 20px;
        }

        .hero-overlay h1 {
            font-family: "Great Vibes"; 
            font-size: clamp(3rem, 8vw, 5rem); 
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .order-btn {
            display: inline-block; 
            margin-top: 25px; 
            padding: 12px 40px; 
            background: var(--primary-color); 
            color: var(--bg-dark); 
            border-radius: 30px; 
            font-weight: 600; 
            text-decoration: none;
            transition: var(--transition);
        }

        /* Sections */
        section { padding: 100px 10%; text-align: center; }
        
        h2 {
            font-family: "Great Vibes"; 
            font-size: 3rem; 
            color: var(--primary-color); 
            margin-bottom: 30px;
        }

        /* Review Grid */
        .review-grid {
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
            gap: 30px; 
            margin-top: 40px;
        }

        .review-card {
            background: var(--card-bg); 
            padding: 30px; 
            border-radius: 20px; 
            transition: var(--transition);
        }

        .review-card:hover { transform: translateY(-10px); }

        .review-card img {
            width: 70px; height: 70px; 
            border-radius: 50%; 
            margin-bottom: 15px;
            border: 2px solid var(--primary-color);
        }

        .stars { color: #ffcc00; margin-bottom: 10px; }

        /* Contact Details */
        .closed-tag {
            color: #ff8e8e;
            font-weight: 600;
            display: block;
            margin-top: 5px;
        }

        /* Footer */
        .footer {
            padding: 40px; 
            background: #110906; 
            font-size: 0.8rem; 
            color: #777;
        }

        @media (max-width: 768px) {
            nav ul { display: none; }
            .hero-overlay { width: 90%; }
        }
    </style>
</head>

<body>

    

    <section class="hero" id="home">
        <div class="slider">
            <div class="slide active" style="background-image: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('images/cake 1.png')"></div>
            <div class="slide" style="background-image: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('images/cake 2.png')"></div>
            <div class="slide" style="background-image: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('images/coffee.png')"></div>
        </div>

        <div class="hero-overlay">
            <h1>Welcome to SugarCloudCafe</h1>
            <p>Where every bite feels like a sweet cloud on your tongue.</p>
            <a href="menu.php" class="order-btn">Explore Menu</a>
        </div>
    </section>

    <section class="about" id="about">
        <h2>About Us</h2>
        <p style="max-width: 800px; margin: auto;">SugarCloudCafe was born from a passion for crafting enchanting desserts. Every recipe blends tradition and creativity to bring you a heavenly experience.</p>
    </section>

    <section class="reviews" id="reviews">
        <h2>Customer Reviews</h2>
        <div class="review-grid">
            <div class="review-card">
                <img src="images/aisyah.png" alt="Aisyah">
                <h4>Aisyah</h4>
                <div class="stars">★★★★★</div>
                <p>"The desserts are SO soft and delicious. Beautiful cafe!"</p>
            </div>
            <div class="review-card">
                <img src="images/amir.png" alt="Amir">
                <h4>Amir</h4>
                <div class="stars">★★★★★</div>
                <p>"Worth every ringgit. The chocolate lava cake is insane."</p>
            </div>
            <div class="review-card">
                <img src="images/dp 1.png" alt="Sofia">
                <h4>Sofia</h4>
                <div class="stars">★★★★☆</div>
                <p>"Very cozy place. Perfect for date & study."</p>
            </div>
        </div>
    </section>

    <section class="contact" id="contact">
        <h2>Contact Info</h2>
        <div style="line-height: 2;">
            <p>📍 Sungai Petani, Kedah</p>
            <p>📞 014-789 2345</p>
            <p>✉️ sugarcloudcafe@gmail.com</p>
            <p>🕒 1 PM – 10 PM <br> <span class="closed-tag">(Closed Every Wednesday)</span></p>
        </div>
    </section>

    <footer class="footer">
        <p>&copy; 2026 SugarCloudCafe — All Rights Reserved.</p>
    </footer>

    <script>
        let slides = document.querySelectorAll(".slide");
        let index = 0;
        
        function nextSlide() {
            slides[index].classList.remove("active");
            index = (index + 1) % slides.length;
            slides[index].classList.add("active");
        }

        setInterval(nextSlide, 5000); 
    </script>

</body>
</html>