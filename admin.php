<?php
function myshop_admin_manager() {
    // 1. Security Check
//    if (!current_user_can('administrator')) return '<div class="sfood-alert sfood-alert-danger">Access Denied.</div>';

    $json_file = get_stylesheet_directory() . '/products.json';
    $message = '';

    // Load Data Helper Function
    $get_data = function() use ($json_file) {
        if (file_exists($json_file)) {
            $content = file_get_contents($json_file);
            $data = json_decode($content, true);
            return $data ? $data : [];
        }
        return [];
    };

    // --- Logic A: Handle "Add New Product" ---
    if (isset($_POST['add_new_product'])) {
        $current_data = $get_data();

        // Auto-generate new ID
        $new_id = 1;
        if (!empty($current_data)) {
            $ids = array_column($current_data, 'id');
            $new_id = max($ids) + 1;
        }

        $new_item = [
            'id'          => $new_id,
            'name'        => sanitize_text_field($_POST['p_name']),
            'description' => sanitize_textarea_field($_POST['p_desc']),
            'price'       => floatval($_POST['p_price']),
            'image_path'  => esc_url_raw($_POST['p_image'])
        ];

        $current_data[] = $new_item;
        
        if (file_put_contents($json_file, json_encode($current_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
            $message = '<div class="sfood-alert sfood-alert-success"><i class="fas fa-check-circle"></i> Product added successfully! (ID: '.$new_id.')</div>';
        } else {
            $message = '<div class="sfood-alert sfood-alert-danger">Failed to save. Check file permissions.</div>';
        }
    }

    // --- Logic B: Handle "Delete Product" (New!) ---
    if (isset($_POST['delete_product'])) {
        $delete_id = intval($_POST['delete_id']);
        $current_data = $get_data();
        $new_data = [];
        $found = false;

        foreach ($current_data as $item) {
            if ($item['id'] == $delete_id) {
                $found = true; // Skip this item (effectively deleting it)
            } else {
                $new_data[] = $item;
            }
        }

        if ($found) {
            // Reset keys to keep JSON clean (optional but recommended)
            $new_data = array_values($new_data);
            file_put_contents($json_file, json_encode($new_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $message = '<div class="sfood-alert sfood-alert-success"><i class="fas fa-trash-alt"></i> Product ID '.$delete_id.' deleted successfully.</div>';
        } else {
            $message = '<div class="sfood-alert sfood-alert-warning">Product ID not found.</div>';
        }
    }

    // --- Logic C: Handle "Raw JSON" Save ---
    if (isset($_POST['save_json'])) {
        $new_json = stripslashes($_POST['json_content']);
        if (json_decode($new_json)) {
            file_put_contents($json_file, $new_json);
            $message = '<div class="sfood-alert sfood-alert-success">JSON Data updated manually!</div>';
        } else {
            $message = '<div class="sfood-alert sfood-alert-danger">Invalid JSON format.</div>';
        }
    }

    // Refresh Data for Display
    $products = $get_data();
    $json_content_display = json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    ob_start();
    ?>
    <div class="sfood-container">
        <h2 class="text-center" style="margin-bottom:30px;">Admin Product Manager</h2>
        
        <?php echo $message; ?>

        <div class="recruit-form-wrapper" style="margin-bottom: 40px;">
            <div class="form-header">
                <h3><i class="fas fa-plus-circle"></i> Add New Product</h3>
            </div>
            <form method="post" class="sfood-recruit-form">
                <div class="form-group">
                    <label>Product Name</label>
                    <div class="input-with-icon"><i class="fas fa-tag"></i><input type="text" name="p_name" required></div>
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
                    <div class="form-group">
                        <label>Price ($)</label>
                        <div class="input-with-icon"><i class="fas fa-dollar-sign"></i><input type="text" name="p_price" required></div>
                    </div>
                    <div class="form-group">
                        <label>Image URL</label>
                        <div class="input-with-icon"><i class="fas fa-image"></i><input type="text" name="p_image" required></div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <div class="input-with-icon"><i class="fas fa-align-left"></i><input type="text" name="p_desc" required></div>
                </div>
                <button type="submit" name="add_new_product" class="btn-submit-recruit" style="margin-top:10px;">Add Product</button>
            </form>
        </div>

        <div style="background:#fff; padding:30px; border-radius:12px; border:1px solid #eee; margin-bottom:40px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
            <h3><i class="fas fa-list-ul"></i> Existing Products</h3>
            <p style="color:#666; margin-bottom:20px;">View current menu items and delete them quickly.</p>
            
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8f9fa; text-align:left; border-bottom:2px solid #e2e8f0;">
                            <th style="padding:15px;">ID</th>
                            <th style="padding:15px;">Image</th>
                            <th style="padding:15px;">Name</th>
                            <th style="padding:15px;">Price</th>
                            <th style="padding:15px; text-align:right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr><td colspan="5" class="text-center" style="padding:20px;">No products found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($products as $p): ?>
                                <tr style="border-bottom:1px solid #eee;">
                                    <td style="padding:15px;"><strong>#<?php echo $p['id']; ?></strong></td>
                                    <td style="padding:15px;">
                                        <img src="<?php echo esc_url($p['image_path']); ?>" style="width:50px; height:50px; object-fit:cover; border-radius:4px;">
                                    </td>
                                    <td style="padding:15px; font-weight:600; color:#333;"><?php echo esc_html($p['name']); ?></td>
                                    <td style="padding:15px; color:#e44d26;">$<?php echo number_format($p['price'], 2); ?></td>
                                    <td style="padding:15px; text-align:right;">
                                        <form method="post" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                            <input type="hidden" name="delete_id" value="<?php echo $p['id']; ?>">
                                            <button type="submit" name="delete_product" style="background:#dc3545; color:white; border:none; padding:8px 15px; border-radius:4px; cursor:pointer; font-size:14px;">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <details>
            <summary style="cursor:pointer; color:#666; margin-bottom:10px;">Advanced: Show Raw JSON Editor</summary>
            <div style="background:#f1f1f1; padding:20px; border-radius:8px;">
                <form method="post">
                    <textarea name="json_content" style="width:100%; height:300px; font-family:monospace; border:1px solid #ccc; padding:10px;"><?php echo esc_textarea($json_content_display); ?></textarea>
                    <button type="submit" name="save_json" class="btn-add" style="margin-top:10px; background:#666; width:auto;">Save JSON</button>
                </form>
            </div>
        </details>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('admin_product_manager', 'myshop_admin_manager');
?>