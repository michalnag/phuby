<?php

require_once __DIR__ . "/../lib/autoload.php";
require_once __DIR__ . "/../vendor/autoload.php";

use App\DBI;
use PHPUnit\Framework\TestCase;
use Config;

class DBITest extends TestCase {

  public function test_connect_to_db() {
    $this->assertTrue(DBI::connect_to_db(Config::get_data("db:test")));
    $this->assertTrue(DBI::disconnect_from_db());
  }

}