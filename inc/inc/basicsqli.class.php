<?php
declare(strict_types=1);

/**
 *@author : Sergio Soares 2016 (PHP 7 only!)
* BasicSQLi Database Connection Class
* @access public
*/
class BasicSQLi {
    /**
    * MySQL server hostname
    * @access protected
    * @var string
    */
    protected $host;

    /**
    * MySQL username
    * @access protected
    * @var string
    */
    protected $user;

    /**
    * MySQL user's password
    * @access protected
    * @var string
    */
    protected $pass;

    /**
    * MySQL Name of database to use
    * @access protected
    * @var string
    */
    protected $name;

    /**
    * MySQL Resource link identifier stored here
    * @access private
    * @var string
    */
    private $con;

    /**
    * MySQL Stores error messages for connection errors
    * @access private
    * @var string
    */
    private $connectError;

    /**
    * BasicSQLi constructor
    * @param string host (MySQL server hostname)
    * @param string User (MySQL User Name)
    * @param string Pass (MySQL User Password)
    * @param string Name (Database to select)
    * @access public
    */
    public function __construct($host,$user,$pass,$name)
    {
        $this->host=$host;
        $this->user=$user;
        $this->pass=$pass;
        $this->name=$name;
        $this->DBconnect();
    }

    /**
    * Establishes connection to MySQL server and selects a database
    * @return void
    * @access private
    */
    private function DBconnect()
    {
        // Make connection to MySQL server
    	try
    	{
    		$this->con = mysqli_connect($this->host,
              $this->user, $this->pass, $this->name);
    	}
    	catch (Error $e)
    	{
    		trigger_error ('Could not connect to server: '.$e->getMessage());
    		$this->connectError=true;
    	}
            
    }
    
    /**
    * Checks for errors
    * @return boolean
    * @access public
    */
    public function isError() : bool
    {
        if ( $this->connectError )
            return true;
        $error=mysqli_error ($this->con);
        if ( empty ($error) )
            return false;
        else
            return true;
    }

    /**
    * Returns an instance of a result class to fetch rows with
    * @param $sql string the database query to run
    * @return result
    * @access public
    */
    public function query($sql)
    {
    	
    	try
    	{
    		$queryRess=mysqli_query($this->con,$sql);
    	}
    	catch (Error $e)
    	{
    		trigger_error ('Query failed: '.$e->getMessage().' SQL: '.$sql);
    	}
        	
    	if (!$this->isError()) {
    		
        	return new class ($this,$queryRess) {
        		
        		/**
        		 * Instance of a result class providing database connection
        		 * @access private
        		 * @var BasicSQLi
        		 */
        		private $bsqli;
        		
        		/**
        		 * Query resource
        		 * @access private
        		 * @var resource
        		 */
        		private $query;
        		
        		/**
        		 * mySQL result constructor
        		 * @param object mysql   (instance of BasicSQLi class)
        		 * @param resource query (BasicSQLi query resource)
        		 * @access public
        		 */
        		public function __construct(& $bsqli,$query)
        		{
        			$this->bsqli=& $bsqli;
        			$this->query= $query;
        		}
        		
        		/**
        		 * Fetches a row from the result
        		 * @return array
        		 * @access public
        		 */
        		public function fetch()
        		{
        		if ( $row=mysqli_fetch_array($this->query, MYSQLI_ASSOC) ) {
        				return $row;
        			} else if ( $this->size() > 0 ) {
        				mysqli_data_seek($this->query,0);
        				return false;
        			} else {
        				return false;
        			}
        		}
        		
        		/**
        		 * Returns the number of rows selected
        		 * @return int
        		 * @access public
        		 */
        		public function size() : int
        		{
        			$size = mysqli_num_rows($this->query);
        			return $size;
        		}
        		
        		/**
        		 * Checks for BasicSQLi errors
        		 * @return boolean
        		 * @access public
        		 */
        		public function isError() : bool
        		{
        			return $this->bsqli->isError();
        		}
        		
        	};
        
    	}
        // Free result set
        mysqli_free_result($queryRess);
        mysqli_close($this->con);
    }
}

?>