<?php

if (!defined('ABSPATH'))
    exit;

class DCO_SM_Posttype {

    public function __construct() {
        add_action('admin_init', array($this, 'init_hooks'));
        add_action('init', array($this, 'register_posttype'));
    }

    public function init_hooks() {
        add_action('post_row_actions', array($this, 'remove_quick_edit'));
        add_filter('manage_dco_shortcode_posts_columns', array($this, 'manage_columns'));
        add_action('manage_dco_shortcode_posts_custom_column', array($this, 'add_column_content'), 10, 2);
        add_action('admin_menu', array($this, 'remove_metaboxes'));
        add_filter('view_mode_post_types', array($this, 'remove_view_mode'));
        add_filter('months_dropdown_results', array($this, 'remove_months_dropdown'), 10, 2);
        add_action('restrict_manage_posts', array($this, 'add_filters'));
        add_action('pre_get_posts', array($this, 'filter_posts'));
    }

    public function remove_quick_edit($actions) {
        global $post;
        if ($post->post_type == 'dco_shortcode') {
            unset($actions['inline hide-if-no-js']);
        }
        return $actions;
    }

    public function manage_columns($columns) {
        unset($columns['date']);
        $columns['title'] = __('Shortcode', 'dco-sm');
        $columns['description'] = __('Description', 'dco-sm');
        $columns['show_in_menu'] = __('Show in menu', 'dco-sm');
        $columns['post_types'] = __('Post Types', 'dco-sm');

        return $columns;
    }

    public function add_column_content($column_name, $post_id) {
        if ($column_name == 'description') {
            $description = get_post_meta($post_id, '_dco_sm_description', true);
            echo $description;
        }
        if ($column_name == 'show_in_menu') {
            $show_in_menu = get_post_meta($post_id, '_dco_sm_show_in_menu', true);
            $show_in_menu ? _e('Yes') : _e('No');
        }
        if ($column_name == 'post_types') {
            $post_types_content = array();
            $post_types = get_post_meta($post_id, '_dco_sm_post_type');
            if(is_array($post_types) && count($post_types)) {
                foreach ($post_types as $post_type) {
                    if(post_type_exists($post_type)) {
                        $post_types_content[] = get_post_type_object($post_type)->labels->singular_name;
                    }
                }
            }
            echo implode(', ', $post_types_content);
        }
    }

    public function remove_view_mode($view_mode_post_types) {
        unset($view_mode_post_types['dco_shortcode']);

        return $view_mode_post_types;
    }

    public function remove_months_dropdown($months, $post_type) {
        if ($post_type == 'dco_shortcode') {
            return array();
        }

        return $months;
    }

    public function add_filters() {
        $screen = get_current_screen();

        if ($screen->post_type == 'dco_shortcode') :
            ?>
            <?php $show_in_menu = isset($_GET['show_in_menu']) ? $_GET['show_in_menu'] : ''; ?>
            <select name="show_in_menu">
                <option value="0" <?php selected($show_in_menu, 0); ?>><?php _e('Show in menu', 'dco-sm'); ?>: <?php _e('Yes'); ?>/<?php _e('No'); ?></option>
                <option value="yes" <?php selected($show_in_menu, 'yes'); ?>><?php _e('Show in menu', 'dco-sm'); ?>: <?php _e('Yes'); ?></option>
                <option value="no" <?php selected($show_in_menu, 'no'); ?>><?php _e('Show in menu', 'dco-sm'); ?>: <?php _e('No'); ?></option>
            </select>
            <?php $filter_post_type = isset($_GET['filter_post_type']) ? $_GET['filter_post_type'] : ''; ?>
            <select name="filter_post_type">
                <option value="0" <?php selected($filter_post_type, 0); ?>><?php _e('All post types', 'dco-sm'); ?></option>
                <?php $post_types = get_post_types(array('_builtin' => false, 'public' => true), 'objects'); ?>

                <?php if (post_type_exists('post')) : ?>
                    <option value="post" <?php selected($filter_post_type, 'post'); ?>><?php echo get_post_type_object('post')->labels->singular_name; ?></option>
                <?php endif; ?>
                <?php if (post_type_exists('page')) : ?>
                    <option value="page" <?php selected($filter_post_type, 'page'); ?>><?php echo get_post_type_object('page')->labels->singular_name; ?></option>
                <?php endif; ?>

                <?php foreach ($post_types as $k => $post_type) : ?>
                    <option value="<?php echo $k; ?>" <?php selected($filter_post_type, $k); ?>><?php echo $post_type->labels->singular_name; ?></option>
                <?php endforeach; ?>
            </select>
            <?php
        endif;
    }

    public function filter_posts($query) {
        global $post_type, $pagenow;

        if ($pagenow == 'edit.php' && $post_type == 'dco_shortcode' && $query->is_main_query()) {

            $meta_query = array();
            if (isset($_GET['show_in_menu'])) {
                $show_in_menu = sanitize_text_field($_GET['show_in_menu']);
                if ($show_in_menu) {
                    $meta_query[] = array(
                        'key' => '_dco_sm_show_in_menu',
                        'value' => $show_in_menu == 'yes' ? '1' : '0',
                    );
                }
            }

            if (isset($_GET['filter_post_type'])) {
                $filter_post_type = sanitize_text_field($_GET['filter_post_type']);
                if ($filter_post_type) {
                    $meta_query[] = array(
                        'key' => '_dco_sm_post_type',
                        'value' => $filter_post_type
                    );
                }
            }

            if (count($meta_query)) {
                $query->set('meta_query', $meta_query);
            }
        }
    }

    public function register_posttype() {
        $labels = array(
            'name' => __('Shortcodes', 'dco-sm'),
            'singular_name' => __('Shortcode', 'dco-sm'),
            'menu_name' => __('Shortcodes', 'dco-sm'),
            'all_items' => __('DCO Shortcodes Menu', 'dco-sm'),
            'add_new' => __('Add New', 'dco-sm'),
            'add_new_item' => __('Add New Shortcode', 'dco-sm'),
            'edit' => __('Edit'),
            'edit_item' => __('Edit Shortcode', 'dco-sm'),
            'new_item' => __('New Shortcode', 'dco-sm'),
            'view' => __('View', 'dco-sm'),
            'view_item' => __('View Shortcode', 'dco-sm'),
            'search_items' => __('Search Shortcode', 'dco-sm'),
            'not_found' => __('No Shortcodes found', 'dco-sm'),
            'not_found_in_trash' => __('No Shortcodes found in Trash', 'dco-sm'),
        );

        $args = array(
            'labels' => $labels,
            'description' => __('Custom post type for DCO Shortcodes Menu', 'dco-sm'),
            'public' => false,
            'show_ui' => true,
            'show_in_rest' => false,
            'has_archive' => false,
            'show_in_menu' => 'options-general.php',
            'show_in_admin_bar' => false,
            'exclude_from_search' => false,
            'capability_type' => 'post',
            'map_meta_cap' => true,
            'hierarchical' => false,
            'rewrite' => false,
            'query_var' => false,
            'supports' => array('title'),
        );
        register_post_type('dco_shortcode', $args);
    }

}

$dco_sm_posttype = new DCO_SM_Posttype();
