<?php

require_once __DIR__ . "/../../lib/autoload.php";
require_once __DIR__ . "/../../vendor/autoload.php";

use App\Model\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase {

  public function __construct() {
    $this->user = new User();
  }

  public function test_assign_attributes() {

    $raw_data = [
      "id" => 12,
      "uuid" => "asdwq21e",
      "email" => "test@gmail.com",
      "password" => "\$2y\$10\$To1mEE22Aomjw3gmvTkvB.RA2.x72kb4R7lGocnZ3uEMufvRTev4i",
      "activation_token" => "plokijuhplokijuhplokijuhplokijuh",
      "password_reset_token" => "plokijuhplokijuhplokijuhplokijuh"
    ];

    $this->user->poulate_attributes($raw_data);

    $this->assertInstanceOf("Attribute\IDAttr", $this->user->id);
    $this->assertEquals($raw_data['id'], $this->user->id->to_int());

    $this->assertInstanceOf("Attribute\UUIDAttr", $this->user->uuid);
    $this->assertEquals($raw_data['uuid'], $this->user->uuid->__toString());
    $this->assertEquals(8, strlen($this->user->uuid->__toString()));

    $this->assertInstanceOf("Attribute\StringAttr", $this->user->email);
    $this->assertEquals($raw_data['email'], $this->user->email->__toString());

    $this->assertInstanceOf("Attribute\PasswordAttr", $this->user->password);
    $this->assertEquals($raw_data['password'], $this->user->password->__toString());

    $this->assertInstanceOf("Attribute\TokenAttr", $this->user->activation_token);
    $this->assertEquals($raw_data['activation_token'], $this->user->activation_token->__toString());

    $this->assertInstanceOf("Attribute\TokenAttr", $this->user->password_reset_token);
    $this->assertEquals($raw_data['password_reset_token'], $this->user->password_reset_token->__toString());

    try {
      $this->user->poulate_attributes(["uuid" => "qweasdz"]);
    } catch(Error\InvalidAttributeError $e) {
      $this->assertTrue(true);
    }
  }

}