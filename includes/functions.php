
<?php

function my_contact_form() {

    
    return  '
    <form id="myForm">
        <input type="text" name="name" placeholder="Your Name" required><br><br>
        <input type="email" name="email" placeholder="Your Email" required><br><br>
        <input type="tel" style="width:100%; padding:8px; border: 1px solid #cec9c9;}" name="phone" placeholder="Your Phone Number" required><br><br>
        <textarea name="message" placeholder="Your Message" required></textarea><br><br>
        <button id="submitBtn" type="submit" style="background-color: #05599d; color: white; border-radius: 10px; padding: 14px 29px;  cursor: pointer;" name="submit_form">Submit</button>


        
           <div class="result" style="margin-top: 20px;"></div>
    </form>
    ';
}


add_shortcode('myform', 'my_contact_form');




function create_form_table() {

    global $wpdb;

    $table_name = $wpdb->prefix . 'my_form_data';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        name VARCHAR(100),
        email VARCHAR(100),
        phone VARCHAR(20),
        message TEXT,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}


// MAIN MENU
function my_plugn_menu() {
    add_menu_page(
        'Contact Form',
        'Contact Form',
        'manage_options',
        'contact-form-plugin',
        'my_plugn_page'
    );
}
add_action('admin_menu', 'my_plugn_menu');

// MAIN PAGE
function my_plugn_page() {
    echo "<h1>Contact Form Plugin Dashboard 🚀</h1>";
}


function my_form_entries_menu() {
    add_submenu_page(
        'contact-form-plugin', 
        'Form Entries',
        'Form Entries',
        'manage_options',
        'form-entries',
        'my_form_entries_page'
    );
}
add_action('admin_menu', 'my_form_entries_menu');




function my_form_entries_page() {

    global $wpdb;
    $table_name = $wpdb->prefix . 'my_form_data';

    // DELETE
    if (isset($_GET['delete_id'])) {
        $wpdb->delete($table_name, ['id' => intval($_GET['delete_id'])]);
    }

    $results = $wpdb->get_results("SELECT * FROM $table_name");

    echo "<div class='wrap'>";
    echo "<h1 style='text-align:start; font-size: 40px; font-weight: bold;'>Form Entries 📋</h1>";

    if (!$results) {
        echo "<p>No entries found</p>";
    } else {

        echo "<table border='1' cellpadding='10'>";
        echo "<tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Message</th>
                <th>Action</th>
              </tr>";

        foreach ($results as $row) {
            echo "<tr>
                    <td>{$row->id}</td>
                    <td>{$row->name}</td>
                    <td>{$row->email}</td>
                    <td>{$row->phone}</td>
                    <td>{$row->message}</td>
                    <td>
                        <a href='?page=form-entries&delete_id={$row->id}'>Delete</a>
                    </td>
                  </tr>";
        }

        echo "</table>";
    }

    echo "</div>";
}




function handle_ajax_form() {

    global $wpdb;

    $table_name = $wpdb->prefix . 'my_form_data';

    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $phone = sanitize_text_field($_POST['phone']);
    $message = sanitize_textarea_field($_POST['message']);

    // ❌ VALIDATION
    if (empty($name) || empty($email) || empty($phone) || empty($message)) {
        echo "All fields are required ❌";
        wp_die();
    }

    if (!is_email($email)) {
        echo "Invalid email ❌";
        wp_die();
    }
$user_id = get_current_user_id();
    // ✅ SAVE
    $wpdb->insert($table_name, array(
    'user_id' => $user_id, // 🔥 IMPORTANT
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'message' => $message
));

    // EMAIL
    wp_mail(
        get_option('admin_email'),
        'New Contact Form Message',
        "Name: $name\nEmail: $email\nPhone: $phone\nMessage: $message"
    );

    echo "Message sent successfully ✅";
    wp_die();
}

add_action('wp_ajax_save_form_data', 'handle_ajax_form');
add_action('wp_ajax_nopriv_save_form_data', 'handle_ajax_form');


function my_plugin_scripts() {

    wp_enqueue_script(
        'my-ajax-script',
        plugin_dir_url(__FILE__) . '../assets/script.js',
        array('jquery'),
        false,
        true
    );

    wp_localize_script('my-ajax-script', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'my_plugin_scripts');