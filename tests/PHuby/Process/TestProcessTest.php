<?php

require_once __DIR__ . "/../../../lib/autoload.php";
require_once __DIR__ . "/../../../vendor/autoload.php";
require_once __DIR__ . "/../../../tests/lib/autoload.php";

use PHPUnit\Framework\TestCase;
use Process\TestProcess;
use PHuby\Config;
use PHuby\Helpers\Utils\FileUtils;

class TestProcessTest extends TestCase {

  public function __construct() {
    $this->obj_process = new TestProcess();
  }

  public function test_instantiation() {
    $this->assertEquals($this->obj_process->get_status(), TestProcess::NOT_STARTED);
    $this->assertEquals(null, $this->obj_process->get_errors());
    $this->assertEquals(null, $this->obj_process->get_warnings());
    $this->assertFalse($this->obj_process->has_completed());
  }

  public function test_is_status_allowed() {
    foreach([
        TestProcess::NOT_STARTED,
        TestProcess::COMPLETE,
        TestProcess::COMPLETE_WITH_WARNINGS,
        TestProcess::FAIL,
        TestProcess::PENDING
      ] as $status) {
      $this->assertTrue($this->obj_process->set_status($status));
    }

    foreach([
        12, 13, "test"
      ] as $status) {
        $exception_caught = false;
        try {
          $this->obj_process->set_status($status);
        } catch (\PHuby\Error\ProcessError $e) {
          $exception_caught = true;
        }
        $this->assertTrue($exception_caught);
      }
  }

  public function test_add_error() {
    $this->assertEquals(null, $this->obj_process->get_errors());
    $this->obj_process->add_error("Test Error message");
    $this->assertEquals(["Test Error message"], $this->obj_process->get_errors());
  }

  public function test_add_warning() {}

}