<?php
namespace calisia_hide_category\renderer;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}



class DefaultRenderer implements interfaces\IRenderer{
    public function render(string $template, array $args = [], bool $render = true){
        if(!$render)
            ob_start();
    
        include  CALISIA_HIDE_CATEGORY_ROOT . '/templates/'.$template.'.php';
        
        if(!$render){
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }
    }
}