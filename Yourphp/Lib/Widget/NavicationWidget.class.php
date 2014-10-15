<?php

/**
 * Navication widget
 * @package Yourphp\Lib\Widget
 */

class NavicationWidget extends Widget {

    public function render($data) {
    	// $data['category'] = $this->tpl->get('Categorys');
    	// dump($data);
        $content = $this->renderFile('', $data);
        return $content;
    }
}