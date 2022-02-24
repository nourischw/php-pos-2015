<?php

class BaseModel extends Eloquent
{

    /**
     * @var $_params array The container of the params
     */
    private static $_params = array();

    function __construct()
    {
        
    }

    /**
     * 	Set params container key and value
     * 	@param string $key The key name
	 *	@param mixed $value The key value
     * 	@return void
     */
    public static function set($key, $value)
    {
        self::$_params[$key] = $value;
    }

    /**
     * 	Set params container by array
	 *	@param array $array The array which stores the params key and value
     * 	@return void
     */
    public function set_multi($array)
    {
        self::$_params = array_merge(self::$_params, $array);
    }

    /**
     * 	Get params value
	 *	@param string $key The key name
     * 	@return mixed The value of selected params key
     */
    public static function get($key)
    {
        if (isset(self::$_params[$key])) {
            return self::$_params[$key];
        }
        return null;
    }

    /**
     * 	Get all params value
     * 	@return mixed All params values
     */
    public static function get_all()
    {
        return self::$_params;
    }

    /**
     * 	Get selected params keys' value
     * 	@return mixed The selected keys' value
     */
    public static function get_multi($keys)
    {
        foreach ($keys as $key) {
            $values[] = self::$_params[$key];
        }
        return $values;
    }

    /**
     * 	Escape string for prevent SQL injection
	 *	@param string $string The query item value
     * 	@return string The escaped string
     */
    public static function escape($string)
    {
        return DB::getPdo()->quote(strip_tags($string));
    }

}
