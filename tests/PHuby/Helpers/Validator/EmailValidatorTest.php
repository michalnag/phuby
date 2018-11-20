<?php

use PHuby\Helpers\Validator\EmailValidator as EV;


class EmailValidatorTest extends TestCase {

   function test_is_valid() {
      $email = "test@test.com";
      $this->assertTrue(EV::is_valid($email));
      
      $email = "balls@";
      $this->assertFalse(EV::is_valid($email));

      $email = 123;
      $this->assertFalse(EV::is_valid($email));

      $email = new stdClass();
      $this->assertFalse(EV::is_valid($email));
   }

}