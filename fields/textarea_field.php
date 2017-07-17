<?php

if (!defined('ABSPATH'))
    exit;

class DCO_SM_Field_Textarea {

    protected $name = 'Textarea';
    protected $title;
    protected $fields;

    public function __construct() {
        $this->title = esc_html__('Textarea', 'dco-sm');
        
        $this->fields = array(
            'default_value' => array(
                'label' => __('Default Value', 'dco-sm'),
                'type' => 'textarea'
            )
        );
    }

    public function get_name() {
        return $this->name;
    }
    
    public function get_title() {
        return $this->title;
    }

    public function get_fields() {
        return $this->fields;
    }

    public function get_js($args = array()) {
        $default_value = isset($args['default_value']) ? $args['default_value'] : '';
        
        $js = array(
            'type' => 'textbox',
            'multiline' => true,
            'minWidth' => 300,
            'minHeight' => 100,
            'value' => $default_value
        );

        return $js;
    }

}
