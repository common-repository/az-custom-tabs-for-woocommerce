<?php
/*
Plugin Name: AZ Custom Tabs for WooCommerce
Description: Add global and product-specific custom tabs in WooCommerce.
Version: 1.0
Author: Abbas Z. Dhebar
*/

defined('ABSPATH') || exit;


// Hook to add meta boxes
add_action('add_meta_boxes', 'azwctabs_product_tabs_meta_box');

function azwctabs_product_tabs_meta_box() {
    add_meta_box(
        'azwctabs_product_tabs',
        __('Product Custom Tabs', 'az-custom-tabs-for-woocommerce'),
        'azwctabs_product_tabs_callback',
        'product',
        'normal',
        'high'
    );
}

// Callback function for the meta box
function azwctabs_product_tabs_callback($post) {
    $product_tabs = get_post_meta($post->ID, '_azwctabs_product_tabs', true) ?: array();
    wp_nonce_field('azwctabs_product_tabs_nonce', 'azwctabs_product_tabs_nonce_field');
    ?>
    <div id="product-tabs-container">
        <?php if(!empty($product_tabs)){
                foreach ($product_tabs as $index => $tab): ?>
            <div class="product-tab">
                <input type="hidden" name="azwctabs_tab_index[]" value="<?php echo esc_attr($index); ?>"/>
                <input type="text" name="azwctabs_product_tabs[<?php echo esc_attr($index); ?>][title]" placeholder="<?php esc_attr_e('Tab Title', 'az-custom-tabs-for-woocommerce'); ?>" value="<?php echo esc_attr($tab['title']); ?>" style="width: 30%;">
                <textarea name="azwctabs_product_tabs[<?php echo esc_attr($index); ?>][content]" placeholder="<?php esc_attr_e('Tab Content', 'az-custom-tabs-for-woocommerce'); ?>" rows="4" style="width: 65%;"><?php echo esc_textarea($tab['content']); ?></textarea>
                <?php 
                if($index!=0){
                ?>
                <button type="button" class="remove-tab button"><?php esc_html_e('Remove', 'az-custom-tabs-for-woocommerce'); ?></button>
                <?php } ?>
            </div>
        <?php 
        endforeach;
    }else{
        ?>
            <div class="product-tab">
                <input type="hidden" name="azwctabs_tab_index[]" value="0"/>
                <input type="text" name="azwctabs_product_tabs[<?php echo esc_attr($index); ?>][title]" placeholder="<?php esc_attr_e('Tab Title', 'az-custom-tabs-for-woocommerce'); ?>" style="width: 30%;">
                <textarea name="azwctabs_product_tabs[<?php echo esc_attr($index); ?>][content]" placeholder="<?php esc_attr_e('Tab Content', 'az-custom-tabs-for-woocommerce'); ?>" rows="4" style="width: 65%;"></textarea>
            </div>
        <?php }?>
    </div>
    <button type="button" id="add-product-tab" class="button"><?php esc_html_e('Add Tab', 'az-custom-tabs-for-woocommerce'); ?></button>
    <br><br>
    <input type="submit" value="<?php esc_attr_e('Save Tabs', 'az-custom-tabs-for-woocommerce'); ?>" class="button button-primary">
    <?php
}

// Save the product-specific tabs using WooCommerce hook
add_action('woocommerce_process_product_meta', 'azwctabs_save_product_tabs');

function azwctabs_save_product_tabs($post_id) {
    // Verify nonce
    if (!isset($_POST['azwctabs_product_tabs_nonce_field'])) {
        return;
    }

    $nonce = sanitize_text_field(wp_unslash($_POST['azwctabs_product_tabs_nonce_field']));

    if (!wp_verify_nonce($nonce, 'azwctabs_product_tabs_nonce')) {
        return;
    }

    // Check if the user has permission to edit the post
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Prevent autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!isset($_POST['azwctabs_product_tabs'])) {
        delete_post_meta($post_id, '_azwctabs_product_tabs'); // Delete if no tabs are present
        return;
    }

    // Sanitize each tab's fields
    $sanitized_data = array_map(function($tab) {
        return [
            'title'   => isset($tab['title']) ? sanitize_text_field($tab['title']) : '',
            'content' => isset($tab['content']) ? sanitize_textarea_field($tab['content']) : '',
        ];
    }, wp_unslash($_POST['azwctabs_product_tabs']));

            // Debugging: Log sanitized data
            error_log(print_r($sanitized_data, true));


        // Update the post meta with sanitized data
        update_post_meta($post_id, '_azwctabs_product_tabs', $sanitized_data);
    
}

// Add product-specific tabs to products
add_filter('woocommerce_product_tabs', 'azwctabs_add_product_specific_tab');

function azwctabs_add_product_specific_tab($tabs) {
    global $post;
    $product_tabs = get_post_meta($post->ID, '_azwctabs_product_tabs', true) ?: array();
    foreach ($product_tabs as $tab) {
        if (!empty($tab['title']) && !empty($tab['content'])) {
            $tabs[sanitize_title($tab['title'])] = array(
                'title'    => esc_html($tab['title']),
                'priority' => 60,
                'callback' => function() use ($tab) {
                    echo esc_html(wp_strip_all_tags(wpautop($tab['content'])));
                },
            );
        }
    }
    return $tabs;
}


