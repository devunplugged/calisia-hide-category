<?php
namespace calisia_hide_category\settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class Section{
    public $id;
    public $title;
    public $template;
    public $page;
    public $text;

    private $renderer;
    
    function __construct(\calisia_hide_category\renderer\interfaces\IRenderer $renderer){
        $this->renderer = $renderer;
    }

    public function Add(){
        \add_settings_section( 
            $this->id,
            $this->title,
            [$this, 'Render'],
            $this->page 
        );
    }

    public function Render(){
        $this->renderer->render(
            $this->template,
            [
                'text' => $this->text
            ]
        );
    }  
}