<?php
function myshop_display_products() {
    // Handle add to cart logic
    if (isset($_POST['add_to_cart'])) {
        $product_id = intval($_POST['product_id']);
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        
        // Increment if exists, otherwise set to 1
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]++;
        } else {
            $_SESSION['cart'][$product_id] = 1;
        }
        echo '<div style="color:green; padding:10px;">Added to cart!</div>';
    }

    // Read JSON
    $json_file = get_stylesheet_directory() . '/products.json';
    if (!file_exists($json_file)) return "Product data not found.";
    
    $products = json_decode(file_get_contents($json_file), true);
    if (!$products) return "Product data format error.";

    // Output HTML
    $output = '<div class="product-grid">';
    foreach ($products as $p) {
        $output .= '
        <div class="product-card">
            <img src="' . esc_url($p['image_path']) . '" alt="' . esc_attr($p['name']) . '">
            <h3>' . esc_html($p['name']) . '</h3>
            <p>' . esc_html($p['description']) . '</p>
            <span class="price">$' . number_format($p['price'], 2) . '</span>
            <form method="post">
                <input type="hidden" name="product_id" value="' . $p['id'] . '">
                <button type="submit" name="add_to_cart" class="btn-add">Add to Cart</button>
            </form>
        </div>';
    }
    $output .= '</div>';
    return $output;
}
add_shortcode('my_product_list', 'myshop_display_products');
?>