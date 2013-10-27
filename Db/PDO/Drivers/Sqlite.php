<?php

/**
 * Base de données Sqlite
 *
 * PHP version 5
 *
 * @package  PHPTools
 * @category Db\PDO\Drivers
 * @author   Jonathan Sahm <contact@johnstyle.fr>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/johnstyle/PHPTools.git
 */

namespace PHPTools\Db\PDO\Drivers;

/**
 * Base de données Sqlite
 *
 * @package  PHPTools
 * @category Db\PDO\Drivers
 * @author   Jonathan Sahm <contact@johnstyle.fr>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/johnstyle/PHPTools.git
 */
class Sqlite extends \PHPTools\Db\PDO\PDOCore
{
    /**
     * Constructeur
     *
     * @param array $auth
     * @return \PHPTools\Db\PDO\Drivers\Sqlite
     */
    public function __construct(array $auth)
    {
        try {
            parent::__construct('sqlite:' . $auth[0]);
            $this->setAttribute(self::ATTR_DEFAULT_FETCH_MODE, self::FETCH_ASSOC);
            $this->setAttribute(self::ATTR_ERRMODE, self::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            die('ERROR: ' . $e->getMessage());
        }
    }
}
