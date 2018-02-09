<?php

/**
 * Short description for file
 * 
 * Long description (if any) ...
 * 
 * PHP version 5
 * 
 * All rights reserved.
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 * + Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 * + Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation and/or
 * other materials provided with the distribution.
 * + Neither the name of the <ORGANIZATION> nor the names of its contributors
 * may be used to endorse or promote products derived
 * from this software without specific prior written permission.
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 * @category  CategoryName
 * @package   Model
 * @author    Author's name <author@mail.com>
 * @copyright 2018 Author's name
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version   CVS: $Id:$
 * @link      http://pear.php.net/package/Model
 * @see       References to other sections (if any)...
 */
namespace Momo;

use MongoDB\Client;

/**
 * Short description for class
 * 
 * Long description (if any) ...
 * 
 * @category  CategoryName
 * @package   Model
 * @author    Author's name <author@mail.com>
 * @copyright 2018 Author's name
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/Model
 * @see       References to other sections (if any)...
 */
class Model
{

    /**
     * Description for public
     * @var array 
     * @access public
     */
    public $fields = array();

    /**
     * Description for private
     * @var unknown
     * @access private
     */
    private $client;

    /**
     * Description for protected
     * @var object   
     * @access protected
     */
    protected $collection = null;

    /**
     * Description for public
     * @var string
     * @access public
     * @static
     */
    public static $username = '';

    /**
     * Description for public
     * @var string
     * @access public
     * @static
     */
    public static $password = '';

    /**
     * Description for public
     * @var string
     * @access public
     * @static
     */
    public static $host = '';

    /**
     * Description for public
     * @var string
     * @access public
     * @static
     */
    public static $database = '';
     

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param unknown $username Parameter description (if any) ...
     * @param unknown $p        Parameter description (if any) ...
     * @param unknown $h        Parameter description (if any) ...
     * @param unknown $db       Parameter description (if any) ...
     * @return void   
     * @access public 
     * @static
     */
    public static function database($u, $p, $h, $db)
    {
        self::$username = $u;
        self::$password = $p;
        self::$host = $h;
        self::$database = $db;
    }

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param unknown $name  Parameter description (if any) ...
     * @param unknown $value Parameter description (if any) ...
     * @return void   
     * @access public 
     */
    public function __set($name, $value)
    {
        $this->fields[$name] = $value;
    }

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param unknown $name Parameter description (if any) ...
     * @return array   Return description (if any) ...
     * @access public 
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->fields)) {
            return $this->fields[$name];
        }
    }

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return void  
     * @access public
     */
    public function __construct()
    {
        $context    = stream_context_create(array(
            "ssl" => array(
                "peer_name" => "127.0.0.1",
                "verify_peer" => false,
                "verify_peer_name" => false,
                "allow_self_signed" => true
            )
        ));
        $client     = new Client('mongodb://'. self::$username . ':' . self::$password . '@' . self::$host . '/admin?ssl=true', array(
            'ssl' => true
        ), array(
            'context' => $context
        ));
        $this->collection = $client->selectCollection(self::$database, get_class($this));
    }
    
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param unknown $name Parameter description (if any) ...
     * @param unknown $args Parameter description (if any) ...
     * @return mixed   Return description (if any) ...
     * @access public 
     */
    public function __call($name, $args)
    {
        return call_user_func_array(array($this->collection, $name), $args);
    }

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param unknown $name Parameter description (if any) ...
     * @param unknown $args Parameter description (if any) ...
     * @return mixed   Return description (if any) ...
     * @access public 
     * @static
     */
    public static function __callStatic($name, $args)
    {
        $me = get_called_class();
        $momo_instance = new $me;
        return call_user_func_array(array($momo_instance, $name), $args);
    }

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param unknown $arr Parameter description (if any) ...
     * @return object  Return description (if any) ...
     * @access public 
     */
    public function save($arr = null)
    {
        if (empty($arr)) {
            //print_r($this->fields);
            $result = $this->collection->insertOne($this->fields);
        } else {
            $result = $this->collection->insertOne($arr);
        }
        $this->findOne(['_id' => $result->getInsertedId()]);
        return $this;
    }

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param array   $filter  Parameter description (if any) ...
     * @param array   $options Parameter description (if any) ...
     * @return mixed   Return description (if any) ...
     * @access private
     */
    private function findOne($filter = array(), $options=array())
    {
        $this->fields = $this->collection->findOne($filter, $options);
        return $this;
    }

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param array   $filter      Parameter description (if any) ...
     * @param array   $replacement Parameter description (if any) ...
     * @param array   $options     Parameter description (if any) ...
     * @return mixed   Return description (if any) ...
     * @access private
     */
    private function findOneAndReplace($filter = array(), $replacement = array(), $options=array())
    {
        $this->fields = $this->collection->findOneAndReplace($filter, $replacement, $options);
        return $this;
    }

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param array   $options Parameter description (if any) ...
     * @return void   
     * @access private
     */
    private function withOptions($options = array())
    {
        $this->collection = $this->collection->withOptions($options);
    }
    
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param array   $filter  Parameter description (if any) ...
     * @param array   $options Parameter description (if any) ...
     * @return array   Return description (if any) ...
     * @access private
     */
    private function find($filter = array(), $options = array())
    {
        $cursor = $this->collection->find($filter, $options);
        $model_arr = array();
        foreach ($cursor as $object) {
            $me = get_called_class();
            $momo_instance = new $me;
            $momo_instance->fields = (array) $object;
            $model_arr[] = $momo_instance;
        }
        return $model_arr;
    }
}
