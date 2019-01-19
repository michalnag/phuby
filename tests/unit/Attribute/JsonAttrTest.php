<?php

use PHuby\Attribute\JsonAttr;
use PHuby\Error\InvalidArgumentError;

class JsonAttrTest extends TestCase {

  public function setUp() {
    $this->attr = new JsonAttr();
    $this->arr_1 = [
      'test' => 1,
      'aa' => [
        'bb' => 'cc',
        'dd' => 21
      ]
    ];
    $this->str_1 = json_encode($this->arr_1);
  }

  public function test_instance() {
    $this->assertInstanceOf('PHuby\Attribute\JsonAttr', $this->attr);
  }

  public function test_set() {

    foreach ([1, false, 'asd', '{aaaa}'] as $val) {
      $exception = false;
      try {
        $this->attr->set($val);        
      } catch (InvalidArgumentError $e) {
        $exception = true;
      }
      $this->assertTrue($exception);
    }

    foreach ([$this->arr_1, $this->str_1] as $value) {
      $this->attr->set($value);
      $this->assertTrue(is_array($this->attr->get()));
      $this->assertEquals($this->attr->get(), $this->arr_1);
    }

  }
  
  public function test_get() {}

  public function test_to_db_format() {
    $this->attr->set($this->arr_1);
    $this->assertEquals(json_encode($this->arr_1), $this->attr->to_db_format());
  }

  public function test_toString() {}

  public function test_key_exist() {
    $this->attr->set($this->arr_1);
    $this->assertEquals($this->attr->get_by_key('aa:bb'), 'cc');
    $this->assertEquals($this->attr->get_by_key('aa:bb21'), null);
  }

  public function test_add_data() {
    $this->attr->set($this->arr_1);
    $this->attr->add_data('aa:dd', 24);
    $this->assertEquals($this->attr->get_by_key('aa:dd'), 24);
  }

}