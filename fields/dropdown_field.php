<?php

if (!defined('ABSPATH'))
    exit;

class DCO_SM_Field_Dropdown {

    protected $name = 'Dropdown';
    protected $title;
    protected $fields;

    public function __construct() {
        $this->title = esc_html__('Dropdown', 'dco-sm');
        
        $this->fields = array(
            'choices' => array(
                'label' => __('Choices', 'dco-sm'),
                'type' => 'textarea',
                'description' => __('Enter each choice on a new line<br>value or label : value', 'dco-sm')
            ),
            'allow_user_choice' => array(
                'label' => __('Allow User Choice', 'dco-sm'),
                'type' => 'checkbox',
            ),
            'allow_blank_choice' => array(
                'label' => __('Allow Blank Choice', 'dco-sm'),
                'type' => 'checkbox',
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
        $choices = isset($args['choices']) ? $args['choices'] : '';
        if (!empty($choices)) {
            $values = array();
            $choices = explode("\n", $choices);

            if (isset($args['allow_blank_choice']) && $args['allow_blank_choice']) {
                $values[] = array('text' => '', 'value' => '');
            }
            
            foreach ($choices as $choice) {
                $el = explode(' : ', $choice);
                if (count($el) == 1) {
                    $values[] = array('text' => $el[0], 'value' => $el[0]);
                } else if (count($el) == 2) {
                    $values[] = array('text' => $el[0], 'value' => $el[1]);
                }
            }

            if (isset($args['allow_user_choice']) && $args['allow_user_choice']) {
                $type = 'combobox';
            } else {
                $type = 'listbox';
            }

            return array(
                'type' => $type,
                'values' => $values
            );
        }

        return false;
    }

}
