<?php

use sJo\Libraries as Lib;
use sJo\View\Helper;

?><!DOCTYPE html>
<html lang="<?php echo Lib\I18n::country(); ?>">
<head>
    <meta charset="<?php echo SJO_CHARSET; ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?php echo SJO_BASEHREF; ?>" />
    <title><?php Lib\I18n::_e('Authentification'); ?></title>
    <meta name="description" content="<?php Lib\I18n::_e('Authentification'); ?>">
    <?php Helper\Style::display(); ?>
    <style type="text/css">
        body {
            background:#333
        }
        .form-signin {
            background:#fff;
            width:300px;
            margin:0 auto;
            padding:0 20px 20px;
            border:5px solid #000;
            border-radius:10px;
            box-shadow: 0 0 10px #000;
        }
    </style>
</head>
<body>

<?php

Helper\Form::create(array(
    'class' => 'form-signin',
    'method' => 'post',
    'elements' => Helper\Fieldset::create(array(
        Helper\Container::create(array(
            'tagname' => 'h2',
            'class' => 'form-signin-heading',
            'elements' => Lib\I18n::__('Authentification')
        )),
        Helper\Token::create('User/Auth::signin'),
        Helper\Input::create(array(
            'name' => 'email',
            'value' => Lib\Env::post('email'),
            'placeholder' => Lib\I18n::__('Adresse email'),
            'autofocus' => true
        )),
        Helper\Input::create(array(
            'type' => 'password',
            'name' => 'password',
            'placeholder' => Lib\I18n::__('Mot de passe')
        )),
        Helper\Button::create(array(
            'class' => 'btn-lg btn-block',
            'value' => Lib\I18n::__('Connexion')
        ))
    ))
))->display();

?>
</body>
</html>