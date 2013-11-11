<?php

namespace PHPTools\Modules\User\Controller;

use \PHPTools\Libraries as Lib;
use \PHPTools\Libraries\I18n;
use \PHPTools\Modules\User\Core\User;

class Auth extends \PHPTools\Controller
{
    public function signin()
    {
        if(Lib\Env::post('email')) {
            if(Lib\Env::post('password')) {
                if($token = $this->Model->exists(Lib\Env::post('email'), Lib\Env::post('password'))) {
                    $this->Logger->info('Signin {user}', array(
                        'user' => Lib\Env::post('email')
                    ));
                    $this->Core->Session->signin($token);
                } else {
                    $this->Core->Alert->add(I18n::__('Les informations de connexion sont incorrects'));
                }
            } else {
                $this->Core->Alert->add(I18n::__('Veuillez renseigner votre mot de passe'));
            }
        } else {
            $this->Core->Alert->add(I18n::__('Veuillez renseigner votre identifiant'));
        }
    }

    public function signout ()
    {
        $this->Logger->info('Signout {user}', array(
            'user' => Lib\Env::session('token')
        ));
        $this->Core->Session->signout();
    }
}