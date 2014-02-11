<?php

use sJo\Modules\User\Model\User;
use sJo\View\Helper;
use sJo\Libraries as Lib;

self::header();

Helper\Panel::create(array(
    'col' => 6,
    'title' => 'Edition',
    'type' => 'primary',
    'elements' => Helper\Fieldset::create(array(
        Helper\Token::create('User/Profile::update'),
        Helper\Input::create(array(
            'type' => 'email',
            'name' => 'email',
            'label' => Lib\I18n::__('Email address'),
            'placeholder' => Lib\I18n::__('Enter email'),
            'value' => User::getInstance()->email
        )),
        Helper\Input::create(array(
            'type' => 'text',
            'name' => 'name',
            'label' => Lib\I18n::__('Name'),
            'placeholder' => Lib\I18n::__('Enter name'),
            'value' => User::getInstance()->name
        ))
    )),
    'footer' => Helper\Button::create(array(
        'class' => 'pull-right',
        'value' => Lib\I18n::__('Save')
    ))
))->display();

self::footer();
