<?php

namespace sJo\View\Helper\Drivers\Html;

use sJo\Loader;
use sJo\Loader\Router;
use sJo\View\Helper\Dom;
use sJo\Libraries as Lib;

class Token extends Dom
{
    protected static $element = array(
        'attributes' => array(
            'type' => 'hidden'
        )
    );

    public function setElement($element)
    {
        $token = isset($element[$this->wrapper][0]) ? $element[$this->wrapper][0] : null;
        $controller = null;
        $method = null;

        if($token) {

            if(preg_match("#^([a-z]+(\\\\([a-z]+))?)(" . Router::$__map['method']['separator'] . "([a-z]+))?$#i", $token, $match)) {
                $controller = $match[1];
                $method = $match[5];
            }

            $token = Loader\Token::get($token);
        }

        $element['elements'] = array(
            self::createStatic('Input', Lib\Arr::extend(self::$element, array(
                'attributes' => array(
                    'name' => 'token',
                    'value' => $token
                )
            ))),
            self::createStatic('Input', Lib\Arr::extend(self::$element, array(
                'attributes' => array(
                    'name' => 'controller',
                    'value' => $controller
                )
            ))),
            self::createStatic('Input', Lib\Arr::extend(self::$element, array(
                'attributes' => array(
                    'name' => 'method',
                    'value' => $method
                )
            ))),
        );

        return $element;
    }
}
