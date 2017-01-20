<?php

/*
 PHBjson v0.0.1
 Copyright 2016 Alberto Bettin
 Released under the MIT license
*/


class PHBjson implements ArrayAccess, Iterator, Countable {

    private $_obj;
    private $_is_valid;

    public function __construct(&$json_obj = '') {
        switch (gettype($json_obj)) {
            case 'array':
                $this->_is_valid = true;
                $this->_obj = &$json_obj;
                break;
            default :
                $parse = json_decode($json_obj, TRUE);
                
                if ($this->_is_valid = is_array($parse)) {
                    $this->_obj = $parse;
                } else {
                    $this->_obj = array();
                }
        }
        $this->rewind();
    }

    public function getValue($field) {
        if (!$this->isValidValue($field)) {
            return NULL;
        }
        if (isset($this->_obj[$field]) && is_array($this->_obj[$field])) {
            return new jsonField($this->_obj[$field]);
        }
        return $this->_obj[$field];
    }

    public function isValidValue($field) {
        return isset($this->_obj[$field]);
    }

    public function setValue($field, $value) {
        if (!$this->isValidValue($field)) {
            $this->_obj[$field] = array();
        }

        $this->_obj[$field] = $value;
    }

    public function deleteValue($field) {
        if ($this->isValidValue($field)) {
            unset($this->_obj[$field]);
        }
    }

    public function toObj() {
        return (object) $this->_obj;
    }

    public function toJSON() {
        $r_obj = (object) $this->_obj;
        return json_encode($r_obj);
    }

    public function isValid() {
        return $this->_is_valid;
    }

    public function updateStruct($strucure) {
        $array_struct = (array) $strucure;
        $this->_obj = array_merge_recursive($this->_obj, $array_struct);
    }

    private function &_get_recoursive($path, $fixMissing = true) {
        $null = NULL;
        $segments = explode(':', $path);
        $target = & $this->_obj;
        for ($i = 0; $i < count($segments); $i++) {
            if (!isset($target[$segments[$i]])) {
                if ($fixMissing === true) {
                    $target[$segments[$i]] = array();
                } else {
                    return $null;
                }
            }
            $target = & $target[$segments[$i]];
        }
        return $target;
    }

    // ArrayAccess interface implementation

    public function offsetSet($offset, $value) {
        $this->setValue($offset, $value);
    }

    public function offsetExists($offset) {
        return $this->isValidValue($offset);
    }

    public function offsetUnset($offset) {
        $this->deleteValue($offset);
    }

    public function offsetGet($offset) {
        return $this->getValue($offset);
    }
    
    // oblect access overwrite

    public function __set($field, $value) {
        $this->setValue($field, $value);
    }
    
    public function __get($name) {
        return $this->getValue($name);
    }
    
    // loop
    
    private $position = 0;
    
    public function rewind() {
        $this->position = count($this->_obj) > 0 ? array_keys($this->_obj)[0] : 0;
    }

    public function current() {
        return $this->getValue($this->position);
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        $keys = array_keys($this->_obj);
        $last_index = array_search($this->position, $keys);
        $this->position = ($last_index  +1 )< count($keys) ? $keys[$last_index + 1] : -1;
    }

    public function valid() {
        return $this->isValidValue($this->position);
    }
    
    //count
    public function count() {
        return count( $keys = array_keys($this->_obj));
    }

}
