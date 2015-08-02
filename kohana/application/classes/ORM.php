<?php defined('SYSPATH') OR die('No direct script access.');

class ORM extends Kohana_ORM {

    public function get($column)
    {
        $method = 'get_' . $column;

        if (method_exists($this, $method))
        {
            $params = func_get_args();

            array_shift($params);

            return call_user_func_array(array($this, $method), $params);
        }

        return parent::get($column);
    }

    public function set($column, $value)
    {
        $method = 'set_' . $column;

        if (method_exists($this, $method))
        {
            $params = func_get_args();

            array_shift($params);

            return call_user_func_array(array($this, $method), $params);
        }

        return parent::set($column, $value);
    }
}

