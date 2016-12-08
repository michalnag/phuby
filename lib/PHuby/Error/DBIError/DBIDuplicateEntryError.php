<?php 

namespace PHuby\Error\DBIError;

use PHuby\Error\DBIError;

class DBIDuplicateEntryError extends DBIError {

  public function get_duplicated_key_name() {
    return preg_replace("/^.*for\skey\s/", "", str_replace("'", "", $this->getMessage()));
  }

}