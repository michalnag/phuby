<?php 
/**
 * DBIDuplicateEntryError
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby\Error\DBIError
 */

namespace PHuby\Error\DBIError;

use PHuby\Error\DBIError;

class DBIDuplicateEntryError extends DBIError {

  public function get_duplicated_key_name() {
    return preg_replace("/^.*for\skey\s/", "", str_replace("'", "", $this->getMessage()));
  }

}