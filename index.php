<?php require __DIR__.'/app.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travenzo Travels Sri Lanka | Luxury Private Tours & Safaris</title>
    <meta name="description" content="Experience the pinnacle of luxury travel in Sri Lanka.">
    <link rel="icon" type="image/jpeg" href="assets/travenzo-logo.jpeg">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,800;1,400&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --ocean-blue: #0A6EBD; --luxury-gold: #D4AF37; --white: #FFFFFF; --dark-navy: #070f1c; --light-bg: #0d1526; --card-bg: #111d33; --text-light: #e0e0e0; --text-dim: #a0a0a0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Poppins', sans-serif; color: var(--text-light); background: var(--dark-navy); line-height: 1.7; }
        ::-webkit-scrollbar { width: 8px; } ::-webkit-scrollbar-track { background: var(--dark-navy); } ::-webkit-scrollbar-thumb { background: var(--luxury-gold); }
        h1, h2, h3, h4, h5, h6 { font-family: 'Playfair Display', serif; color: var(--white); font-weight: 600; }
        a { text-decoration: none; color: inherit; } ul { list-style: none; } img { max-width: 100%; height: auto; }
        .btn-primary, .btn-secondary, .btn-outline { font-family: 'Montserrat', sans-serif; font-weight: 500; padding: 14px 35px; border: 1px solid transparent; cursor: pointer; transition: all 0.4s ease; display: inline-block; text-transform: uppercase; letter-spacing: 2px; font-size: 0.85rem; border-radius: 0; }
        .btn-primary { background: var(--luxury-gold); color: var(--dark-navy); } .btn-primary:hover { background: transparent; border-color: var(--luxury-gold); color: var(--luxury-gold); }
        .btn-secondary { background: transparent; border-color: var(--white); color: var(--white); } .btn-secondary:hover { background: var(--white); color: var(--dark-navy); }
        .btn-outline { background: transparent; border-color: var(--text-dim); color: var(--white); } .btn-outline:hover { border-color: var(--luxury-gold); color: var(--luxury-gold); }
        .section-padding { padding: 100px 0; } .bg-light { background: var(--light-bg); } .bg-dark { background: var(--dark-navy); }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 40px; }
        .section-header { text-align: center; margin-bottom: 60px; }
        .sub-title { font-family: 'Montserrat', sans-serif; color: var(--luxury-gold); text-transform: uppercase; letter-spacing: 3px; font-size: 0.8rem; display: block; margin-bottom: 10px; }
        .section-header h2 { font-size: 2.8rem; margin-bottom: 15px; } .section-header h2::after { content: ''; display: block; width: 50px; height: 2px; background: var(--luxury-gold); margin: 15px auto 0; }
        .section-header p { color: var(--text-dim); font-weight: 300; } .section-header.light p { color: var(--text-dim); }
        #header { position: fixed; width: 100%; top: 0; left: 0; z-index: 1000; transition: all 0.4s ease; padding: 20px 0; background: rgba(7, 15, 28, 0.3); backdrop-filter: blur(5px); }
        #header.scrolled { background: rgba(7, 15, 28, 0.95); padding: 12px 0; border-bottom: 1px solid rgba(212, 175, 55, 0.2); }
        .navbar { display: flex; justify-content: space-between; align-items: center; }
        .logo img { height: 55px; transition: all 0.3s ease; filter: brightness(0) invert(1); } #header.scrolled .logo img { height: 45px; }
        .nav-menu { display: flex; gap: 35px; }
        .nav-link { font-family: 'Montserrat', sans-serif; font-weight: 400; color: var(--white); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; position: relative; transition: color 0.3s; }
        .nav-link::after { content: ''; position: absolute; bottom: -5px; left: 0; width: 0; height: 1px; background: var(--luxury-gold); transition: width 0.4s; }
        .nav-link:hover::after, .nav-link.active::after { width: 100%; } .nav-link:hover { color: var(--luxury-gold); }
        .nav-cta { display: flex; align-items: center; gap: 25px; } .hamburger { display: none; font-size: 24px; color: var(--white); cursor: pointer; }
        .hero { height: 100vh; position: relative; overflow: hidden; }
        .hero-slider { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center; opacity: 0; transition: opacity 2s ease-in-out; } .hero-slider.active { opacity: 1; }
        .hero-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(rgba(7, 15, 28, 0.8), rgba(7, 15, 28, 0.5)); display: flex; justify-content: center; align-items: center; text-align: center; }
        .hero-content h1 { font-size: 4.5rem; color: var(--white); margin-bottom: 20px; letter-spacing: 2px; }
        .hero-content p { font-size: 1.1rem; margin-bottom: 40px; max-width: 800px; margin-left: auto; margin-right: auto; color: var(--text-light); font-weight: 300; letter-spacing: 1px; }
        .hero-buttons { display: flex; gap: 20px; justify-content: center; }
        .scroll-down-indicator { position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); color: var(--white); text-align: center; font-family: 'Montserrat', sans-serif; font-size: 0.8rem; letter-spacing: 2px; }
        .scroll-down-indicator span { display: block; width: 20px; height: 30px; border: 1px solid var(--luxury-gold); border-radius: 10px; margin: 0 auto 10px; }
        .about-content { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; margin-bottom: 80px; }
        .about-text p { font-size: 1.1rem; color: var(--text-dim); font-weight: 300; }
        .about-image img { border-radius: 4px; box-shadow: 0 20px 50px rgba(0,0,0,0.5); border: 1px solid rgba(212, 175, 55, 0.3); }
        .why-choose-us h3 { text-align: center; font-size: 2rem; margin-bottom: 40px; color: var(--white); }
        .features-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
        .feature-card { background: var(--card-bg); padding: 40px 20px; text-align: center; border-radius: 4px; border: 1px solid rgba(255,255,255,0.05); transition: all 0.4s ease; }
        .feature-card:hover { transform: translateY(-10px); border-color: var(--luxury-gold); background: rgba(212, 175, 55, 0.05); }
        .feature-card i { font-size: 2rem; color: var(--luxury-gold); margin-bottom: 20px; }
        .feature-card h4 { font-size: 1rem; color: var(--white); font-family: 'Montserrat', sans-serif; text-transform: uppercase; letter-spacing: 1px; }
        .packages-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; max-width: 900px; margin: 0 auto; }
        .package-card { background: var(--card-bg); border-radius: 4px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.3); transition: all 0.4s ease; position: relative; border: 1px solid rgba(255,255,255,0.05); }
        .package-card:hover { transform: scale(1.02); border-color: var(--luxury-gold); }
        .package-badge { position: absolute; top: 20px; right: 20px; background: var(--luxury-gold); color: var(--dark-navy); padding: 5px 15px; border-radius: 2px; font-family: 'Montserrat', sans-serif; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
        .package-header { padding: 40px; text-align: center; background: rgba(255,255,255,0.02); border-bottom: 1px solid rgba(255,255,255,0.05); }
        .package-header h3 { color: var(--white); font-size: 1.8rem; letter-spacing: 1px; }
        .package-pricing { padding: 30px 40px; display: flex; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .price-tier { text-align: center; } .price-tier span { display: block; font-size: 0.8rem; color: var(--text-dim); text-transform: uppercase; } .price-tier strong { font-family: 'Playfair Display', serif; font-size: 1.6rem; color: var(--luxury-gold); }
        .package-includes { padding: 30px 40px; } .package-includes li { margin-bottom: 15px; padding-left: 25px; position: relative; color: var(--text-light); font-size: 0.95rem; font-weight: 300; }
        .package-includes li i { position: absolute; left: 0; top: 5px; color: var(--luxury-gold); font-size: 0.8rem; }
        .package-card .btn-primary, .package-card .btn-outline { display: block; width: calc(100% - 80px); margin: 0 auto 40px; text-align: center; }
        .timeline { position: relative; max-width: 1000px; margin: 0 auto; padding: 40px 0; }
        .timeline::before { content: ''; position: absolute; top: 0; left: 50%; transform: translateX(-50%); width: 1px; height: 100%; background: rgba(212, 175, 55, 0.3); }
        .timeline-item { width: 50%; padding: 20px 40px; position: relative; } .timeline-item.right { left: 50%; }
        .timeline-dot { position: absolute; top: 30px; right: -6px; width: 12px; height: 12px; background: var(--luxury-gold); border: 2px solid var(--dark-navy); border-radius: 50%; z-index: 1; } .timeline-item.right .timeline-dot { left: -6px; }
        .timeline-content { background: var(--card-bg); padding: 30px; border-radius: 4px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); position: relative; border: 1px solid rgba(255,255,255,0.05); transition: border-color 0.3s; } .timeline-content:hover { border-color: var(--luxury-gold); }
        .timeline-day { font-family: 'Montserrat', sans-serif; background: var(--luxury-gold); color: var(--dark-navy); padding: 3px 12px; border-radius: 2px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; display: inline-block; margin-bottom: 15px; }
        .timeline-content h3 { margin-bottom: 15px; color: var(--white); font-size: 1.4rem; }
        .timeline-content ul li { margin-bottom: 8px; font-size: 0.9rem; color: var(--text-dim); padding-left: 15px; position: relative; font-weight: 300; } .timeline-content ul li::before { content: '—'; position: absolute; left: 0; color: var(--luxury-gold); }
        .destinations-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
        .destination-card { background: var(--card-bg); border-radius: 4px; overflow: hidden; box-shadow: 0 15px 30px rgba(0,0,0,0.3); transition: all 0.4s ease; border: 1px solid rgba(255,255,255,0.05); } .destination-card:hover { transform: translateY(-5px); border-color: var(--luxury-gold); }
        .destination-card img { width: 100%; height: 260px; object-fit: cover; } .floating-photo { animation: floatPhoto 5s ease-in-out infinite; }
        @keyframes floatPhoto { 0% { transform: scale(1.05) translateY(0px); } 50% { transform: scale(1.05) translateY(-15px); } 100% { transform: scale(1.05) translateY(0px); } }
        .destination-info { padding: 25px; } .destination-info h3 { font-size: 1.3rem; margin-bottom: 10px; color: var(--white); } .destination-info p { color: var(--text-dim); font-size: 0.9rem; margin-bottom: 20px; font-weight: 300; }
        .read-more { font-family: 'Montserrat', sans-serif; font-weight: 500; color: var(--luxury-gold); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 2px; border-bottom: 1px solid transparent; transition: border-color 0.3s; } .read-more:hover { border-color: var(--luxury-gold); }
        .gallery-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; }
        .gallery-item { overflow: hidden; border-radius: 4px; cursor: pointer; height: 260px; position: relative; border: 1px solid rgba(255,255,255,0.05); }
        .gallery-item img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.8s ease, filter 0.5s ease; filter: grayscale(20%); } .gallery-item:hover img { transform: scale(1.1); filter: grayscale(0%); }
        .testimonial-slider { max-width: 800px; margin: 0 auto; position: relative; min-height: 200px; }
        .testimonial-card { background: var(--card-bg); padding: 50px; border-radius: 4px; text-align: center; display: none; border: 1px solid rgba(212, 175, 55, 0.2); } .testimonial-card.active { display: block; }
        .testimonial-card .stars { color: var(--luxury-gold); font-size: 1.2rem; margin-bottom: 20px; letter-spacing: 5px; } .testimonial-card p { font-size: 1.3rem; font-family: 'Playfair Display', serif; font-style: italic; color: var(--white); margin-bottom: 20px; }
        .contact-wrapper { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; } .contact-info ul { margin: 30px 0; }
        .contact-info li { margin-bottom: 20px; display: flex; align-items: center; gap: 20px; font-size: 1rem; color: var(--text-light); font-weight: 300; } .contact-info i { color: var(--luxury-gold); width: 20px; text-align: center; }
        .contact-socials, .footer-socials { display: flex; gap: 15px; margin: 30px 0; }
        .contact-socials a, .footer-socials a { width: 40px; height: 40px; border: 1px solid var(--text-dim); color: var(--white); display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: all 0.3s; } .contact-socials a:hover, .footer-socials a:hover { border-color: var(--luxury-gold); background: var(--luxury-gold); color: var(--dark-navy); }
        .map-embed { margin-top: 30px; border-radius: 4px; overflow: hidden; border: 1px solid rgba(255,255,255,0.1); }
        .contact-form { background: var(--card-bg); padding: 40px; border-radius: 4px; box-shadow: 0 15px 30px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.05); }
        .form-group { margin-bottom: 25px; } .form-group input, .form-group textarea { width: 100%; padding: 15px; background: var(--dark-navy); border: 1px solid rgba(255,255,255,0.1); color: var(--white); border-radius: 2px; font-family: 'Poppins', sans-serif; } .form-group input:focus, .form-group textarea:focus { border-color: var(--luxury-gold); outline: none; }
        #form-message { color: var(--luxury-gold); margin-top: 15px; font-family: 'Montserrat', sans-serif; font-size: 0.9rem; }
        footer { background: #030710; color: var(--white); padding: 60px 0 0; border-top: 1px solid rgba(212, 175, 55, 0.2); }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 2fr; gap: 40px; padding-bottom: 50px; }
        .footer-logo { height: 50px; margin-bottom: 15px; filter: brightness(0) invert(1); } .footer-col p { color: var(--text-dim); font-size: 0.9rem; }
        .footer-col h4 { color: var(--white); margin-bottom: 25px; font-family: 'Montserrat', sans-serif; text-transform: uppercase; font-size: 0.9rem; letter-spacing: 2px; }
        .footer-col ul li { margin-bottom: 12px; } .footer-col ul li a { color: var(--text-dim); transition: color 0.3s; font-size: 0.9rem; } .footer-col ul li a:hover { color: var(--luxury-gold); }
        #newsletterForm { display: flex; margin-bottom: 25px; } #newsletterForm input { flex: 1; padding: 12px; background: var(--dark-navy); border: 1px solid rgba(255,255,255,0.1); color: var(--white); border-radius: 0; } #newsletterForm button { background: var(--luxury-gold); color: var(--dark-navy); border: none; padding: 0 20px; cursor: pointer; }
        .footer-bottom { text-align: center; padding: 25px 0; border-top: 1px solid rgba(255,255,255,0.05); font-size: 0.85rem; color: var(--text-dim); }
        .lightbox { display: none; position: fixed; z-index: 2000; top: 0; left: 0; width: 100%; height: 100%; background: rgba(3, 7, 16, 0.95); justify-content: center; align-items: center; } .lightbox.active { display: flex; }
        .close-lightbox { position: absolute; top: 30px; right: 40px; color: var(--luxury-gold); font-size: 40px; cursor: pointer; } #lightbox-img { max-width: 80%; max-height: 80%; border: 2px solid var(--luxury-gold); }
        .floating-buttons { position: fixed; bottom: 30px; right: 30px; display: flex; flex-direction: column; gap: 15px; z-index: 999; }
        .float-btn { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--white); font-size: 24px; box-shadow: 0 5px 20px rgba(0,0,0,0.5); border: none; cursor: pointer; transition: transform 0.3s; } .float-btn:hover { transform: scale(1.1); }
        .float-btn.whatsapp { background: #25D366; animation: pulseGreen 2s infinite; } .float-btn.call { background: var(--ocean-blue); } .float-btn.scroll-top { background: var(--luxury-gold); color: var(--dark-navy); display: none; } .float-btn.scroll-top.active { display: flex; }
        @keyframes pulseGreen { 0% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7), 0 5px 20px rgba(0,0,0,0.5); } 70% { box-shadow: 0 0 0 15px rgba(37, 211, 102, 0), 0 5px 20px rgba(0,0,0,0.5); } 100% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0), 0 5px 20px rgba(0,0,0,0.5); } }
        .fade-up { opacity: 0; transform: translateY(40px); transition: opacity 1s ease, transform 1s ease; } .fade-up.visible { opacity: 1; transform: translateY(0); }
        @media (max-width: 992px) { .hamburger { display: block; } .nav-menu { position: fixed; top: 0; right: -100%; width: 250px; height: 100vh; background: var(--white); flex-direction: column; justify-content: center; align-items: center; gap: 25px; box-shadow: -2px 0 10px rgba(0,0,0,0.1); transition: right 0.4s ease; } .nav-menu.active { right: 0; } .nav-link { color: var(--dark-navy) !important; } .nav-cta .btn-primary { display: none; } }
        @media (max-width: 768px) { .hero-content h1 { font-size: 2.5rem; } .hero-content p { font-size: 1rem; } .hero-buttons { flex-direction: column; gap: 15px; } .about-content, .packages-grid, .contact-wrapper { grid-template-columns: 1fr; } .features-grid { grid-template-columns: repeat(2, 1fr); } .destinations-grid { grid-template-columns: repeat(2, 1fr); } .gallery-grid { grid-template-columns: repeat(2, 1fr); } .timeline::before { left: 20px; } .timeline-item, .timeline-item.right { width: 100%; left: 0; padding-left: 50px; padding-right: 20px; } .timeline-dot, .timeline-item.right .timeline-dot { left: 12px; right: auto; } .footer-grid { grid-template-columns: 1fr; gap: 30px; text-align: center; } .footer-col ul { list-style: none; padding: 0; } #newsletterForm { max-width: 300px; margin: 0 auto 20px; } }
        @media (max-width: 480px) { .section-padding { padding: 50px 0; } .section-header h2 { font-size: 2rem; } .features-grid, .destinations-grid, .gallery-grid { grid-template-columns: 1fr; } .package-pricing { flex-direction: column; gap: 15px; } .price-tier { text-align: left; } }
    </style>
<link rel="stylesheet" href="assets/enhancements.css"><meta name="csrf-token" content="<?= e(csrf()) ?>"></head>
<body>
    <header id="header">
        <nav class="navbar container">
            <a href="#home" class="logo" aria-label="Travenzo Travels home"><img class="site-logo" src="assets/travenzo-logo.jpeg" alt="Travenzo Travels Sri Lanka logo" width="76" height="76"></a>
            <ul class="nav-menu" id="nav-menu">
                <li><a href="#home" class="nav-link">Home</a></li><li><a href="#about" class="nav-link">About</a></li><li><a href="#packages" class="nav-link">Packages</a></li><li><a href="#tour-plan" class="nav-link">Tour Plan</a></li><li><a href="#destinations" class="nav-link">Destinations</a></li><li><a href="#gallery" class="nav-link">Gallery</a></li><li><a href="reviews.php" class="nav-link">Reviews</a></li><li><a href="#contact" class="nav-link">Contact</a></li>
            </ul>
            <div class="nav-cta">
                <a href="#contact" class="btn-primary">Book Now</a>
                <button type="button" class="hamburger" id="hamburger" aria-controls="nav-menu" aria-expanded="false" aria-label="Open navigation"><i class="fas fa-bars" aria-hidden="true"></i></button>
            </div>
        </nav>
    </header>

    <?php echo content('home'); ?>

    <?php echo content('about'); ?>

    <?php echo content('packages'); ?>

    <?php include __DIR__.'/tour-section.php'; ?>

    <?php echo content('destinations'); ?>

    <?php echo content('gallery'); ?>

    <?php include __DIR__.'/review-section.php'; ?>

    <?php echo content('contact'); ?>

    <footer>
        <div class="container footer-grid">
            <div class="footer-col">
                <img class="site-footer-logo" src="assets/travenzo-logo.jpeg" alt="Travenzo Travels Sri Lanka logo" width="190" height="190" loading="lazy">
                <p>Explore • Experience • Enjoy</p>
            </div>
            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul><li><a href="#home">Home</a></li><li><a href="#about">About Us</a></li><li><a href="#destinations">Destinations</a></li><li><a href="#gallery">Gallery</a></li></ul>
            </div>
            <div class="footer-col">
                <h4>Our Tours</h4>
                <ul><li><a href="#packages">Packages</a></li><li><a href="#tour-plan">Tour Plan</a></li><li><a href="#contact">Book Now</a></li></ul>
            </div>
            <div class="footer-col">
                <a href="admin.php">Admin login</a><h4>Newsletter</h4>
                <form id="newsletterForm">
                    <input type="email" placeholder="Enter your email" required>
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                </form>
                <div class="footer-socials">
                    <a href="<?= e(contact_link('facebook')) ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <a href="<?= e(contact_link('instagram')) ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="<?= e(contact_link('whatsapp')) ?>" target="_blank"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2026 Travenzo Travels Sri Lanka. Explore • Experience • Enjoy</p>
        </div>
    </footer>

    <div id="lightbox" class="lightbox">
        <span class="close-lightbox">&times;</span>
        <img src="" id="lightbox-img" alt="Lightbox Image">
    </div>

    <div class="floating-buttons">
        <a href="<?= e(contact_link('whatsapp')) ?>" target="_blank" class="float-btn whatsapp" title="Chat with us on WhatsApp"><i class="fab fa-whatsapp"></i></a>
        <a href="<?= e(contact_phone_link()) ?>" class="float-btn call" title="Call Us"><i class="fas fa-phone"></i></a>
        <button id="scroll-top" class="float-btn scroll-top" title="Scroll to Top"><i class="fas fa-arrow-up"></i></button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const header = document.getElementById('header');
            window.addEventListener('scroll', () => { if (window.scrollY > 50) { header.classList.add('scrolled'); } else { header.classList.remove('scrolled'); } });
            const hamburger = document.getElementById('hamburger'); const navMenu = document.getElementById('nav-menu');
            hamburger.addEventListener('click', () => { navMenu.classList.toggle('active'); const icon = hamburger.querySelector('i'); if(navMenu.classList.contains('active')) { icon.classList.remove('fa-bars'); icon.classList.add('fa-times'); } else { icon.classList.remove('fa-times'); icon.classList.add('fa-bars'); } });
            document.querySelectorAll('.nav-link').forEach(link => { link.addEventListener('click', () => { navMenu.classList.remove('active'); hamburger.querySelector('i').classList.remove('fa-times'); hamburger.querySelector('i').classList.add('fa-bars'); }); });
            const slides = document.querySelectorAll('.hero-slider'); let currentSlide = 0;
            function nextSlide() { slides[currentSlide].classList.remove('active'); currentSlide = (currentSlide + 1) % slides.length; slides[currentSlide].classList.add('active'); }
            if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) setInterval(nextSlide, 5000);

            const testimonials = document.querySelectorAll('.testimonial-card'); let currentTestimonial = 0;
            function showTestimonial(index) { testimonials.forEach(t => t.classList.remove('active')); testimonials[index].classList.add('active'); }
            if(testimonials.length > 1) setInterval(() => { currentTestimonial = (currentTestimonial + 1) % testimonials.length; showTestimonial(currentTestimonial); }, 4000);
            const galleryItems = document.querySelectorAll('.gallery-item'); const lightbox = document.getElementById('lightbox'); const lightboxImg = document.getElementById('lightbox-img'); const closeLightbox = document.querySelector('.close-lightbox');
            galleryItems.forEach(item => { item.addEventListener('click', () => { const imgSrc = item.querySelector('img').src; lightboxImg.src = imgSrc; lightbox.classList.add('active'); }); });
            closeLightbox.addEventListener('click', () => { lightbox.classList.remove('active'); });
            lightbox.addEventListener('click', (e) => { if (e.target === lightbox) { lightbox.classList.remove('active'); } });
            const bookingForm = document.getElementById('bookingForm'); const formMessage = document.getElementById('form-message');
            const scrollTopBtn = document.getElementById('scroll-top');
            window.addEventListener('scroll', () => { if (window.scrollY > 300) { scrollTopBtn.classList.add('active'); } else { scrollTopBtn.classList.remove('active'); } });
            scrollTopBtn.addEventListener('click', () => { window.scrollTo({ top: 0, behavior: 'smooth' }); });
        });
    </script>
<script src="assets/forms.js"></script><script src="assets/scroll-reveal.js"></script></body>
</html>


 
