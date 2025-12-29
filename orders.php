<?php
/**
 * Orders Module
 * Shortcode: [sfood_orders]
 */

// Orders shortcode
add_shortcode('sfood_orders', 'sfood_orders_page');
function sfood_orders_page() {
    // Check if user is logged in
    if (!is_user_logged_in()) {
        return '
        <div class="sfood-alert alert-warning">
            <p>Please <a href="?page_id=' . get_page_by_path('login')->ID . '">login</a> to view your orders.</p>
        </div>';
    }
    
    ob_start();
    
    global $wpdb;
    $user_id = get_current_user_id();
    
    $orders_table = $wpdb->prefix . 'sfood_orders';
    $order_items_table = $wpdb->prefix . 'sfood_order_items';
    
    // Get all orders for current user
    $orders = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $orders_table WHERE user_id = %d ORDER BY order_date DESC",
        $user_id
    ), ARRAY_A);
    
    // Get specific order details if requested
    $order_details = null;
    if (isset($_GET['order_id'])) {
        $order_id = intval($_GET['order_id']);
        $order_details = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $orders_table WHERE id = %d AND user_id = %d",
            $order_id, $user_id
        ), ARRAY_A);
        
        if ($order_details) {
            $order_details['items'] = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $order_items_table WHERE order_id = %d",
                $order_id
            ), ARRAY_A);
        }
    }
    
    ?>
    
    <div class="sfood-container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-danger"><i class="fas fa-history me-2"></i>Order History</h2>
            <a href="<?php echo home_url(); ?>" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i>Back to Home
            </a>
        </div>
        
        <?php if (empty($orders)): ?>
            <div class="sfood-alert alert-info text-center py-5">
                <i class="fas fa-shopping-bag fa-3x mb-3 text-muted"></i>
                <h4>No Orders Yet</h4>
                <p class="mb-3">You haven't placed any orders yet. Start exploring our delicious menu!</p>
                <a href="?page_id=<?php echo get_page_by_path('products')->ID; ?>" class="btn btn-danger">
                    Explore Our Menu
                </a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-8">
                    <div class="sfood-card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Your Orders</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table sfood-table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Date</th>
                                            <th>Items</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): 
                                            $item_count = $wpdb->get_var($wpdb->prepare(
                                                "SELECT COUNT(*) FROM $order_items_table WHERE order_id = %d",
                                                $order['id']
                                            ));
                                        ?>
                                            <tr>
                                                <td>
                                                    <strong>#<?php echo $order['id']; ?></strong>
                                                </td>
                                                <td>
                                                    <?php echo date_i18n('M j, Y', strtotime($order['order_date'])); ?>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?php echo date_i18n('g:i A', strtotime($order['order_date'])); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <?php echo $item_count; ?> item(s)
                                                </td>
                                                <td>
                                                    <strong class="text-success">
                                                        $<?php echo number_format($order['total_amount'], 2); ?>
                                                    </strong>
                                                </td>
                                                <td>
                                                    <?php 
                                                    $status_class = 'secondary';
                                                    switch($order['status']) {
                                                        case 'completed': $status_class = 'success'; break;
                                                        case 'pending': $status_class = 'warning'; break;
                                                        case 'cancelled': $status_class = 'danger'; break;
                                                    }
                                                    ?>
                                                    <span class="badge bg-<?php echo $status_class; ?>">
                                                        <?php echo ucfirst($order['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="?order_id=<?php echo $order['id']; ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye me-1"></i>View
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <?php if ($order_details): ?>
                        <div class="sfood-card">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">Order Details #<?php echo $order_details['id']; ?></h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <h6>Order Information</h6>
                                    <p class="mb-1">
                                        <strong>Date:</strong> 
                                        <?php echo date_i18n('M j, Y g:i A', strtotime($order_details['order_date'])); ?>
                                    </p>
                                    <p class="mb-1">
                                        <strong>Status:</strong> 
                                        <?php 
                                        $status_class = 'secondary';
                                        switch($order_details['status']) {
                                            case 'completed': $status_class = 'success'; break;
                                            case 'pending': $status_class = 'warning'; break;
                                            case 'cancelled': $status_class = 'danger'; break;
                                        }
                                        ?>
                                        <span class="badge bg-<?php echo $status_class; ?>">
                                            <?php echo ucfirst($order_details['status']); ?>
                                        </span>
                                    </p>
                                    <p class="mb-1">
                                        <strong>Total:</strong> 
                                        $<?php echo number_format($order_details['total_amount'], 2); ?>
                                    </p>
                                </div>
                                
                                <div class="mb-3">
                                    <h6>Shipping Address</h6>
                                    <p class="mb-0">
                                        <?php echo esc_html($order_details['customer_name']); ?><br>
                                        <?php echo esc_html($order_details['customer_email']); ?><br>
                                        <?php echo nl2br(esc_html($order_details['customer_address'])); ?><br>
                                        <?php echo esc_html($order_details['customer_city']); ?>, 
                                        <?php echo esc_html($order_details['customer_zip']); ?>
                                    </p>
                                </div>
                                
                                <?php if (!empty($order_details['items'])): ?>
                                    <div class="mb-3">
                                        <h6>Order Items</h6>
                                        <?php foreach ($order_details['items'] as $item): ?>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div>
                                                    <strong><?php echo esc_html($item['product_name']); ?></strong>
                                                    <br>
                                                    <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                                                </div>
                                                <div class="text-end">
                                                    $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <a href="?" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left me-1"></i>Back to Orders
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="sfood-card">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">Order Statistics</h5>
                            </div>
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <h3 class="text-primary"><?php echo count($orders); ?></h3>
                                    <p class="mb-0">Total Orders</p>
                                </div>
                                <?php 
                                $total_spent = array_sum(array_column($orders, 'total_amount'));
                                $average_order = count($orders) > 0 ? $total_spent / count($orders) : 0;
                                ?>
                                <div class="mb-3">
                                    <h3 class="text-success">
                                        $<?php echo number_format($total_spent, 2); ?>
                                    </h3>
                                    <p class="mb-0">Total Spent</p>
                                </div>
                                <div class="mb-0">
                                    <h3 class="text-warning">
                                        $<?php echo number_format($average_order, 2); ?>
                                    </h3>
                                    <p class="mb-0">Average Order</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <?php
    return ob_get_clean();
}