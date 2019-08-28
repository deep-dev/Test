<?php

class File
{
    private static $instances = array();
	
    private $handle;                     // open file handle
    private $strings = array();          // count of symbols in strings
    private $count;                      // count of strings in file
	
    public function __construct($path)
    {
        $this->count = 0;
        $this->handle = fopen($path, "r");
        
		while (fgets($this->handle) !== false) {
            $this->strings[] = ftell($this->handle);
            ++$this->count;
        }
    }
	
    public function __destruct()
    {
        $this->close();
    }
	
    /**
     * Represents a getInstance
     * @param string $path - file name for processed
     */
    public static function getInstance($path)
    {
        if (!array_key_exists($path, self::$instances)) {
            self::$instances[$path] = new self($path);
        }
        return self::$instances[$path];
    }

    /**
     * Close file and reset to 0 all parameters
     */
    public function close()
    {
        if ($this->handle) {
            flock($this->handle, LOCK_UN);
            fclose($this->handle);
            $this->handle = null;
            $this->count = 0;
        }
    }
	
    /**
     * Check file descriptor return bool True if closed, false if is open
     */	  
    public function isClosed()
    {
        return (bool) $this->file === null;
    }
	
    public function getCount() {
        return ($this->isClosed()) ?  false : $this->count;
    }
	
    public function getString($index) {
        if ($index >= $this->getCount()) {
            //throw new \Exception('Not');
        }
        if ($index === 0){
            fseek($this->handle, 0);
        } else {
            fseek($this->handle, $this->strings[$index - 1]);
        }
        return rtrim(fgets($this->handle), PHP_EOL);
    }
}

class FileIterator implements SeekableIterator
{
    private $currentOffset;
    private $file;
	
    public function __construct( $path)
    {
        $this->file = File::getInstance($path);
    }
    public function seek($currentOffset)
    {
        if ($currentOffset < 0 || $currentOffset >= $this->file->getCount()) {
            //throw new \OutOfBoundsException("invalid seek currentOffset ($currentOffset)");
        }
        $this->currentOffset = $currentOffset;
    }
	
    /**
     * Rewind to start position in file
     */
    public function rewind()
    {
        $this->currentOffset = 0;
    }
	
    /**
     * Getting string with current position
     */
    public function current()
    {
        return $this->file->getString($this->currentOffset);
    }

    /**
     * return current offset
     */
    public function key()
    {
        return $this->currentOffset;
    }
	
    /**
     * Rewind to next position in file
     */
    public function next()
    {
        ++$this->currentOffset;
    }

    /**
     * Validate position in file
     */
    public function valid()
    {
        return $this->currentOffset < $this->file->getCount();
    }
}

$myFile = 'example3.txt';

try {
    $fileIterator = new FileIterator( __DIR__ . '\\' . $myFile );
    
	$fileIterator->rewind();
    $fileIterator->next();
    $fileIterator->next();
	
	echo 'String: '. $fileIterator->key() ."<br>\n";
	echo $fileIterator->current(), "<br>\n";
	
	
    $fileIterator->seek(9);
	echo 'String: '. $fileIterator->key() ."<br>\n";
    echo $fileIterator->current(), "<br>\n";

    
	echo 'Current string: '. $fileIterator->key() ."<br>\n";
	echo $fileIterator->key();

    //echo $fileIterator->current();
    /*
    foreach ($fileIterator as $char) {
        echo $char;
    }
    */
/*
$fileIterator->seek(3);
$fileIterator->next();
echo $fileIterator->current();
$fileIterator->next();
echo $fileIterator->current();
$fileIterator->seek(9);
$fileIterator->next();
$fileIterator->next();
*/

// $fileIterator->valid();

} catch (OutOfBoundsException $e) {
    echo $e->getMessage();
}

?>