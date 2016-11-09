<?php

require_once __DIR__ . "/../../lib/autoload.php";
require_once __DIR__ . "/../../vendor/autoload.php";

use App\Model\User;
use App\Process\UserProcess;
use PHPUnit\Framework\TestCase;
use App\DBI;

class UserTest extends TestCase {

  public function __construct() {
    $this->user_process = new UserProcess();
    DBI::connect_to_db(Config::get_data("db:test"));
  }

  public function test_signup() {

    $raw_data = [
      "uuid" => "asdwq21e",
      "email" => "test@gmail.com",
      "password" => '$2y$10$To1mEE22Aomjw3gmvTkvB.RA2.x72kb4R7lGocnZ3uEMufvRTev4i',
      "activation_token" => "plokijuhplokijuhplokijuhplokijuh"
    ]; 

    $user = new User();
    $user->poulate_attributes($raw_data);

    $this->assertTrue($this->user_process->signup($user));
    $this->assertInstanceOf("Attribute\IDAttr", $user->id);
    $this->assertEquals(1, $user->id->to_int());
    $this->assertFalse($this->user_process->signup($user));

  }

}