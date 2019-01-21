<?php

namespace PHubyTest\Model;

use PHuby\AbstractModel;
use PHuby\Attribute;
use PHuby\Attribute\StringAttr;
use PHuby\Attribute\PasswordAttr;
use PHuby\Interfaces\ResourceModelInterface;
use PHubyTest\DBI\UserDBI;
use PHuby\Traits\IsResource;

class User extends AbstractModel implements ResourceModelInterface {

  use IsResource;

  const 
    STATUS_ACTIVE = 1,
    STATUS_LOCKED = 2,
    STATUS_ARCHIVED = 3,
    STATUS_GUEST = 3;

  protected 
    $id, $email, $password, $password_reset_token, $activation_token, $first_name, $last_name, $company_name, $contact_number, $dtm_created, $status;

  const ATTRIBUTE_MAP = [
    'id'            => Attribute\IntAttr::class,
    'email'         => Attribute\EmailAttr::class,
    'password'      => Attribute\PasswordAttr::class,
    'password_reset_token'      => Attribute\TokenAttr::class,
    'activation_token'          => Attribute\TokenAttr::class,
    'first_name'      => [ 
      "class" => Attribute\StringAttr::class,
      "options" => [
        "validation" => [
          "length" => [ "max" => 40 ]
        ]
      ]
    ],
    'last_name'       => [
      "class" => Attribute\StringAttr::class,
      "options" => [
        "validation" => [
          "length" => [ "max" => 40 ]
        ]
      ]
    ],
    'company_name'    => [
      "class" => Attribute\StringAttr::class,
      "options" => [
        "validation" => [
          "length" => [ "max" => 40 ]
        ]
      ]
    ],
    'contact_number'  => [
      "class" => Attribute\StringAttr::class,
      "options" => [
        "validation" => [
          "length" => [ "max" => 16 ]
        ]
      ]
    ],
    'dtm_created'     => Attribute\DateTimeAttr::class,
    'status'          => [ 
      "class" => Attribute\IntAttr::class,
      "options" => [
        "default_value" => self::STATUS_LOCKED
      ]
    ]
  ];

  public function generate_random_password() {
    $str_password = StringAttr::generate_random_string();
    return $this->set_attr('password', PasswordAttr::hash_password($str_password));
  }

  public static function DBI() {
    return UserDBI::class;
  }

  public function getCreateData(): array {
    return $this->get_formatted_params('email,password,password_reset_token,activation_token,first_name,last_name,company_name,contact_number,dtm_created,status');
  }

  public function getUpdateData(): array {
    return array_merge(
      $this->get_formatted_params('id'),
      $this->getCreateData()
    );
  }

}