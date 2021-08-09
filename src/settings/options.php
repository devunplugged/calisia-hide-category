<?php
namespace calisia_hide_category\settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class Options{
    private $optionName;

    function __construct($optionName){
        $this->optionName = $optionName;
    }

    public function RegisterSetting($optionGroup, $args=[]){
        \register_setting(
            $optionGroup,
            $this->optionName,
            $args
        );
    }

    public function option_on($option_name){
        $options = get_option( $this->optionName );
        if(!isset($options[$option_name]) || !$options[$option_name])
            return 0;
        return 1;
    }

    public function get_option_value($option_name){
        $options = get_option( $this->optionName );
        if(isset($options[$option_name]) && $options[$option_name])
            return $options[$option_name];
        return '';
    }
}