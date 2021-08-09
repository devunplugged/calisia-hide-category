<?php
namespace calisia_hide_category\settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}


class MenuPage{
    public $pageTitle;
    public $menuTitle;
    public $capability;
    public $menuSlug;
    public $pageTemplate = '';
    public $iconUrl = '';
    public $position = null;
    public $optionGroup;
    public $page;

    private $renderer;
    
    function __construct(\calisia_hide_category\renderer\interfaces\IRenderer $renderer){
        $this->renderer = $renderer;
    }

    public function Add(){
        \add_menu_page(
            $this->pageTitle,
            $this->menuTitle,
            $this->capability,
            $this->menuSlug,
            [$this, 'Render'],
            $this->iconUrl,
            $this->position
        );
    }

    public function Render(){
        $this->renderer->render(
            $this->pageTemplate,
            [
                'title' => $this->pageTitle,
                'optionGroup' => $this->optionGroup,
                'page' => $this->page
            ]
        );
    }
}