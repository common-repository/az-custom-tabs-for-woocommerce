<?php
/*
Plugin Name: AZ Custom Tabs for WooCommerce
Description: Add global and product-specific custom tabs in WooCommerce.
Version: 1.0
Author: Abbas Z. Dhebar
*/

defined('ABSPATH') || exit;


// Add the admin menu
add_action('admin_menu', 'azwctabs_global_tabs_menu');

function azwctabs_global_tabs_menu() {
    add_menu_page(
        __('AZ WC Tabs', 'az-custom-tabs-for-woocommerce'),
        __('AZ WC Tabs', 'az-custom-tabs-for-woocommerce'),
        'manage_options',
        'az-global-tabs',
        'azwctabs_global_tabs_page'
    );
}

function azwctabs_global_tabs_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Global Custom Tabs', 'az-custom-tabs-for-woocommerce'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('azwctabs_global_tabs_group');
            do_settings_sections('azwctabs_global_tabs_group');
            wp_nonce_field('azwctabs_global_tabs_nonce', 'azwctabs_global_tabs_nonce_field'); // Nonce for security
            ?>
            <div id="global-tabs-container">
                <?php
                $global_tabs = get_option('azwctabs_global_tabs', []);
                if (!empty($global_tabs)) {
                    foreach ($global_tabs as $index => $tab) {
                        azwctabs_render_tab($index, $tab);
                    }
                }else{
                ?>

                <div class="global-tab">
                    <input type="hidden" name="azwctabs_tab_index[]" value="0"/>
                    <input type="text" name="azwctabs_global_tabs[0][title]" placeholder="<?php esc_attr_e('Tab Title', 'az-custom-tabs-for-woocommerce'); ?>"  style="width: 30%;">
                    <textarea name="azwctabs_global_tabs[0][content]" rows="4" style="width: 65%;"></textarea>

                </div>

                <?php } ?>
            </div>
            <button type="button" id="add-tab" class="button"><?php esc_html_e('Add Tab', 'az-custom-tabs-for-woocommerce'); ?></button>
            <br><br>
            <input type="submit" value="<?php esc_attr_e('Save Tabs', 'az-custom-tabs-for-woocommerce'); ?>" class="button button-primary">
        </form>
    </div>
 
    <?php
}

function azwctabs_render_tab($index, $tab) {
    ?>
    <div class="global-tab">
        <input type="hidden" name="azwctabs_tab_index[]" value="<?php echo esc_attr($index); ?>"/>
        <input type="text" name="azwctabs_global_tabs[<?php echo esc_attr($index); ?>][title]" placeholder="<?php esc_attr_e('Tab Title', 'az-custom-tabs-for-woocommerce'); ?>" value="<?php echo esc_attr($tab['title']); ?>" style="width: 30%;">
        <textarea name="azwctabs_global_tabs[<?php echo esc_attr($index); ?>][content]" rows="4" style="width: 65%;"><?php echo esc_textarea($tab['content']); ?></textarea>
        <?php 
        if($index !=0){
        ?>
        <button type="button" class="remove-tab button"><?php esc_html_e('Remove', 'az-custom-tabs-for-woocommerce'); ?></button>
        <?php } ?>
    </div>
    <?php
}

add_action('admin_init', 'azwctabs_register_settings');

function azwctabs_register_settings() {
    register_setting('azwctabs_global_tabs_group', 'azwctabs_global_tabs', 'azwctabs_sanitize_global_tabs');
}

function azwctabs_sanitize_global_tabs($input) {
    foreach ($input as &$tab) {
        $tab['title'] = sanitize_text_field($tab['title']);
        $tab['content'] = sanitize_textarea_field($tab['content']);
    }
    return $input;
}

add_filter('woocommerce_product_tabs', 'azwctabs_add_global_tabs');

function azwctabs_add_global_tabs($tabs) {
    $global_tabs = get_option('azwctabs_global_tabs', []);
    foreach ($global_tabs as $tab) {
        if (!empty($tab['title']) && !empty($tab['content'])) {
            $tabs[sanitize_title($tab['title'])] = [
                'title'    => esc_html($tab['title']),
                'priority' => 90,
                'callback' => function() use ($tab) {
                    echo esc_html(wp_strip_all_tags(wpautop($tab['content'])));
                },
            ];
        }
    }
    return $tabs;
}

add_action('admin_post_save_tabs', 'azwctabs_handle_save_tabs');

function azwctabs_handle_save_tabs() {
    if (!current_user_can('manage_options')) {
        wp_die(esc_html('You do not have sufficient permissions to access this page.'));
    }

    if (!isset($_POST['azwctabs_global_tabs_nonce_field'])) {
        return;
    }

    $nonce = sanitize_text_field(wp_unslash($_POST['azwctabs_global_tabs_nonce_field']));

    if(!(wp_verify_nonce($nonce, 'azwctabs_global_tabs_nonce'))){
        return;
    }
    

    if (!isset($_POST['az-custom-tabs-for-woocommerce'])) {
        return;
    }

    $data = sanitize_text_field(wp_unslash($_POST['az-custom-tabs-for-woocommerce']));


        $tabs = array_map(function($tab) {
            return [
                'title' => sanitize_text_field($tab['title']),
                'content' => sanitize_textarea_field($tab['content']),
            ];
        }, $data);
        var_dump($tabs);
        exit();

        update_option('az-custom-tabs-for-woocommerce', $tabs);
        wp_redirect(admin_url('admin.php?page=az-global-tabs&success=1'));
        exit;
    
}

add_action('admin_notices', 'azwctabs_global_tabs_admin_notice');

function azwctabs_global_tabs_admin_notice() {

    if (!isset($_POST['azwctabs_global_tabs_nonce_field'])) {
        return;
    }

    $nonce = sanitize_text_field(wp_unslash($_POST['azwctabs_global_tabs_nonce_field']));

    if(!(wp_verify_nonce($nonce, 'azwctabs_global_tabs_nonce'))){
        return;
    }
    
    if (isset($_GET['success'])) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html(__('Tabs saved successfully!', 'az-custom-tabs-for-woocommerce')) . '</p></div>';
    }
}


