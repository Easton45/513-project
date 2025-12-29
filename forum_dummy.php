<?php
/**
 * SFOOD Forum Dummy Data Generator
 * Used to generate forum test data (seeding)
 */

function sfood_generate_forum_data() {
    global $wpdb;
    
    // 1. Define table names
    $table_topics = $wpdb->prefix . 'sfood_forum_topics';
    $table_replies = $wpdb->prefix . 'sfood_forum_replies';

    // 2. Create tables (if not exists)
    $charset_collate = $wpdb->get_charset_collate();

    $sql_topics = "CREATE TABLE IF NOT EXISTS $table_topics (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        board_id varchar(50) NOT NULL,
        title text NOT NULL,
        content longtext NOT NULL,
        author_name varchar(100) NOT NULL,
        views int DEFAULT 0,
        created_at datetime DEFAULT '0000-00-00 00:00:00',
        PRIMARY KEY  (id)
    ) $charset_collate;";

    $sql_replies = "CREATE TABLE IF NOT EXISTS $table_replies (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        topic_id mediumint(9) NOT NULL,
        content longtext NOT NULL,
        author_name varchar(100) NOT NULL,
        created_at datetime DEFAULT '0000-00-00 00:00:00',
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_topics);
    dbDelta($sql_replies);

    // --- 修改重点 START: 先清空旧数据 (Reset Old Data) ---
    // 无论之前有没有数据，先清空，以确保显示的是最新的英文版
    $wpdb->query("TRUNCATE TABLE $table_topics");
    $wpdb->query("TRUNCATE TABLE $table_replies");
    // --- 修改重点 END ---

    // 4. Prepare "Seeding" Material (English)
    
    // Fake Users
    $users = ['SpicyJack', 'Foodie_Alice', 'OldManZhang', 'SFOOD_Fan', 'MidnightSnackKing', 'Student_Chen', 'CantoneseBoy', 'SichuanGirl007', 'NoodleHead', 'Admin_SFOOD'];

    // Data Source: Board specific topics
    $data_source = [
        'reviews' => [ // Delicious Reviews
            ['title' => 'This Peking Duck is amazing! Skin is so crispy', 'content' => 'I thought delivery duck would be average, but SFOOD\'s roast duck is stunning. Crispy skin, tender meat, and the pancakes are chewy. Highly recommend!'],
            ['title' => 'The Snail Noodles taste authentic', 'content' => 'As a Liuzhou local, I rate this 8/10. The bamboo shoot smell is strong enough, but the spice level is a bit mild for me. Will add extra spice next time.'],
            ['title' => 'Shrimp Dumplings are good, but need more vinegar', 'content' => 'The dumplings are crystal clear with a whole shrimp inside. But the red vinegar portion was a bit small, not enough for me, boss!'],
            ['title' => 'Roujiamo is absolutely delicious', 'content' => 'The meat burger is so fragrant, the juice soaked into the bun. One bite is pure satisfaction. A must for breakfast.'],
            ['title' => 'Big Plate Chicken portion is huge', 'content' => 'Two of us ordered one Big Plate Chicken and couldn\'t finish it. Potatoes are soft, noodles are chewy. Best value for money.']
        ],
        'nostalgia' => [ // Nostalgia Canteen
            ['title' => 'Boss, when will you have Hot Dry Noodles?', 'content' => 'I miss Wuhan Hot Dry Noodles so much. It\'s hard to find authentic sesame paste here. Can you develop this new dish?'],
            ['title' => 'Homesick, came for the Cross-Bridge Rice Noodles', 'content' => 'Every rainy day I crave hot soup. Today\'s chicken broth was rich, felt like I was back home in Yunnan.'],
            ['title' => 'Sharing a secret recipe using the Hotpot Base', 'content' => 'Bought their Sichuan spicy base, not just for hotpot, but it\'s amazing for stir-frying dry pot shrimp! You guys should try it.'],
            ['title' => 'Will there be Mooncake gift boxes for Mid-Autumn Festival?', 'content' => 'Festival is coming, want to send some Suzhou Mooncakes to classmates abroad. Does the shop support shipping?']
        ],
        'service' => [ // Service & Delivery
            ['title' => 'Delivery guy had a great attitude', 'content' => 'Heavy rain today, the rider was a few minutes late but kept apologizing. Packaging was well protected. Thumbs up.'],
            ['title' => 'Does delivery cover the New Territories?', 'content' => 'I live quite far, wondering if you deliver there? Really want to try the Beef Noodles.'],
            ['title' => 'Question about refund process', 'content' => 'Accidentally ordered an extra portion, contacted support and they handled it quickly. Money refunded, good efficiency.']
        ],
        'news' => [ // SFOOD News
            ['title' => '【Hiring】Part-time Delivery Drivers Wanted', 'content' => 'SFOOD is expanding and needs drivers familiar with local routes. Requirement: Own vehicle, responsible. Apply on the recruitment page.'],
            ['title' => '【Notice】12% OFF Storewide this Weekend!', 'content' => 'To reward our customers, all noodle products are 12% off this weekend. Includes Rice Noodles, Beef Noodles, Snail Noodles.'],
            ['title' => '【New Arrival】Xinjiang Big Plate Chicken is Live', 'content' => 'After a month of R&D by our chef team, authentic Xinjiang Big Plate Chicken is finally here! Welcome to taste.'],
            ['title' => 'SFOOD is looking for Senior Chinese Chefs', 'content' => 'If you specialize in Sichuan or Cantonese cuisine, join our family. Click "Join Us" in the navigation to send your resume.']
        ]
    ];

    // Generic Reply Pool
    $replies_pool = [
        'Looks great, I will try it next time.',
        'Totally agree! I love this too.',
        'Thanks for sharing!',
        'The boss really puts heart into making this.',
        'Haha, I also think it could be spicier.',
        'Ordered already, waiting for delivery.',
        'Good suggestion, hope the owner sees this.',
        'Upvoted!'
    ];

    // 5. Loop to insert data
    $topics_count = 0;
    
    foreach ($data_source as $board => $items) {
        foreach ($items as $item) {
            // Random user
            $user = $users[array_rand($users)];
            // Random time (within last 30 days)
            $time = date('Y-m-d H:i:s', strtotime('-' . rand(1, 720) . ' hours'));
            
            // Insert Topic
            $wpdb->insert($table_topics, [
                'board_id' => $board,
                'title' => $item['title'],
                'content' => $item['content'],
                'author_name' => $user,
                'views' => rand(10, 500),
                'created_at' => $time
            ]);
            
            $topic_id = $wpdb->insert_id;
            $topics_count++;

            // Insert 0-3 replies per topic
            $reply_count = rand(0, 3);
            for ($i = 0; $i < $reply_count; $i++) {
                $wpdb->insert($table_replies, [
                    'topic_id' => $topic_id,
                    'content' => $replies_pool[array_rand($replies_pool)],
                    'author_name' => $users[array_rand($users)],
                    'created_at' => date('Y-m-d H:i:s', strtotime($time . ' +'.rand(1,5).' hours'))
                ]);
            }
        }
    }

    return "<div class='sfood-alert sfood-alert-success'>Success! Old data cleared. Generated {$topics_count} new English topics.</div>";
}

