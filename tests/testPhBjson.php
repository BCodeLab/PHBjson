<?php

use PHPUnit\Framework\TestCase;

require_once '../PHBjson.php';

class StackTest extends TestCase {

    /**
     * 
     */
    public function test_main() {
        $start_obj = new stdClass();
        $start_obj->attr1 = 1;
        $start_obj->attr2 = 3;
        $start_obj->attr3 = 5;
        $json = json_encode($start_obj);

        $testObj = new PHBjson($json);

        foreach ($start_obj as $key => $value) {
            $this->assertEquals($value, $testObj->{$key});
        }
    }

    public function test_isValidValue() {
        $start_obj = new stdClass();
        $start_obj->attr1 = 1;
        $start_obj->attr2 = 3;
        $start_obj->attr3 = 5;

        $start_sub_obj = new stdClass();
        $start_sub_obj->subattr2 = 3;
        $start_sub_obj->subattr1 = 6;
        
        $start_obj->attr4 = $start_sub_obj;
        
        $json = json_encode($start_obj);

        $testObj = new PHBjson($json);

        foreach ($start_obj as $key => $value) {
            $this->assertTrue($testObj->isValidValue($key));
        }
        
        foreach ($start_obj->attr4 as $key => $value) {
            $this->assertTrue($testObj->attr4->isValidValue($key));
        }

        $this->assertFalse($testObj->isValidValue($testObj->fooAttribute));
        $this->assertFalse($testObj->isValidValue($testObj->attr));
    }

}
