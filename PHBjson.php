<?php

/*
  PHBjson v0.0.1
  Copyright 2016 Alberto Bettin
  Released under the MIT license
 */

class PHBjson implements ArrayAccess, Iterator, Countable {

    private $_obj;
    private $_is_valid;
    private $position = 0;
    private $FOO_NULL_VALUE = NULL;

    public function __construct($json_obj = '') {
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

    /**
     * Get a value
     * @param string $name the field name
     * @return the value
     */
    public function &getValue($field) {
        if (!$this->isValidValue($field)) {
            return $this->FOO_NULL_VALUE;
        }
        if (isset($this->_obj[$field]) && is_array($this->_obj[$field])) {
            $obj = new PHBjson();
            $obj->__setInternalObject($this->_obj[$field]);
            return $obj;
        }
        $el = $this->_obj[$field];

        return $el;
    }

    /**
     * Check if field exists
     * @param string $field the field name
     * @return bool true if present, false otherwise
     */
    public function isValidValue($field) {
        return isset($this->_obj[$field]);
    }

    /**
     * Set a value
     * @param string $field the field name
     * @param mixed $value the field value
     */
    public function setValue($field, $value) {
        if (!$this->isValidValue($field)) {
            $this->_obj[$field] = array();
        }

        $this->_obj[$field] = $value;
    }

    /**
     * Removes a value
     * @param string $field the field name
     */
    public function deleteValue($field) {
        if ($this->isValidValue($field)) {
            unset($this->_obj[$field]);
        }
    }

    /**
     * Return the object contained
     * @return stdClass the object
     */
    public function toObj() {
        return (object) $this->_obj;
    }

    /**
     * REturn the stringed (json) object
     * @return string json string
     */
    public function toJSON() {
        $r_obj = (object) $this->_obj;
        return json_encode($r_obj);
    }

    /**
     * Return true if contains a valid object
     * @return bool true if valid, false otherwise
     */
    public function isValid() {
        return $this->_is_valid;
    }

    /**
     * Updates the structure mergin the current with the one given ad parameter
     * @param stdClass $strucure the strucure to merge in
     */
    public function updateStruct($strucure) {
        $array_struct = (array) $strucure;
        $this->_obj = array_merge_recursive($this->_obj, $array_struct);
    }

    /**
     * 
     * @param type $path
     * @param type $fixMissing
     * @return type
     */
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

    public function __setInternalObject(&$obj) {
        $this->_is_valid = true;
        $this->_obj = &$obj;
        $this->rewind();
    }

    /**
     * -------------------------------------------------------------------------
     * Implementing direct access to fields (get + set) 
     * -------------------------------------------------------------------------
     */

    /**
     * Set a values
     * @param string $field the field name
     * @param mixed $value the value to set
     */
    public function __set($field, $value) {
        $this->setValue($field, $value);
    }

    /**
     * Get a value
     * @param string $name the field name
     */
    public function &__get($name) {
        return $this->getValue($name);
    }

    /**
     * -------------------------------------------------------------------------
     * Implementing iterable interface 
     * -------------------------------------------------------------------------
     */

    /**
     *
     * @var type 
     */
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
        $this->position = ($last_index + 1 ) < count($keys) ? $keys[$last_index + 1] : -1;
    }

    public function valid() {
        return $this->isValidValue($this->position);
    }

    /**
     * -------------------------------------------------------------------------
     * Implementing Countable
     * -------------------------------------------------------------------------
     */

    /**
     * 
     * @return number the number of fieds
     */
    public function count() {
        return count($keys = array_keys($this->_obj));
    }

}
