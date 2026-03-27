<?php

/*
Plugin Name: Contact Form Plugin
Description: My custom plugin learning
Version: 1.2
Author: Sayyed Ali
*/

// Security
if (!defined('ABSPATH')) {
    exit;
}


// Include files
require_once plugin_dir_path(__FILE__) . 'includes/functions.php';

register_activation_hook(__FILE__, 'create_form_table') ;
