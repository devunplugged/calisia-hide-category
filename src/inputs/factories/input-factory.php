<?php
namespace calisia_hide_category\inputs\factories;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}


class InputFactory{
    public static function Create(string $type){
        switch($type){
            case 'input': return new \calisia_hide_category\inputs\Input(); break;
        }
    }
}
