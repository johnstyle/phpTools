<?php

namespace sJo\View\Helper\Drivers\Bootstrap;

use sJo\Data\Validate;
use sJo\View\Helper\Dom;

class Panel extends Dom
{
    protected $wrapper = 'main';
    protected $wrappers = array(
        'main',
        'header',
        'footer'
    );

    protected static $element = array(
        'tagname' => 'form',
        'attributes' => array(
            'method' => 'post'
        ),
        'header' => null,
        'footer' => null
    );

    public function setElement($element)
    {
        $element['attributes']['class'] .= ' panel panel-default';

        if (!is_null($element['header'])
            && Validate::isString($element['header'])) {

            $element['header'] = Container::create(array(
                'attributes' => array(
                    'class' => 'panel-title'
                ),
                'elements' => $element['header']
            ));
       }

       return $element;
    }
}
