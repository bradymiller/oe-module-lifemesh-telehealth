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

use OpenEMR\Common\Crypto;

class Database
{
    public $cryptoGen;

    public function __construct()
    {
        $this->cryptoGen = new Crypto\CryptoGen();
    }

    private function createLifemeshDb()
    {
        $DBSQL_SESSIONS = <<<'DB'
CREATE TABLE IF NOT EXISTS lifemesh_chime_sessions(
  `id`            int         NOT NULL primary key AUTO_INCREMENT comment 'Primary Key',
  `pc_eid`        int(11)     NOT NULL UNIQUE comment 'Event ID from Calendar Table',
  `meeting_id`    VARCHAR(50) NOT NULL comment 'chime session ID',
  `patient_code`  VARCHAR(8)  NOT NULL comment 'Patient PIN',
  `patient_uri`   TEXT        NOT NULL comment 'Patient URI',
  `provider_code` VARCHAR(8)  NOT NULL comment 'Provider PIN',
  `provider_uri`  TEXT        NOT NULL comment 'Provider URI',
  `event_date`    DATE    NOT NULL,
  `event_time`    TIME    NOT NULL,
  `event_status`  VARCHAR(15)  NOT NULL,
  `updatedAt`     DATETIME    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB COMMENT = 'lifemesh chime sessions';
DB;

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
        $exist = sqlQuery("SHOW TABLES FROM `$db` LIKE 'lifemesh_account'");
        if (empty($exist)) {
             sqlQuery($DBSQL);
             sqlQuery($DBSQL_SESSIONS);
        }

    }

    public function doesTableExist()
    {
        $db = $GLOBALS['dbase'];
        $exist = sqlQuery("SHOW TABLES FROM `$db` LIKE 'lifemesh_account'");
        if (empty($exist)) {
            self::createLifemeshDb();
            return "created";
        } else {
            return "exist";
        }
    }

    public function saveUserInformation($username, $password)
    {
        $pass = $this->cryptoGen->encryptStandard($password);
        $sql = "INSERT INTO lifemesh_account SET id = 1, username = ?, password = ?";
        sqlStatement($sql, [$username, $pass]);
        return true;
    }

    public function removeAccountInfo()
    {
        $sql = "DELETE FROM lifemesh_account";
         sqlStatement($sql);
         return "completed";
    }
}
