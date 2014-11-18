<?php
/**
 * class RequestFactory
 */
// PHP Pattern - Creational / Simple Factory
class RequestFactory
{
	public function __construct()
    {
        if(isset($_GET)):
        	GetRequest::params($_GET);
    	endif;

    	if(isset($_POST)):
        	PostRequest::params($_POST);
    	endif;
    }
}

/**
 * RequestInterface is a handle of params
 */
interface RequestInterface
{
    /**
     * @param array $params
     */
    public static function params($params);
}

/**
 * $_GET is a get request
 */
abstract class GetRequest implements RequestInterface
{
    /**
     * @param array $params
     */
    public static function params($params)
    {
    	foreach($params as $key => $value):
    		RequestRegistry::set($key,$value);
    	endforeach;
    }
}

/**
 * $_POST is a post request
 */
abstract class PostRequest implements RequestInterface
{
    /**
     * @param array $params
     */
    public static function params($params)
    {
    	foreach($params as $key => $value):
    		RequestRegistry::set($key,$value);
    	endforeach;
    }
}

/**
 * class Registry
 */
// PHP Pattern - Structural / Registry
abstract class RequestRegistry
{
    protected static $storedValues = array();

    public static function set($key, $value)
    {
        self::$storedValues[$key] = $value;
    }

    public static function get($key = null)
    {
        $stored = (is_null($key)) ? self::$storedValues : self::$storedValues[$key];
        return $stored;
    }
}

$request = new RequestFactory();
header('Content-Type: application/json');
echo json_encode(RequestRegistry::get());

?>