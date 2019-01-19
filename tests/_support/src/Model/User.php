<?php

namespace PHubyTest\Model;

use PHuby\AbstractModel;
use PHuby\Attribute\{StringAttr,PasswordAttr};

class User extends AbstractModel {

  const 
    STATUS_ACTIVE = 1,
    STATUS_LOCKED = 2,
    STATUS_ARCHIVED = 3,
    STATUS_GUEST = 3;

  protected 
    $id, $email, $password, $password_reset_token, $activation_token, $first_name, $last_name, $company_name, $contact_number, $dtm_created, $status;

  const ATTRIBUTE_MAP = [
    'id'            => [ "class" => "\PHuby\Attribute\IntAttr" ],
    'email'         => [ "class" => "\PHuby\Attribute\EmailAttr" ],
    'password'      => [ "class" => "\PHuby\Attribute\PasswordAttr" ],
    'password_reset_token'      => [ "class" => "\PHuby\Attribute\TokenAttr" ],
    'activation_token'          => [ "class" => "\PHuby\Attribute\TokenAttr" ],
    'first_name'      => [ 
      "class" => "\PHuby\Attribute\StringAttr",
      "options" => [
        "validation" => [
          "length" => [ "max" => 40 ]
        ]
      ]
    ],
    'last_name'       => [
      "class" => "\PHuby\Attribute\StringAttr",
      "options" => [
        "validation" => [
          "length" => [ "max" => 40 ]
        ]
      ]
    ],
    'company_name'    => [
      "class" => "\PHuby\Attribute\StringAttr",
      "options" => [
        "validation" => [
          "length" => [ "max" => 40 ]
        ]
      ]
    ],
    'contact_number'  => [
      "class" => "\PHuby\Attribute\StringAttr",
      "options" => [
        "validation" => [
          "length" => [ "max" => 16 ]
        ]
      ]
    ],
    'dtm_created'     => [ "class" => "\PHuby\Attribute\DateTimeAttr" ],
    'status'          => [ 
      "class" => "\PHuby\Attribute\IntAttr" ,
      "options" => [
        "default_value" => self::STATUS_LOCKED
      ]
    ],

    // Non IO
    'addresses' => [
      'collection_class' => "DFC\Model\User\UserAddressCollection"
    ]
  ];

  public function generate_random_password() {
    $str_password = StringAttr::generate_random_string();
    return $this->set_attr('password', PasswordAttr::hash_password($str_password));
  }

  public function get_api_data() {
    return $this->get_flat_data("exclude:password,password_reset_token,activation_token|nesting:true");
  }

  public function set_as_guest() {
    $this->set_attr('status', self::STATUS_GUEST);
  }

  public function is_guest() {
    return $this->get_attr('status')->get() === self::STATUS_GUEST;
  }

  public function canPlaceOrder() {
    return $this->get_attr('status')->to_int() !== self::STATUS_ARCHIVED;
  }
}