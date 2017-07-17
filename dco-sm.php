<?php

if (!defined('ABSPATH'))
    exit;

class DCO_SM {

    public function __construct() {
        add_action('init', array($this, 'init_hooks'));
    }

    public function init_hooks() {
        // Check if WYSIWYG is enabled
        if ('true' == get_user_option('rich_editing')) {
            add_action('admin_footer', array($this, 'get_shortcodes'));
            add_filter('mce_external_plugins', array($this, 'add_tinymce_plugin'));
            add_filter('mce_buttons', array($this, 'register_mce_button'));
        }
    }

    public function get_shortcodes() {
        global $post;
        $post_type = get_post_type($post->ID);

        $args = array(
            'post_type' => 'dco_shortcode',
            'orderby' => 'title',
            'order' => 'ASC',
            'meta_query' => array(
                array(
                    'key' => '_dco_sm_show_in_menu',
                    'value' => 1,
                ),
                array(
                    'key' => '_dco_sm_post_type',
                    'value' => $post_type
                )
            ),
        );

        $shortcodes = get_posts($args);
        $tags = array();
        foreach ($shortcodes as $k => $post) {
            setup_postdata($post);

            $tags[$k] = array();
            $tags[$k]['name'] = get_the_title();
            $fields = get_post_meta(get_the_ID(), '_dco_sm_fields', true);
            if (is_array($fields) && count($fields)) {
                foreach ($fields as $index => $field) {
                    $type = $this->get_field_type($field['type']);
                    if ($type) {
                        // Get fields for TinyMCE
                        $js_fields = $type->get_js($field);
                        if ($js_fields && is_array($js_fields)) {
                            $js_fields_default = array(
                                'name' => $field['name'],
                                'label' => $field['label']
                            );
                            // Check is one field or more
                            if (isset($js_fields['type'])) {
                                $tags[$k]['fields'][] = array_merge($js_fields_default, $js_fields);
                            } else {
                                foreach ($js_fields as $js_field) {
                                    $tags[$k]['fields'][] = array_merge($js_fields_default, $js_field);
                                }
                            }
                        }
                    }
                }
            }
        }
        wp_reset_query();

        echo "<script>var shortcodes = " . json_encode($tags) . "; var dco_sm_shortcodes_title = '" . esc_html__('Shortcodes', 'dco-sm') . "';</script>\n";
    }

    // Declare script for new button
    function add_tinymce_plugin($plugin_array) {
        $plugin_array['dco_sm_mce_button'] = plugin_dir_url(__FILE__) . '/js/dco-sm-mce-button.js';
        return $plugin_array;
    }

    // Register new button in the editor
    function register_mce_button($buttons) {
        array_push($buttons, 'dco_sm_mce_button');
        return $buttons;
    }

    public function get_field_type($name) {
        $filename = DCO_SM__PLUGIN_DIR . 'fields/' . strtolower($name) . '_field.php';
        if (file_exists($filename)) {
            require_once $filename;
            $field_class = 'DCO_SM_Field_' . ucfirst(strtolower($name));
            if (class_exists($field_class)) {
                return new $field_class;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}

$dco_sm = new DCO_SM();
