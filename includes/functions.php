
<?php

function my_contact_form() {

    $message = '';

    if (isset($_GET['success'])) {
    $message = "<p style='color:green;'>Message sent successfully ✅</p>";
}

    return $message . '
    <form method="post">
        <input type="text" name="name" placeholder="Your Name" required><br><br>
        <input type="email" name="email" placeholder="Your Email" required><br><br>
        <textarea name="message" placeholder="Your Message" required></textarea><br><br>
        <button type="submit" name="submit_form">Send</button>
    </form>
    ';
}

add_shortcode('myform', 'my_contact_form');

function handle_form_submission() {

    if (isset($_POST['submit_form'])) {

        global $wpdb;

        $table_name = $wpdb->prefix . 'my_form_data';

        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $message = sanitize_textarea_field($_POST['message']);

        $wpdb->insert($table_name, array(
            'name' => $name,
            'email' => $email,
            'message' => $message
        ));

        wp_mail(
            get_option('admin_email'),
            'New Contact Form Message',
            "Name: $name\nEmail: $email\nMessage: $message"
        );

        // ✅ REDIRECT
        wp_redirect(add_query_arg('success', '1', get_permalink()));
        exit;
    }
}

add_action('init', 'handle_form_submission');

function create_form_table() {

    global $wpdb;

    $table_name = $wpdb->prefix . 'my_form_data';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        name VARCHAR(100),
        email VARCHAR(100),
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
        'contact-form-plugin', // 👈 SAME slug
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
    echo "<h1 class='fs-1'>Form Entries 📋</h1>";

    if (!$results) {
        echo "<p>No entries found</p>";
    } else {

        echo "<table border='1' cellpadding='10'>";
        echo "<tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Message</th>
                <th>Action</th>
              </tr>";

        foreach ($results as $row) {
            echo "<tr>
                    <td>{$row->id}</td>
                    <td>{$row->name}</td>
                    <td>{$row->email}</td>
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