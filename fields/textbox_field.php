<?php

if (!defined('ABSPATH'))
    exit;

class DCO_SM_Field_Textbox {

    protected $name = 'Textbox';
    protected $title;
    protected $fields;

    public function __construct() {
        $this->title = esc_html__('Textbox', 'dco-sm');
        
        $this->fields = array(
            'default_value' => array(
                'label' => __('Default Value', 'dco-sm'),
                'type' => 'text'
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
        
        return array(
            'type' => 'textbox',
            'value' => $default_value
        );
    }

}
