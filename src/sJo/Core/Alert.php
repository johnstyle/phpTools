<?php

/**
 * Alertes
 *
 * PHP version 5
 *
 * @package  sJo
 * @category Core
 * @author   Jonathan Sahm <contact@johnstyle.fr>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/johnstyle/sjo.git
 */

namespace sJo\Core;

use sJo\Libraries as Lib;

/**
 * Alertes
 *
 * @package  sJo
 * @category Core
 * @author   Jonathan Sahm <contact@johnstyle.fr>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/johnstyle/sjo.git
 */
class Alert
{
    private $alerts;

    public function __construct()
    {
        Session::start();

        if (Lib\Env::sessionExists('alerts')) {
            $this->alerts = json_decode(Lib\Env::session('alerts'));
        }
    }

    public function __destruct()
    {
        Lib\Env::sessionSet('alerts', json_encode($this->alerts));
    }

    public function add($message, $type = 'danger')
    {
        $this->alerts[$type][] = $message;
    }

    public function exists()
    {
        if ($this->alerts) {
            return true;
        }
        return false;
    }

    public function display()
    {
        if ($this->exists()) {
            foreach ($this->alerts as $type => $alerts) {
                echo '<div class="alert alert-' . $type . '">';
                if (count($alerts) > 1) {
                    echo '<ol>';
                    foreach ($alerts as $alert) {
                        echo '<li>' . $alert . '</li>';
                    }
                    echo '</ol>';
                } else {
                    foreach ($alerts as $alert) {
                        echo '<p>' . $alert . '</p>';
                    }
                }
                echo '</div>';
            }
        }

        Lib\Env::sessionSet('alerts');
        $this->alerts = null;
    }
}