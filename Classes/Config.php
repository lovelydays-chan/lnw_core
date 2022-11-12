<?php

namespace Lnw\Core;

class Config
{

    /**
     * All of the items from the config file that is loaded
     *
     * @var array
     */
    public static $items = array();

    /**
     * Loads the config file specified and sets $items to the array
     *
     * @param   string  $filepath
     * @return  void
     */
    public static function load($filepath)
    {
        static::$items = include "./config/$filepath.php";
    }

    /**
     * Searches the $items array and returns the item
     *
     * @param   string  $item
     * @return  string
     */
    public static function get($key = null)
    {
        if (!$key) {
            return static::$items;
        }

        $input = explode('.', $key);
        $filepath = array_shift($input);

        static::load($filepath);
        return self::getValue($input, static::$items);
    }

    private static function getValue($index, $value)
    {
        if (
            is_array($index) &&
            count($index)
        ) {
            $current_index = array_shift($index);
        }
        if (
            is_array($index) &&
            count($index) &&
            is_array($value[$current_index]) &&
            count($value[$current_index])
        ) {
            return self::getValue($index, $value[$current_index]);
        } else {
            return $value[$current_index];
        }
    }
}
