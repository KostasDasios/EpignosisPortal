<?php

class Database
{
    const HOST = 'localhost';
    const USER = 'root';
    const PASS = '';
    const DBNAME = 'epignosis_portal';

    private static $connection = null;
    private static $dsn = 'mysql:host=' . self::HOST . ';dbname=' . self::DBNAME;

    private function __construct() {
    }

    public static function getConnection() {
        if(is_null(self::$connection)) {
            
            try
			{  
			   self::$connection = new PDO(self::$dsn, self::USER, self::PASS);
			   //self::$connection = new PDO("mysql:host=localhost;dbname=epignosis_portal", 'root', '');

			   
			   /* Enable exceptions on errors */
			   self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			catch (PDOException $e)
			{
			   echo 'Database connection failed: '.$e->getMessage();
			   die();
			}
        }
        return self::$connection;
    }
}
?>