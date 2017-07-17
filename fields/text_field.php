<?php

if (!defined('ABSPATH'))
    exit;

class DCO_SM_Field_Text {

    protected $name = 'Text';
    protected $title;
    protected $fields;

    public function __construct() {
        $this->title = esc_html__('Text', 'dco-sm');
        
        $this->fields = array(
            'text' => array(
                'label' => __('Text', 'dco-sm'),
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
        $text = isset($args['text']) ? $args['text'] : '';
        
        return array(
            'label' => ' ',
            'name' => ' ',
            'type' => 'container',
            'html' => $text
        );
    }

}
