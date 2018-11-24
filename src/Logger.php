<?php
/**
 * Logger class
 * Standard
 *
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby
 */

namespace PHuby;

use \Monolog\Logger as MonologLogger;
use \Monolog\Handler\StreamHandler as MonologStreamHandler;
use PHuby\Helpers\Utils\ObjectUtils;
use PHuby\Error;

class Logger {

  private static $logger;

  // We use an array insted of one logger to keep multiple loggers cached
  private static $arr_loggers = [];

  const
    // Constances representing available levels
    DEBUG = "debug",
    INFO = "info",
    NOTICE = "notice",
    WARNING = "warning",
    ERROR = "error",
    CRITICAL = "critical",
    ALERT = "alert",
    EMERGENCY = "emergency",

    // Logger names
    LOGGER_DEFAULT = "default";


  public static function array_to_string(array $data) {
    $string_parts = [];
    foreach($data as $key => $value) {
      $string_parts[] = "$key: $value";
    }
    return join(', ', $string_parts);
  }

  protected static function set_logger_from_config(\stdClass $logger_details) {
    if (!self::is_logger_set($logger_details->name)) {
      self::add_logger($logger_details->name, self::instantiate_monolog_logger($logger_details));
    } else {
      // This is already set
      return;
    }
  }

  protected static function add_logger($str_name, $logger) {
    self::$arr_loggers[$str_name] = $logger;
    return;
  }

  protected static function is_logger_set($str_name) {
    return array_key_exists($str_name, self::$arr_loggers);
  }

  /**
   * @return MonologLogger
   */
  protected static function instantiate_monolog_logger(\stdClass $logger_details) {
    $logger = new MonologLogger($logger_details->name);
    $logger->pushHandler(new MonologStreamHandler($logger_details->output, constant("\Monolog\Logger::".strtoupper($logger_details->level))));
    $logger->debug("Instantiating Monolog instance name $logger_details->name, output: $logger_details->output, and level: $logger_details->level");
    return $logger;
  }

  protected static function log($str_level, $arr_args) {

    switch (count($arr_args)) {

      case 1:
        // Only one parameter passed, log a message to default logger
        if (!self::is_logger_set(self::LOGGER_DEFAULT)) {
          self::set_logger_from_config(Config::get_data("log:".self::LOGGER_DEFAULT));
        }
        return self::$arr_loggers[self::LOGGER_DEFAULT]->$str_level($arr_args[0]);
        break;

      case 2:
        // Two arguments passed. First is the name of the config log
        $obj_config = Config::get_data("log:{$arr_args[0]}");
        self::set_logger_from_config($obj_config);
        return self::$arr_loggers[$obj_config->name]->$str_level($arr_args[1]);
        break;

      default:
        // Unsupported numbers of parameters
        throw new Error\InvalidArgumentError(__METHOD__ . " must receive one or two arguments. Got " . count($arr_args));
        break;
    }
  }

  // 100 - debug
  static function debug() {
    self::log(self::DEBUG, func_get_args());
  }
  
  // 200 - info
  static function info() {
    self::log(self::INFO, func_get_args());
  }
  
  // 250 - notice
  static function notice() {
    self::log(self::NOTICE, func_get_args());
  }

  // 300 - warning
  static function warning() {
    self::log(self::WARNING, func_get_args());
  }   

  // 400 - error
  static function error() {
    self::log(self::ERROR, func_get_args());
  }   

  // 500 - critical
  static function critical() {
    self::log(self::CRITICAL, func_get_args());
  }

  // 550 - alert
  static function alert() {
    self::log(self::ALERT, func_get_args());
  }

  // 600 - emergency
  static function emergency() {
    self::log(self::EMERGENCY, func_get_args());
  }

}