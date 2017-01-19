<?php
/**
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby
 * 
 * AbstractProcess class sits in the bottom of Processs and provides groud work for them
 */

namespace PHuby;
use PHuby\AbstractCore;
use PHuby\Error;

abstract class AbstractProcess extends AbstractCore {

  /** All constances holding statuses */
  const
    NOT_STARTED = 1,
    COMPLETE = 2,
    COMPLETE_WITH_WARNINGS = 3,
    FAIL = 4,
    PENDING = 5;

  private
    $int_status = self::NOT_STARTED,
    $arr_errors = [],
    $arr_warnings = [];

  /**
   * Method retrieves current process status
   * 
   * @return integer representing process status
   */
  public function get_status() {
    return $this->int_status;
  }

  /**
   * Method removes all errors from the process by setting $arr_errors to 
   * an empty array
   * 
   * @return boolean true once errors are removed
   */
  public function clear_errors() {
    $this->arr_errors = [];
    return true;
  }

  /**
   * Adds error message to the $arr_errors
   * 
   * @param string $str_error_msg containing an error message
   * @return boolean true once error is added
   */
  public function add_error($str_error_msg) {
    $this->arr_errors[] = $str_error_msg;
    return true;
  }

  /**
   * Adds multiple errors to the $arr_errors array
   * 
   * @param mixed[] Array containing error messages
   */
  public function add_errors(Array $arr_errors) {
    foreach($arr_errors as $str_error_msg) {
      $this->add_error($str_error_msg);
    }
    return true;
  }

  /**
   * Method removes all warnings from the process by setting $arr_warnings to 
   * an empty array
   * 
   * @return boolean true once warnings are removed
   */
  public function clear_warnings() {
    $this->arr_warnings = [];
    return true;
  }

  /**
   * Adds warning message to the $arr_warnings
   * 
   * @param string $str_warning_msg containing an warning message
   * @return boolean true once warning is added
   */
  public function add_warning($str_warning_msg) {
    $this->arr_warnings[] = $str_warning_msg;
    return true;
  }

  /**
   * Sets the status of the process that is corresponding to status constances
   * 
   * @param integer $int_process_status represeting the status id
   * @return boolean true if status is succesfully set
   * @throws \PHuby\Error\ProcessError if invalid status is passed
   */
  public function set_status($int_process_status) {
    if($this->is_status_allowed($int_process_status)) {
      $this->status = $int_process_status;
      return true;
    } else {
      throw new Error\ProcessError("Unable to set unsupported status $int_process_status");
    }
  }

  /**
   * Checks if the status is allowed to be set
   * 
   * @param integer $int_process_status represeting the status id
   * @return boolean true if status is allowed, false otherwise
   */
  protected function is_status_allowed($int_process_status) {
    $reflection_class = new \ReflectionClass(get_class($this));
    $arr_constances = $reflection_class->getConstants();
    $arr_statuses = [];
    foreach($arr_constances as $key => $value) {
      if(preg_match("/NOT_STARTED|COMPLETE|COMPLETE_WITH_WARNINGS|FAIL|PENDING/", $key)) {
        $arr_statuses[] = $value;
      }
    }
    return in_array($int_process_status, $arr_statuses);
  }

  /**
   * Method gets all error messages that occured during the process.
   * 
   * @return mixed[] Array containing all errors, null if no errors occured
   */
  public function get_errors() {
    return empty($this->arr_errors) ? null : $this->arr_errors;
  }

  /**
   * Method gets all warning messages that occured during the process.
   * 
   * @return mixed[] Array containing all warnings, null if no warnings occured
   */
  public function get_warnings() {
    return empty($this->arr_warnings) ? null : $this->arr_warnings;
  }

  /**
   * Method checks if the process has completed by checking the currently set status
   * 
   * @return boolean true if the process has completed, false otherwise
   */
  public function has_completed() {
    if(in_array($this->get_status(), [$this::COMPLETE, $this::COMPLETE_WITH_WARNINGS])) {
      return true;
    } else {
      return false;
    }
  }

}