<?php

require_once __DIR__ . "/../../../../lib/autoload.php";
require_once __DIR__ . "/../../../../vendor/autoload.php";

use Lumen\Helpers\Validator\StringValidator as SV;
use PHPUnit\Framework\TestCase;


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
   }

}