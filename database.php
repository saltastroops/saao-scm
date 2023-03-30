<?php

/**
 * A class for connecting to the SCM database.
 *
 * The class provides a static method which returns a PDO instance for connecting to the
 * SCM database.
 */
class SCMDatabase {
    private static $pdo = null;

    /**
     * Return a PDO instance for connecting to the SCM database.
     */
    public static function get_pdo() {
        if (SCMDatabase::$pdo === null) {
            $host = get_option('saao_scm_database_host');
            $username = get_option('saao_scm_database_username');
            $password = get_option('saao_scm_database_password');
            $database_name = get_option('saao_scm_database_name');
            SCMDatabase::$pdo = new PDO("mysql:host=$host;dbname=$database_name", $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        }

        return SCMDatabase::$pdo;
    }
}
