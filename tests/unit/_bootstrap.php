<?php 

require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../src/autoload.php";

class TestCase extends PHPUnit\Framework\TestCase {

    public function setUp() {
        \PHuby\Config::set_config_root(__DIR__."/../config.d.testing");
    }

}