<?php

if (!defined('ABSPATH'))
    exit;

class DCO_SM_Field_Colorpicker {

    protected $name = 'ColorPicker';
    protected $title;
    protected $fields = array();

    public function __construct() {
        $this->title = esc_html__('Color Picker', 'dco-sm');
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
        return array(
            'type' => 'colorpicker'
        );
    }

}