// Shortcode to display forum interface
function myshop_forum_display() {
    global $wpdb;
    
    // If generate data button clicked
    if (isset($_POST['generate_data']) && current_user_can('administrator')) {
        echo sfood_generate_forum_data();
    }

    $boards = [
        'reviews' => ['icon' => 'fa-utensils', 'name' => 'Delicious Reviews', 'desc' => 'Show your food photos, real taste reviews'],
        'nostalgia' => ['icon' => 'fa-home', 'name' => 'Nostalgia Canteen', 'desc' => 'Talk about hometown flavors, wish for new dishes'],
        'service' => ['icon' => 'fa-headset', 'name' => 'Diners Help', 'desc' => 'Delivery inquiries, order questions'],
        'news' => ['icon' => 'fa-bullhorn', 'name' => 'SFOOD News', 'desc' => 'Official announcements, discounts & hiring']
    ];

    $output = '<div class="sfood-container">';
    
    // Button visible only to admins
    if (current_user_can('administrator')) {
        $output .= '
        <form method="post" style="margin-bottom:20px; text-align:right;">
            <button type="submit" name="generate_data" class="btn-add" style="background:#dc3545; width:auto;">
                <i class="fas fa-sync-alt"></i> Admin: Reset & Regenerate English Data
            </button>
        </form>';
    }

    // Board Display
    $output .= '<div class="forum-boards-grid" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:20px; margin-bottom:40px;">';
    foreach ($boards as $key => $board) {
        $output .= '
        <div class="sfood-card text-center" style="cursor:pointer;" onclick="location.href=\'?board='.$key.'\'">
            <div style="font-size:2.5em; color:#e44d26; margin-bottom:10px;"><i class="fas '.$board['icon'].'"></i></div>
            <h3 style="margin:10px 0;">'.$board['name'].'</h3>
            <p style="color:#666; font-size:0.9em;">'.$board['desc'].'</p>
        </div>';
    }
    $output .= '</div>';

    // Topic List Display
    $current_board = isset($_GET['board']) ? $_GET['board'] : 'all';
    $table_topics = $wpdb->prefix . 'sfood_forum_topics';
    $table_replies = $wpdb->prefix . 'sfood_forum_replies';

    // Check if table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_topics'") != $table_topics) {
        return $output . "<div class='text-center'>Forum tables not initialized. Admin please click the button above to generate data.</div></div>";
    }

    $where = $current_board === 'all' ? '' : $wpdb->prepare("WHERE board_id = %s", $current_board);
    $topics = $wpdb->get_results("SELECT * FROM $table_topics $where ORDER BY created_at DESC LIMIT 20");

    $output .= '<h3 style="border-bottom:2px solid #e44d26; padding-bottom:10px; margin-bottom:20px;">
        <i class="fas fa-comments"></i> Latest Discussions
        '.($current_board !== 'all' ? ' - ' . $boards[$current_board]['name'] : '').'
    </h3>';

    if ($topics) {
        $output .= '<div class="topic-list">';
        foreach ($topics as $t) {
            $reply_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_replies WHERE topic_id = %d", $t->id));
            $output .= '
            <div style="display:flex; justify-content:space-between; align-items:center; padding:15px; border-bottom:1px solid #eee; background:#fff;">
                <div>
                    <span style="background:#eee; padding:2px 8px; border-radius:4px; font-size:0.8em; margin-right:10px;">
                        '.$boards[$t->board_id]['name'].'
                    </span>
                    <strong style="font-size:1.1em; color:#333;">'.esc_html($t->title).'</strong>
                    <div style="font-size:0.85em; color:#888; margin-top:5px;">
                        <i class="fas fa-user"></i> '.esc_html($t->author_name).' &nbsp;&nbsp; 
                        <i class="fas fa-clock"></i> '.date('m-d H:i', strtotime($t->created_at)).'
                    </div>
                </div>
                <div class="text-center" style="min-width:60px;">
                    <div style="font-size:1.2em; color:#e44d26;">'.$reply_count.'</div>
                    <div style="font-size:0.8em; color:#999;">Replies</div>
                </div>
            </div>';
        }
        $output .= '</div>';
    } else {
        $output .= '<p class="text-center">No topics yet.</p>';
    }

    $output .= '</div>';
    return $output;
}
add_shortcode('sfood_forum', 'myshop_forum_display');
?>