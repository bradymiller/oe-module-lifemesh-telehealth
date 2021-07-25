<?php
/*
 *
 * @package      OpenEMR
 * @link               https://www.open-emr.org
 *
 * @author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * @copyright Copyright (c) 2021 Sherwin Gaddis <sherwingaddis@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 *
 */

namespace OpenEMR\Modules\LifeMesh;


class Database
{
    public function __construct()
    {

    }
    private function createLifemeshDb()
    {
        $DBSQL = <<<'DB'
 CREATE TABLE IF NOT EXISTS `lifemesh_account`
(
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` TEXT DEFAULT NULL,
    `password` TEXT DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB COMMENT = 'Lifemesh Telehealth';
DB;
        $db = $GLOBALS['dbase'];
        $exist = sqlQuery("SHOW TABLES FROM `$db` LIKE 'lifemesh_user'");
        if (empty($exist)) {
             sqlQuery($DBSQL);
        }
    }

    public function doesTableExist()
    {
        $db = $GLOBALS['dbase'];
        $exist = sqlQuery("SHOW TABLES FROM `$db` LIKE 'lifemesh_user'");
        if (empty($exist)) {
            self::createLifemeshDb();
            return "created";
        }
    }

}
