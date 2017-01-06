<?php

namespace Model;

use PHuby\AbstractModel;

class TestModel extends AbstractModel {

  public 
    $int,
    $datetime,
    $email,
    $uuid,
    $string,
    $password,
    $token,
    $boolean,
    $text,
    $collection;

  const ATTRIBUTE_MAP = [
    'int'        => [
      "class" => "\PHuby\Attribute\IntAttr"
    ],
    'datetime'        => [
      "class" => "\PHuby\Attribute\DateTimeAttr"
    ],
    'email'        => [
      "class" => "\PHuby\Attribute\EmailAttr"
    ],
    'uuid' => [
      "class" => "\PHuby\Attribute\UUIDAttr"
    ],
    'string' => [
      "class" => "\PHuby\Attribute\StringAttr"
    ],
    'password' => [
      "class" => "\PHuby\Attribute\PasswordAttr"
    ],
    'token' => [
      "class" => "\PHuby\Attribute\TokenAttr"
    ],
    'boolean' => [
      "class" => "\PHuby\Attribute\BooleanAttr"
    ],
    'text' => [
      "class" => "\PHuby\Attribute\TextAttr"
    ]
  ];

  const CHILD_CLASS_MAP = [
    "collection" => "\Model\TestModelCollection"
  ];

}