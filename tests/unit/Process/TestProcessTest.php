<?php

use PHubyTest\Process\TestProcess;
use PHuby\Config;
use PHuby\Helpers\Utils\FileUtils;

class TestProcessTest extends TestCase {

  public function setUp() {
    parent::setUp();
    $this->obj_process = new TestProcess();
  }

  public function testInstantiation() {
    $this->assertEquals($this->obj_process->get_status(), TestProcess::NOT_STARTED);
    $this->assertEquals(null, $this->obj_process->get_errors());
    $this->assertEquals(null, $this->obj_process->get_warnings());
    $this->assertFalse($this->obj_process->has_completed());
  }

  public function testIsStatusAllowed() {
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

  public function testAddError() {
    $this->assertEquals(null, $this->obj_process->get_errors());
    $this->obj_process->add_error("Test Error message");
    $this->assertEquals(["Test Error message"], $this->obj_process->get_errors());
  }

  public function testAddWarning() {
    $this->markTestIncomplete();
  }

}