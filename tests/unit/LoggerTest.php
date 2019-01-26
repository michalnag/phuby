<?php

use PHuby\Helpers\Utils\FileUtils;
use PHuby\{Error,Logger};

class LoggerTest extends TestCase {

  const
    ADDITIONAL_LOG = "additional";

  public function testDebug() {
    Logger::debug("Test debug");
    Logger::debug(self::ADDITIONAL_LOG, "Test additional debug");

    $bol_exception_caught = false;    
    try {
      Logger::debug();
    } catch(Error\InvalidArgumentError $e) {
      $bol_exception_caught = true;
    }

    $this->assertTrue($bol_exception_caught);

    $bol_exception_caught = false;    
    try {
      Logger::debug(self::ADDITIONAL_LOG, "Test additional debug", "This should fail");
    } catch(Error\InvalidArgumentError $e) {
      $bol_exception_caught = true;
    }
  }

  public function testInfo() {
    Logger::info("Test info");
    Logger::info(self::ADDITIONAL_LOG, "Test additional info");
  }

  public function testNotice() {
    Logger::notice("Test notice");
    Logger::notice(self::ADDITIONAL_LOG, "Test additional notice");
  }

  public function testWarning() {
    Logger::warning("Test warning");
    Logger::warning(self::ADDITIONAL_LOG, "Test additional warning");
  }

  public function testError() {
    Logger::error("Test error");
    Logger::error(self::ADDITIONAL_LOG, "Test additional error");
  }

  public function testCritical() {
    Logger::critical("Test critical");
    Logger::critical(self::ADDITIONAL_LOG, "Test additional critical");
  }

  public function testAlert() {
    Logger::alert("Test alert");
    Logger::alert(self::ADDITIONAL_LOG, "Test additional alert");
  }

}