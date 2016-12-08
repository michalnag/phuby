<?php

namespace PHuby;
use PHuby\AbstractCore;
use PHuby\Error;

abstract class AbstractProcess extends AbstractCore {

  const
    PS_NOT_STARTED = 0,
    PS_COMPLETE_NO_ERRORS = 1,
    PS_COMPLETE_WITH_ERRORS = 2,
    PS_FAILED = 3,
    PS_PENDING = 4;

  private
    $int_status = self::PS_NOT_STARTED,
    $arr_errors = array(),
    $arr_public_msg = array();


  public function set_process_error() {

  }

  protected function set_process_status($int_process_status, Array $arr_options = null) {
    if($this->is_status_allowed($int_process_status)) {
      $this->status = $int_process_status;
      return true;
    } else {
      throw new ProcessError("Unable to set unsupported status $int_process_status");
    }
  }

  private function is_status_allowed($int_process_status) {
    $reflection_class = new \ReflectionClass(get_class($this));
    $arr_constances = $reflection_class->getConstants();
    $arr_statuses = [];
    foreach($arr_constances as $key => $value) {
      if(preg_match("/^PS_/", $key)) {
        $arr_statuses[] = $value;
      }
    }
    return in_array($int_process_status, $arr_statuses);
  }

}