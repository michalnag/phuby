<?php

namespace Model;

use PHuby\AbstractModel;

class TestModel extends AbstractModel {

  protected 
    $int,
    $datetime,
    $email,
    $string,
    $password,
    $token,
    $boolean,
    $text,
    $image,
    $file,
    $collection,
    $nested_model;
    
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
    'string' => [
      "class" => "\PHuby\Attribute\StringAttr"
    ],
    'string_with_options' => [
      "class" => "\PHuby\Attribute\StringAttr",
      "options" => [
        "length" => [
          "min" => 8,
          "max" => 12
        ]
      ]
    ],
    'password' => [
      "class" => "\PHuby\Attribute\PasswordAttr"
    ],
    'token' => [
      "class" => "\PHuby\Attribute\TokenAttr",
      "options" => [
        "length" => 12
      ]
    ],
    'boolean' => [
      "class" => "\PHuby\Attribute\BooleanAttr"
    ],
    'image' => [
      "class" => "\PHuby\Attribute\ImageAttr"
    ],
    'file' => [
      "class" => "\PHuby\Attribute\FileAttr"
    ],

    // Child classes
    'collection' => [
      "collection_class" => "\Model\TestModelCollection"
    ],
    'nested_model' => [
      "child_class" => "\Model\TestModel"
    ]

  ];

}