<?php

if (!defined('ABSPATH'))
    exit;

class DCO_SM_Metaboxes {

    public function __construct() {
        add_action('init', array($this, 'init_hooks'));
    }

    public function init_hooks() {
        add_action('admin_enqueue_scripts', array($this, 'add_styles_and_scripts'));
        add_filter('enter_title_here', array($this, 'change_title_placeholder'));
        add_action('admin_menu', array($this, 'remove_metaboxes'));
        add_action('add_meta_boxes', array($this, 'add_metaboxes'));
        add_action('save_post', array($this, 'save'));
    }

    public function add_fields_metabox() {
        global $post;
        wp_nonce_field('dco_sm_metabox', 'dco_sm_metabox_nonce');
        $field_types = $this->get_field_types();
        ?>

        <h4><?php _e('If shortcode has attributes you can add them here.', 'dco-sm'); ?></h4>

        <div class="dco-sm-fields-list">
            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Attribute', 'dco-sm'); ?></th>
                        <th><?php _e('Label', 'dco-sm'); ?></th>
                        <th><?php _e('Type', 'dco-sm'); ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $fields = get_post_meta($post->ID, '_dco_sm_fields', true);
                    if ($fields && is_array($fields) && count($fields)) :
                        foreach ($fields as $field) :
                            ?>
                            <tr>
                                <td class="name"><?php echo $field['name']; ?></td>
                                <td class="label"><?php echo $field['label']; ?></td>
                                <td class="type" data-type="<?php echo esc_attr($field['type']); ?>"><?php echo $field['type_title']; ?></td>
                                <?php $field_data = json_encode($field); ?>
                                <td>
                                    <a href="#" class="button button-edit"><?php _e('Edit', 'dco-sm'); ?></a> 
                                    <a href="#" class="button button-delete"><?php _e('Delete', 'dco-sm'); ?></a>
                                    <textarea name="dco_sm_field[]" style="display: none;"><?php echo $field_data; ?></textarea>
                                </td>
                            </tr>
                            <?php
                        endforeach;
                    endif;
                    ?>
                </tbody>
            </table>
        </div>

        <div class="dco-sm-field-types">
            <h3 data-edit="<?php esc_attr_e('Edit field', 'dco-sm'); ?>" data-add="<?php esc_attr_e('Add new field', 'dco-sm'); ?>"><?php _e('Add new field', 'dco-sm'); ?></h3>
            <select data-default="-1">
                <option value="-1"><?php _e('Select field type', 'dco-sm'); ?></option>
                <?php foreach ($field_types as $id => $type) : ?>
                    <option value="<?php echo $id; ?>" data-type="<?php echo esc_attr($type->get_name()); ?>"><?php echo esc_html($type->get_title()); ?></option>
                <?php endforeach; ?>
            </select>

            <?php foreach ($field_types as $id => $type) : ?>
                <div class="dco-sm-field-type dco-sm-field-type<?php echo $id; ?>" data-type="<?php echo esc_attr($type->get_name()); ?>" data-type-title="<?php echo esc_attr($type->get_title()); ?>">
                    <table class="form-table">
                        <tr>
                            <th><label for="name"><?php _e('Shortcode Attribute', 'dco-sm'); ?></label></th>
                            <td>
                                <input name="name" type="text" class="regular-text required">
                                <p class="description"><?php _e('Attribute name used in the shortcode', 'dco-sm'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="label"><?php _e('Field Label', 'dco-sm'); ?></label></th>
                            <td>
                                <input name="label" type="text" class="regular-text required">
                                <p class="description"><?php _e('Label for field in shortcode insert form', 'dco-sm'); ?></p>
                            </td>
                        </tr>
                        <?php
                        $fields = $type->get_fields();
                        if (is_array($fields) && count($fields)) :
                            foreach ($fields as $name => $field) :
                                ?>
                                <tr>
                                    <th><label for="<?php echo esc_attr($name); ?>"><?php echo $field['label']; ?></label></th>
                                    <td>
                                        <?php
                                        switch ($field['type']) :
                                            case 'text':
                                                ?>
                                                <input name="<?php echo esc_attr($name); ?>" type="text" class="regular-text">
                                                <?php
                                                break;
                                            case 'textarea':
                                                ?>
                                                <textarea name="<?php echo esc_attr($name); ?>" rows="7" cols="40" class="code" placeholder="<?php echo isset($field['placeholder']) ? esc_attr($field['placeholder']) : ''; ?>"></textarea>
                                                <?php if (isset($field['description']) && !empty($field['description'])) : ?>
                                                    <p class="description"><?php echo $field['description']; ?></p>
                                                <?php endif; ?>
                                                <?php
                                                break;
                                            case 'checkbox':
                                                ?>
                                                <input type="checkbox" name="<?php echo esc_attr($name); ?>">
                                                <?php if (isset($field['description']) && !empty($field['description'])) : ?>
                                                    <p class="description"><?php echo $field['description']; ?></p>
                                                <?php endif; ?>
                                                <?php
                                                break;
                                        endswitch;
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            endforeach;
                        endif;
                        ?>
                    </table>
                    <a href="#" class="dco-sm-add-field button" data-edit="<?php esc_attr_e('Save field', 'dco-sm'); ?>" data-add="<?php esc_attr_e('Add field', 'dco-sm'); ?>"><?php _e('Add field', 'dco-sm'); ?></a>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }

    function add_post_types_metabox() {
        global $post;

        $post_types = get_post_types(array('_builtin' => false, 'public' => true), 'objects');
        $dco_sm_post_types = get_post_meta($post->ID, '_dco_sm_post_type');
        ?>
                        
        <?php if ($post->post_status != 'publish' || 0 == $post->ID) : ?>
            <?php if (post_type_exists('post')) : ?>
                <p>
                    <input type="checkbox" name="dco_sm_post_types[]" checked value="post"> <?php echo get_post_type_object('post')->labels->singular_name; ?>
                </p>
            <?php endif; ?>
            <?php if (post_type_exists('page')) : ?>
                <p>
                    <input type="checkbox" name="dco_sm_post_types[]" checked value="page"> <?php echo get_post_type_object('page')->labels->singular_name; ?>
                </p>
            <?php endif; ?>
        <?php else : ?>
            <?php if (post_type_exists('post')) : ?>
                <p>
                    <input type="checkbox" name="dco_sm_post_types[]" <?php checked(in_array('post', (array) $dco_sm_post_types)); ?> value="post"> <?php echo get_post_type_object('post')->labels->singular_name; ?>
                </p>
            <?php endif; ?>
            <?php if (post_type_exists('page')) : ?>
                <p>
                    <input type="checkbox" name="dco_sm_post_types[]" <?php checked(in_array('page', (array) $dco_sm_post_types)); ?> value="page"> <?php echo get_post_type_object('page')->labels->singular_name; ?>
                </p>
            <?php endif; ?>
        <?php endif; ?>

        <?php foreach ($post_types as $k => $post_type) :
            ?>
            <p>
                <?php if ($post->post_status != 'publish' || 0 == $post->ID) : ?>
                    <input type="checkbox" name="dco_sm_post_types[]" checked value="<?php echo esc_attr($k); ?>"> <?php echo $post_type->labels->singular_name; ?>
                <?php else : ?>
                    <input type="checkbox" name="dco_sm_post_types[]" <?php checked(in_array($k, (array) $dco_sm_post_types)); ?> value="<?php echo esc_attr($k); ?>"> <?php echo $post_type->labels->singular_name; ?>
                <?php endif; ?>
            </p>
            <?php
        endforeach;
    }

    function save($post_id) {
        if (!isset($_POST['dco_sm_metabox_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['dco_sm_metabox_nonce'], 'dco_sm_metabox')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['dco_sm_field']) && is_array($_POST['dco_sm_field']) && count($_POST['dco_sm_field'])) {
            $fields = array();
            foreach ($_POST['dco_sm_field'] as $index => $field) {
                $fields[$index] = array();
                $field = json_decode(stripcslashes($field), true);
                if (is_array($field) && count($field)) {
                    foreach ($field as $k => $v) {
                        $name = sanitize_text_field($k);
                        $value = implode("\n", array_map('sanitize_text_field', explode("\n", $v)));
                        $fields[$index][$name] = $value;
                    }
                }
            }
            update_post_meta($post_id, '_dco_sm_fields', $fields);
        } else {
            delete_post_meta($post_id, '_dco_sm_fields');
        }

        if (isset($_POST['dco_sm_show_in_menu'])) {
            update_post_meta($post_id, '_dco_sm_show_in_menu', sanitize_text_field($_POST['dco_sm_show_in_menu']));
        }

        delete_post_meta($post_id, '_dco_sm_post_type');
        if (isset($_POST['dco_sm_post_types']) && is_array($_POST['dco_sm_post_types']) && count($_POST['dco_sm_post_types'])) {
            foreach ($_POST['dco_sm_post_types'] as $post_type) {
                add_post_meta($post_id, '_dco_sm_post_type', sanitize_text_field($post_type));
            }
        }

        update_post_meta($post_id, '_dco_sm_description', implode("\n", array_map('sanitize_text_field', explode("\n", $_POST['dco_sm_description']))));
    }

    public function add_styles_and_scripts() {
        wp_enqueue_style('dco-sm', plugins_url('css/dco-sm.css', __FILE__));
        wp_enqueue_script('dco-sm', plugins_url('js/dco-sm.js', __FILE__), array('jquery', 'jquery-ui-sortable'));
        wp_localize_script('dco-sm', 'dco_sm', array(
            'edit' => esc_html__('Edit', 'dco-sm'),
            'delete' => esc_html__('Delete', 'dco-sm'),
            'delete_attr' => esc_html__('Are you sure you want to delete a field for attribute %attr%? This action can not be undone!', 'dco-sm')
        ) );
    }

    public function remove_metaboxes() {
        remove_meta_box('submitdiv', 'dco_shortcode', 'side');
        remove_meta_box('slugdiv', 'dco_shortcode', 'normal');
    }

    public function add_metaboxes() {
        add_meta_box('submitdiv', __('Publish'), array($this, 'add_submit_metabox'), 'dco_shortcode', 'side');
        add_meta_box('dco-sm-fields', __('Fields', 'dco-sm'), array($this, 'add_fields_metabox'), 'dco_shortcode', 'normal');
        add_meta_box('dco-sm-post_types', __('Post Types', 'dco-sm'), array($this, 'add_post_types_metabox'), 'dco_shortcode', 'side');
    }

    public function add_submit_metabox() {
        global $post;

        $post_type = $post->post_type;
        $post_type_object = get_post_type_object($post_type);
        $can_publish = current_user_can($post_type_object->cap->publish_posts);
        ?>
        <div class="submitbox" id="submitpost">
            <div class="misc-pub-section">
                <p>
                    <input type="hidden" name="dco_sm_show_in_menu" value="0">
                    <?php if ($post->post_status != 'publish' || 0 == $post->ID) : ?>
                        <input type="checkbox" name="dco_sm_show_in_menu" value="1" checked>
                    <?php else : ?>
                        <input type="checkbox" name="dco_sm_show_in_menu" <?php checked(get_post_meta($post->ID, '_dco_sm_show_in_menu', true), 1); ?> value="1">
                    <?php endif; ?>
                    <?php _e('Show in menu', 'dco-sm'); ?>
                </p>
                <p>
                    <label for="dco_sm_description"><strong><?php _e('Description', 'dco-sm'); ?></strong></label>
                    <textarea name="dco_sm_description" class="large-text code" rows="3" placeholder="<?php esc_attr_e('Short description (used only for admin list of shortcodes).', 'dco-sm'); ?>"><?php echo get_post_meta($post->ID, '_dco_sm_description', true); ?></textarea>
                </p>
            </div>
            <div id="major-publishing-actions">
                <div id="delete-action">
                    <?php
                    if (current_user_can("delete_post", $post->ID)) :
                        if (!EMPTY_TRASH_DAYS)
                            $delete_text = __('Delete Permanently');
                        else
                            $delete_text = __('Move to Trash');
                        ?>
                        <a class="submitdelete deletion" href="<?php echo get_delete_post_link($post->ID); ?>"><?php echo $delete_text; ?></a>
                    <?php endif; ?>
                </div>

                <div id="publishing-action">
                    <span class="spinner"></span>
                    <?php
                    if ($post->post_status != 'publish' || 0 == $post->ID) {
                        if ($can_publish) :
                            ?>
                            <input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Publish') ?>" />
                            <?php submit_button(__('Publish'), 'primary button-large', 'publish', false); ?>
                            <?php
                        endif;
                    } else {
                        ?>
                        <input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Update') ?>" />
                        <input name="save" type="submit" class="button button-primary button-large" id="publish" value="<?php esc_attr_e('Update') ?>" />
                    <?php } ?>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <?php
    }

    public function change_title_placeholder($title) {
        $screen = get_current_screen();
        if ('dco_shortcode' == $screen->post_type) {
            $title = esc_attr__('Enter shortcode without [] and attributes', 'dco-sm');
        }
        return $title;
    }
    
     public function get_field_types() {
        $field_types = array();

        foreach (glob(DCO_SM__PLUGIN_DIR . 'fields/*_field.php') as $filename) {
            require_once $filename;
            $field_class = 'DCO_SM_Field_' . ucfirst(basename($filename, '_field.php'));
            if (class_exists($field_class)) {
                $field_types[] = new $field_class;
            }
        }

        return $field_types;
    }

}

$dco_sm_metaboxes = new DCO_SM_Metaboxes();
