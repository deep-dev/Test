<?php

class EmailCountProcessor
{
	private $dbh;			// PDO instance db connection
	
	private $stateFileName = 'example2.txt';	// Current state for processing items

	private $lockFileName = 'example2.pid';		// Lock file for single running

	private $logFileName = '';					// Log file for output data
    
	private $selectStatement;					// PDOStatement query for select data

	private $maxId = -1;						// Current max id user, already processed

	private $batchSize = 1000;					// Size of processed data

	private $domainsCount = 0;					// Domains count
	
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }
}

/* Connecting to db */
function connect_db() {
	$dsn = 'mysql:dbname=example1;host=localhost';
	$user = 'root';
	$password = '';
	$driver = array(PDO :: MYSQL_ATTR_INIT_COMMAND => 'SET NAMES `utf8`'); 

	try {
		$db = new PDO($dsn, $user, $password, $driver); //создаем новый объект класса PDO для взаимодействия с БД
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //Устанавливаем режим обработки ошибок ERRMODE_EXCEPTION
	} catch (PDOException $e) { 
		echo 'Подключение не удалось: '. $e->getCode() .'|'. $e->getMessage();    
		return false; 
	}
	return $db;
}

$dbh = connect_db();

$myprocessor = new EmailCountProcessor($dbh);

$names =array ('admin', 'user03');

$stmt = $dbh->prepare("SELECT * FROM `users` WHERE `name` = :name LIMIT 0 , 30");

$stmt->bindParam(':name', $names[0]);

if ( $stmt->execute() ) {
	while ($row = $stmt->fetch()) {
		print_r($row);
	}
}


?>