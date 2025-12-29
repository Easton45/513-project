<?php
function myshop_about_page() {
    // Replace with your real image URL
    $img_url = 'http://easton55.infinityfreeapp.com/wp-content/uploads/2025/12/map-300x291.jpg';

    return '
    <div class="sfood-container">
        <div class="about-wrapper">
            <div class="about-text">
                <h2 style="color:#e44d26;">About SFOOD</h2>
                <p style="font-size:1.1em; color:#555; font-style:italic;">"The bridge between taste buds and nostalgia"</p>
                <br>
                <p>Founded in 2023, SFOOD is an e-commerce platform dedicated to traditional ethnic Chinese cuisine. From the rugged noodles of the Northwest to the exquisite dim sum of the Jiangnan region, we are committed to unearthing authentic flavors hidden in the alleyways.</p>
                <p>We are not just selling food, but transmitting a culture and a dedication to the taste of home.</p>
                
                <div class="info-box">
                    <p><i class="fas fa-building"></i> <strong>Company Name:</strong> SFOOD Catering Co., Ltd.</p>
                    <p><i class="fas fa-map-marker-alt"></i> <strong>Address:</strong>3 Bridge Ln, Sydney NSW 2000, Australia</p>
                    <p><i class="fas fa-phone"></i> <strong>Service Hotline:</strong> +852 1234 5678</p>
                    <p><i class="fas fa-clock"></i> <strong>Working Hours:</strong> 10:00 - 22:00 (Daily)</p>
                </div>
            </div>
            <div class="about-img">
                <img src="' . $img_url . '" alt="SFOOD Environment">
            </div>
        </div>
    </div>';
}
add_shortcode('sfood_about', 'myshop_about_page');
?>