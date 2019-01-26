<?php

namespace PHuby\DBI;

class ResourceDBI extends \PHuby\AbstractDBI {
  
     public static function findById($table, $id): ?array {
        return self::get_by_id($table, $id);
    }
    
     public static function insert($table, $params) {
        return self::_insert($table, $params);
    }
    
    public static function update($table, $params) {
        return self::_update($table, $params, 'id');
    }

    public static function delete($table, $id) {
        return self::_delete($table, [ 'id' => $id ]);
    }    
}