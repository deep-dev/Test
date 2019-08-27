<?php

class EmailCountProcessor
{
	private $dbh;			// PDO instance db connection
	
	private $stateFileName = 'example2.txt';	// Current state for processing items

	private $lockFileName = 'example2.pid';		// Lock file for single running

	private $logFileName = '';					// Log file for output data
    
	private $selectStatement;					// PDOStatement query for select data

	private $currentId = 0;						// Current max id user, already processed

	private $limit = 10;						// Size of processed data LIMIT

	private $domainsCount = array();			// Domains count
	
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }
	
	public function execute()
	{
		if (file_exists($this->lockFileName)) {
			$lock = @fopen($this->lockFileName, 'r+');
		} else {
			$lock = @fopen($this->lockFileName, 'a+');
        }
        if (!$lock) {
            throw new \RuntimeException('Failed to open lock file "'.$this->lockFileName.'"');
        }
        if (!flock($lock, LOCK_EX | LOCK_NB)) {
            throw new \RuntimeException('Failed to lock file "'.$this->lockFileName.'"');
        }
        fwrite($lock, getmypid());
        fflush($lock);
		
		try {
			$this->loadCurrentState();
			echo('Getting data from table users with id: ' . $this->currentId);
			
			do {
				$stmt = $this->getSelectStatement($this->currentId);
				if ($stmt->execute()) {
                    $count = $stmt->rowCount();
                    echo($count . ' records found');
                    if ($count > 0) {
                        $domains = $this->fetchEmail($stmt);
                        $stmt->closeCursor();
                        echo(count($domains) . ' domains found');
                        $this->updateCount($domains);
                    }
                } else {
                    throw new \RuntimeException('Failed to execute query '.$stmt->queryString.': '.$stmt->errorInfo());
                }
            } while ($count > 0);
		} catch (\Throwable $e) {
            echo('Exception: ' . $e->getMessage());
            echo('Exception trace: ' . PHP_EOL . $e->getTraceAsString());
		}

		flock($lock, LOCK_UN);
		fclose($lock);
		unlink($this->lockFileName);

	}
	
    protected function getSelectStatement($currentId)
    {
        if ($this->selectStatement === null) {
            $sql = 'SELECT `id`, `email` FROM `users` WHERE `id` > :currentId ORDER BY `id` ASC LIMIT :limit';
            $this->selectStatement = $this->pdo->prepare($sql);
        }
        $this->selectStatement->bindValue(':currentId', $currentId, \PDO::PARAM_INT);
        $this->selectStatement->bindValue(':limit', $this->limit, \PDO::PARAM_INT);
        return $this->selectStatement;
    }
	
	/* Processed email domains fetch */
    protected function fetchEmail(\PDOStatement $stmt)
    {
        $domains = array();
		
        while (($row = $stmt->fetch(\PDO::FETCH_NUM)) !== false) {
            $this->currentId = (int) $row[0];
            if (empty($row[1])) {
                continue;
            }
			
            $emails = explode(',', trim($row[1]));
            
            foreach ($emails as $email) {
                $parts = explode('@', $email, 2);
                if (count($parts) != 2 || trim($parts[0]) == '') {
                    echo('Invalid user email "'.$email.'", user id: ' . $row[0]);
                    continue;
                }
				
                $domain = trim($parts[1]);
				
                if (empty($domain)) {
                    echo('Invalid user email "'.$email.'", user id: ' . $row[0]);
                    continue;
                }

				if (!array_key_exists($domain, $domains)) {
					$domains[$domain] = 1;
				} else {
					$domains[$domain] += 1;
				}
            }
        }
		echo '<br><pre>';
		print_r($domains);
		echo '<br></pre>';
        return $domains;
    }
	
	protected function updateCount(array $counters)
	{
		if (!empty($counters)) {
			
			foreach ($counters as $domain => $count) {
                    if (array_key_exists($domain, $this->domainsCount)) {
                        $this->domainsCount[$domain] += $count;
                    } else {
                        $this->domainsCount[$domain] = $count;
                    }
			}
		}
        $this->saveCurrentState();
	}
	
	/*	Clear previous working history */
	public function clearPreviousState()
	{
		if (file_exists($this->stateFileName)) {
			unlink($this->stateFileName);
		}
		return $this;
	}

	/* Load saved state from file */
	protected function loadCurrentState()
    {
		if (!file_exists($this->stateFileName)) {
			return;
		}
		$fd = fopen($this->stateFileName, 'r');
		$data = fread($fd, 1024);
		fclose($fd);
		$tmp = json_decode($data, true);
		$this->currentId = $tmp['currentId'];
		if (!empty($tmp['counters']) && is_array($tmp['counters'])) {
			$this->domainsCount = $tmp['counters'];
		}
	}

	/* Save working state to file */
    protected function saveCurrentState()
    {
		$my_json = array(
			'currentId'    => $this->currentId,
			'counters' => $this->domainsCount,
		);

		if (file_exists($this->stateFileName)) {
			$fd = fopen($this->stateFileName, 'a+');
		} else {
			$fd = fopen($this->stateFileName, 'w');
		}
		if (!$fd) {
			throw new \RuntimeException('Failed to open state file "'.$this->stateFileName.'"');
		}
		flock($fd, LOCK_EX);
		ftruncate($fd, 0);
		fseek($fd, 0, SEEK_SET);
		fwrite($fd, json_encode($my_json));
		fflush($fd);
		flock($fd, LOCK_UN);
		fclose($fd);
	}
}

/* Connecting to db */
function connect_db() {
	$dsn = 'mysql:dbname=example1;host=localhost';
	$user = 'root';
	$password = '';
	$driver = array(PDO :: MYSQL_ATTR_INIT_COMMAND => 'SET NAMES `utf8`'); 

	try {
		$db = new PDO($dsn, $user, $password, $driver);					//create new PDO object for connecting db
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	// Set error processing mode to ERRMODE_EXCEPTION
	} catch (PDOException $e) { 
		echo 'Error connection: '. $e->getCode() .'|'. $e->getMessage();    
		return false; 
	}
	return $db;
}

$dbh = connect_db();

$myprocessor = new EmailCountProcessor($dbh);

$myprocessor->execute();

?>