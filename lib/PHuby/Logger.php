<?php
/**
 * Logger
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby
 */

namespace PHuby;

use \Monolog\Logger as MonologLogger;
use \Monolog\Handler\StreamHandler as MonologStreamHandler;
use PHuby\Helpers\Utils\ObjectUtils;

class Logger {

  private static $logger;

  public static function array_to_string(Array $data) {
    $string_parts = [];
    foreach($data as $key => $value) {
      $string_parts[] = "$key: $value";
    }
    return join(', ', $string_parts);
  }

  public static function set_logger(MonologLogger $logger = null) {
    // Check if the logger has been passed
    if($logger) {
      // Logger has been passed as an argument
      self::$logger = $logger;
      return true;
    } else {
      // Check if the logger is already instantiated
      if(self::$logger) {
        // Logger exists. Return true
        return true;
      } else {
        // Logger is not set. Get the name of the logger that we want
        // We want to go to the top level controller that calls the message, and check if the specific logger is set on it
        foreach(debug_backtrace(false) as $trace) {
          // Check if the file key exists
          if(array_key_exists("file", $trace) && !empty($trace["file"])) {
            // We can check the filename. Check if it is type of controller
            $caller_class = ObjectUtils::get_class_name_from_filepath($trace['file']);
            if($caller_class && preg_match("/Controller/", $caller_class)) {
              // This is a controller. Check if it has LOGGER_NAME defined
              if(defined("$caller_class::LOGGER_NAME")) {
                // Logger name is defined. Use details of it to create new MonologLogger
                return self::instantiate_monolog_logger(Config::get_data("log:$caller_class::LOGGER_NAME"));
              } else {
                // Controller has no Logger name defined. Break the loop
                break;
              }
            } else {
              // Caller class is not a controller type. Continue looping
              continue;
            }
          } else {
            // file key does not exist in the trace array. Continue looping
            continue;
          }
        }

        // Loop is now finished. Check if the log is defined and if not, Set it with default values
        if(!self::$logger) {
          return self::instantiate_monolog_logger(Config::get_data("log:default"));
        }
      }
    }
  }

  private static function instantiate_monolog_logger(\stdClass $logger_details) {
    // Check if all details 
    self::$logger = new MonologLogger($logger_details->name);
    self::$logger->pushHandler(new MonologStreamHandler($logger_details->output, constant("\Monolog\Logger::".strtoupper($logger_details->level))));
    self::$logger->debug("Instantiating Monolog instance name $logger_details->name, output: $logger_details->output, and level: $logger_details->level");
    return true;
  }

  // With every message, we need to check if an instance of a logger class is set, and falls back to the right process name.
  // Dedicated process names are defined in config.d/log.json
  
  // 100 - debug
  static function debug($msg) {
    self::set_logger();
    self::$logger->debug($msg);
  }
  
  // 200 - info
  static function info($msg) {
    self::set_logger();
    self::$logger->info($msg);
  }
  
  // 250 - notice
  static function notice($msg) {
    self::set_logger();
    self::$logger->notice($msg); 
  }

  // 300 - warning
  static function warning($msg) {
    self::set_logger();
    self::$logger->warning($msg); 
  }   

  // 400 - error
  static function error($msg) {
    self::set_logger();
    self::$logger->error($msg);
  }   

  // 500 - critical
  static function critical($msg) {
    self::set_logger();
    self::$logger->critical($msg);    
  }

  // 550 - alert
  static function alert($msg) {
    self::set_logger();
    self::$logger->alert($msg);
  }

  // 600 - emergency
  static function emergency($msg) {
    self::set_logger();
    self::$logger->emergency($msg); 
  }

}