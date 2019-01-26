<?php


use PHuby\Helpers\Validator\StringValidator as SV;


class StringValidatorTest extends TestCase {

   function test_is_valid() {
      $string = "random string";
      $this->assertTrue(SV::is_valid($string));
      
      $string = 123;
      $this->assertFalse(SV::is_valid($string));

      $string = new stdClass();
      $this->assertFalse(SV::is_valid($string));
   }

   function test_is_valid_length() {
      $string = "aaaaaaaaaa";
      $this->assertTrue(SV::is_valid($string, 
         ['length' => ['exact' => 10]]
      ));

      $this->assertTrue(SV::is_valid($string, 
         ['length' => ['min' => 10]]
      ));

      $this->assertTrue(SV::is_valid($string, 
         ['length' => ['max' => 10]]
      ));

      $this->assertTrue(SV::is_valid($string, 
         ['length' => ['min' => 9, "max" => 11]]
      ));

      $this->assertTrue(SV::is_valid($string, 
         ['allow_null' => true]
      ));


   }



}