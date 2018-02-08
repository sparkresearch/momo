<?php

namespace Momo;
use MongoDB\Client;

class Model
{
    
    private $fields = array();
    private $client;
    protected $coll = NULL;
    public static $username = '';
    public static $password = '';
    public static $host = '';
     public static $database = '';     
     
     public static function client($u, $p, $h, $db)
     {
     	self::$username = $u;
     	self::$password = $p;
     	self::$host = $h;
     	self::$database = $db;
     	
     
     }
    public function __set($name, $value)
    {
        $this->fields[$name] = $value;
        echo $name . '\r\n';
    }

    public function __get($name)
    {
        
        if (array_key_exists($name, $this->fields)) {
            return $this->fields[$name];
        }
    }
    function __construct()
    {
        $context    = stream_context_create(array(
            "ssl" => array(
                "peer_name" => "127.0.0.1",
                "verify_peer" => false,
                "verify_peer_name" => false,
                "allow_self_signed" => true
            )
        ));
        $client     = new MongoDB\Client('mongodb://'. self::$username . ':' . self::$password . '@' . self::$host . '/admin?ssl=true', array(
            'ssl' => true
        ), array(
            'context' => $context
        ));
        $this->coll = $client->selectCollection(self::$database, get_class($this));        
        
    }
    
    public function __call($name, $args)
    {

        call_user_func_array(array($this->coll, $name),$args);
    }

    public static function __callStatic($name, $args)
    {
		$me = get_called_class();
		$momo_instance = new $me;
		return call_user_func_array(array($momo_instance, $name),$args);
    }

    function save($arr = null)
    {
    
    	if (empty($arr))
    	{
    	//print_r($this->fields);
    		$this->coll->insertOne($this->fields);
    	}
    	else
    	{
    		$this->coll->insertOne($arr);
    	}
    }

    private function findOne($filter = array(), $options=array())
    {
    		
    	
    	$this->fields = $this->coll->findOne($filter, $options);
    	return $this;
    }
}

?>
