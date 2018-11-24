<?php 

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/src/autoload.php";
require_once __DIR__ . "/PHuby/Attribute/AttributeTestInterface.php";

PHuby\Config::set_config_root(__DIR__."/../config.d");

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class TestCase extends PHPUnitTestCase {}