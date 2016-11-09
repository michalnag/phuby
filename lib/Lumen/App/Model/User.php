<?php

namespace App\Model;

use App\AbstractModel;

class User extends AbstractModel {

  const ATTRIBUTE_MAP = [
    "id" => [
      "type" => "attribute",
      "attribute_class" => "IDAttr"
    ],
    "uuid" => [
      "type" => "attribute",
      "attribute_class" => "UUIDAttr",
      "attribute_options" => [
        "length" => ['exact' => 8]
      ]    
    ],
    "email" => [
      "type" => "attribute",
      "attribute_class" => "StringAttr"      
    ],
    "password" => [
      "type" => "attribute",
      "attribute_class" => "PasswordAttr"      
    ],
    "activation_token" => [
      "type" => "attribute",
      "attribute_class" => "TokenAttr",
      "attribute_options" => [
        "length" => ['exact' => 32]
      ]
    ],
    "password_reset_token" => [
      "type" => "attribute",
      "attribute_class" => "TokenAttr",
      "attribute_options" => [
        "length" => ['exact' => 32]
      ]
    ]
  ];

  const CHILD_CLASS_MAP = [
    "accounts" => "\App\Model\User\UserAccount"
  ];

  public function generate_uuid() {
    
  }

  public function generate_activation_token() {

  }

}