<?php
/**
 * Sync phone number from User Meta to FluentCRM when a user registers.
 * Applicable scenarios: WooCommerce registration, plugin-generated registration forms.
 */
add_action('user_register', function($user_id) {
    if (!function_exists('FluentCrmApi')) {
        return;
    }

    $user_info = get_userdata($user_id);
    
    // Logic to get phone number
    // 1. Try to get WooCommerce billing phone
    $phone = get_user_meta($user_id, 'billing_phone', true);
    
    // 2. If not WooCommerce, try common phone meta keys (adjust based on your registration plugin)
    if (empty($phone)) {
        $phone = get_user_meta($user_id, 'phone_number', true); 
    }
    if (empty($phone)) {
        $phone = get_user_meta($user_id, 'mobile', true);
    }

    // If phone number is found, sync to FluentCRM
    if (!empty($phone)) {
        $contact_api = FluentCrmApi('contacts');
        $contact_api->createOrUpdate([
            'email' => $user_info->user_email,
            'phone' => $phone, // Sync phone
            'first_name' => $user_info->first_name,
            'last_name' => $user_info->last_name,
            'status' => 'subscribed'
        ]);
    }
}, 20, 1);

// 1. Start Session (Required for cart functionality, must be the first line)
add_action('init', function() {
    if (!session_id()) {
        session_start();
    }
});

// 2. Load Styles
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style') );
    wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' );
} );

// 3. Smart Module Loading
// Automatically detects if files are in root or 'inc' folder
$files_to_load = [
    'products.php',
    'cart.php',
    'admin.php',
    'home.php',
    'about.php',
    'orders.php',
    'forum_dummy.php',
    'recruit.php' 
];

foreach ($files_to_load as $file) {
    // Check 'inc' folder first
    $path_in_inc = get_stylesheet_directory() . '/inc/' . $file;
    // Check root directory next
    $path_in_root = get_stylesheet_directory() . '/' . $file;

    if (file_exists($path_in_inc)) {
        require_once $path_in_inc;
    } elseif (file_exists($path_in_root)) {
        require_once $path_in_root;
    } else {
        // Do not throw error if file doesn't exist, just log it to prevent white screen
        error_log("SFOOD Info: File $file not found, skipped loading.");
    }
}
?>