<?php

namespace sJo\View\Helper\Drivers\Html;

use sJo\View\Helper\Dom;

class Input extends Dom
{
    protected static $element = array(
        'tagname' => 'input',
        'options' => null,
        'attributes' => array(
            'type'        => 'text',
            'placeholder' => null,
            'autofocus'   => null,
            'name'        => null,
            'value'       => null,
        )
    );

    protected function setElement($element)
    {
        switch ($element['attributes']['type']) {

            case 'password':
                $element['attributes']['value'] = null;
                break;
        }

        return parent::setElement($element);
    }
}
