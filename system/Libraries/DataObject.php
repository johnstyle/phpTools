<?php

namespace sJo\Libraries;

abstract class DataObject
{
    public function __get($name)
    {
        if(isset($this->{$name})) {
            return $this->{$name};
        }
    }

    public function __set($name, $value)
    {
        if(isset($this->{$name})) {
            $this->{$name} = $value;
        }
    }

    public function __isset($name)
    {
        return isset($this->{$name});
    }
}
