<?php
function myshop_home_page() {
    // 1. Handle Homepage Cart Logic
    if (isset($_POST['add_to_cart_home'])) {
        $pid = intval($_POST['product_id']);
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        
        if (isset($_SESSION['cart'][$pid])) {
            $_SESSION['cart'][$pid]++;
        } else {
            $_SESSION['cart'][$pid] = 1;
        }
        echo '<div class="sfood-alert sfood-alert-success text-center">Added to cart!</div>';
    }

    // 2. Read Data
    $json_file = get_stylesheet_directory() . '/products.json';
    $products = [];
    if (file_exists($json_file)) {
        $json_content = file_get_contents($json_file);
        $products = json_decode($json_content, true);
    }

    // 3. Randomly select 3 items
    $featured = [];
    if (!empty($products) && is_array($products)) {
        shuffle($products);
        $featured = array_slice($products, 0, 3);
    }

    ob_start();
    ?>
    <div class="sfood-container">
        <div class="hero-section">
            <div class="hero-content">
                <h1>SFOOD Ethnic Cuisine</h1>
                <p>Authentic flavors, straight to your table. We carefully select every ingredient to restore the taste of memory.</p>
                </div>
        </div>

        <h2 class="text-center" style="margin-bottom:30px;">Today's Recommendations</h2>
        
        <?php if (!empty($featured)): ?>
            <div class="product-grid">
                <?php foreach ($featured as $p): ?>
                <div class="product-card">
                    <img src="<?php echo esc_url($p['image_path']); ?>" alt="<?php echo esc_attr($p['name']); ?>">
                    <h3><?php echo esc_html($p['name']); ?></h3>
                    <p><?php echo mb_strimwidth(esc_html($p['description']), 0, 40, '...'); ?></p>
                    <span class="price">$<?php echo number_format($p['price'], 2); ?></span>
                    <form method="post">
                        <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                        <button type="submit" name="add_to_cart_home" class="btn-add">Add to Cart</button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center">No recommended products at the moment, please check back later.</p>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('sfood_home', 'myshop_home_page');
?>