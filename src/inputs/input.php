<?php
namespace calisia_hide_category\inputs;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}


class Input{
    public $label = "label";
    public $type = "text";
    public $id;
    public $name;
    public $class;
    public $value;

    public function Render(\calisia_hide_category\renderer\interfaces\IRenderer $renderer, bool $render = true){
        return $renderer->render(
            'inputs/input',
            [
                'label' => $this->label,
                'type' => $this->type,
                'id' => $this->id,
                'name' => $this->name,
                'class' => $this->class,
                'value' => $this->value
            ],
            $render
        );
    }
}

