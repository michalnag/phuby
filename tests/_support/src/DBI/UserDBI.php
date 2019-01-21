<?php

namespace PHubyTest\DBI;

use PHuby\Interfaces\ResourceDBIInterface;
use PHuby\AbstractDBI;
use PHuby\Helpers\Utils\ArrayUtils;

class UserDBI extends AbstractDBI implements ResourceDBIInterface {
    
     public static function findById($id): ?array {
        return self::_get_by_id('users', $id);
    }
    
     public static function create($params) {
        return self::default_insert(
            ArrayUtils::keymap_to_array('email,password,password_reset_token,activation_token,first_name,last_name,company_name,contact_number,dtm_created,status'),
            $params,
            'users'
        );
    }
    
    public static function update($params) {
        return self::default_update(
            ArrayUtils::keymap_to_array('id,email,password,password_reset_token,activation_token,first_name,last_name,company_name,contact_number,dtm_created,status'),
            $params,
            'users',
            'id'
        );
    }

    public static function delete($id) {
        return self::default_delete(
            'users',
            [ 'id' => $id ]
        );
    }

}