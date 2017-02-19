<?php

require_once __DIR__ . "/../../../../lib/autoload.php";
require_once __DIR__ . "/../../../../vendor/autoload.php";

use PHPUnit\Framework\TestCase;
use PHuby\Helpers\Utils\ArrayUtils;
use PHuby\Config;

class ArrayUtilsTest extends TestCase {

  public function __construct() {
    Config::set_config_root(__DIR__."/../../../config.d");
  }

  public function test_add_data() {
    $arr1 = ["msg" => ["error" => [["content" => "Error 1"]]]];
    $arr2 = ["msg" => ["error" => [["content" => "Error 2"]]]];

    $arr_expeceted_result = ["msg" => ["error" => [
      ["content" => "Error 1"],
      ["content" => "Error 2"]
    ]]];

    $this->assertEquals(
      $arr_expeceted_result,
      ArrayUtils::add_data("msg:error:[]", $arr1, ["content" => "Error 2"])
    );

    $arr1 = [];

    $this->assertEquals(
      ["msg" => ["error" => [["content" => "Error 1"]]]],
      ArrayUtils::add_data("msg:error:[]", $arr1, ["content" => "Error 1"])
    ); 

    $this->assertEquals(
      ["msg" => [
        "error" => [["content" => "Error 1"]],
        "success" => [["content" => "Success 1"]]
        ]
      ],
      ArrayUtils::add_data("msg:success:[]", $arr1, ["content" => "Success 1"])
    ); 


    $this->assertEquals(
      ["msg" => [
        "error" => [["content" => "Error 1"]],
        "success" => [["content" => "Success 1"],["content" => "Success 2"]]
        ]
      ],
      ArrayUtils::add_data("msg:success:[]", $arr1, ["content" => "Success 2"])
    );

    $arr = [];
    $this->assertEquals(
      ["msg" => ["success" => [["content" => "success msg"]]]],
      ArrayUtils::add_data("msg:success:[]", $arr, ["content" => "success msg"])
    );  


    $this->assertEquals(
      ["msg" => [
        "success" => [["content" => "success msg"]],
        "error" => [["content" => "error msg"]]
      ]],
      ArrayUtils::add_data("msg:error:[]", $arr, ["content" => "error msg"])
    );  
  }

  public function test_get_data() {
    $arr_data = ["msg" => ["error" => [
      ["content" => "Error 1"],
      ["content" => "Error 2"]
    ]]];

    $this->assertEquals(
        [
          ["content" => "Error 1"],
          ["content" => "Error 2"]
        ],
        ArrayUtils::get_data("msg:error", $arr_data)
      );

    $this->assertEquals(null, ArrayUtils::get_data("msg:test", $arr_data));
  }


  public function test_keymap_to_array() {
    $this->assertEquals(
        ArrayUtils::keymap_to_array("id"),
        [ "id" ]
      );

    $this->assertEquals(
        ArrayUtils::keymap_to_array("id,email"),
        [ "id", "email" ]
      );

    $this->assertEquals(
        ArrayUtils::keymap_to_array("user:id"),
        ["user" => [ "id" ]]
      );

    $this->assertEquals(
        ArrayUtils::keymap_to_array("user:id,email"),
        ["user" => [ "id", "email" ]]
      );

    $this->assertEquals(
        ArrayUtils::keymap_to_array("user:orders[id,status]"),
        ["user" => [ "orders" => [[ "id", "status" ]]]]
      );

    $this->assertEquals(
        ArrayUtils::keymap_to_array("user:orders[details:id]"),
        ["user" => [ "orders" => [[ "details" => [ "id" ]]]]]
      );

  }

  public function test_remove_data() {
    $arr_data = ["msg" => ["error" => [
      ["content" => "Error 1"],
      ["content" => "Error 2"]
    ]]];

    $this->assertTrue(ArrayUtils::remove_data("msg:error", $arr_data));
    $this->assertEquals(null, ArrayUtils::get_data("msg:error", $arr_data));
    $this->assertEquals(["msg" => []], $arr_data);

    // Multiple arguments removal and nesting
    $arr_example = [
      "user" => [
        "id" => 1,
        "email" => "test@test.com",
        "orders" => [
          ["id" => 1, "status" => 2, "details" => ["id" => 1, "desc" => "test"]],
          ["id" => 2, "status" => 2, "details" => ["id" => 2, "desc" => "test2"]]
        ]
      ]
    ];


    $arr_example_a = $arr_example;
    $arr_example_b = $arr_example;
    unset($arr_example_a["user"]["id"]);
    ArrayUtils::remove_data("user:id", $arr_example_b);
    $this->assertEquals($arr_example_a, $arr_example_b);

    $arr_example_a = $arr_example;
    $arr_example_b = $arr_example;
    unset($arr_example_a["user"]["id"]);
    unset($arr_example_a["user"]["email"]);
    ArrayUtils::remove_data("user:id,email", $arr_example_b);
    $this->assertEquals($arr_example_a, $arr_example_b);

    $arr_example_a = $arr_example;
    $arr_example_b = $arr_example;
    unset($arr_example_a["user"]["orders"][0]["id"]);
    unset($arr_example_a["user"]["orders"][1]["id"]);
    ArrayUtils::remove_data("user:orders[id]", $arr_example_b);
    $this->assertEquals($arr_example_a, $arr_example_b);

  }

  public function test_splice_data() {
    $arr_data = ["msg" => ["error" => [
      ["content" => "Error 1"],
      ["content" => "Error 2"]
    ]]];

    $this->assertEquals(
      ["error" => [
        ["content" => "Error 1"],
        ["content" => "Error 2"]
      ]], 
      ArrayUtils::splice_data("msg", $arr_data)
    );

    $this->assertEquals([], $arr_data);
  }

  public function test_group_by_map() {
    $arr_ungrouped = [
      [
        "user_id" => 1,
        "email" => "test@test.com",
        "description" => "description 1",
        "description_type" => 1
      ],
      [
        "user_id" => 1,
        "email" => "test@test.com",
        "description" => "description 2",
        "description_type" => 2
      ],
      [
        "user_id" => 2,
        "email" => "test2@test.com",
        "description" => "description 1",
        "description_type" => 1
      ],
      [
        "user_id" => 2,
        "email" => "test2@test.com",
        "description" => "description 2",
        "description_type" => 2
      ]
    ];

    $arr_grouped = [
      1 => [
        "user_id" => 1,
        "email" => "test@test.com",
        "descriptions" => [
          1 => [
            "description" => "description 1",
            "description_type" => 1
          ],
          2 => [
            "description" => "description 2",
            "description_type" => 2
          ]
        ]
      ],
      2 => [
        "user_id" => 2,
        "email" => "test2@test.com",
        "descriptions" => [
          1 => [
            "description" => "description 1",
            "description_type" => 1
          ],
          2 => [
            "description" => "description 2",
            "description_type" => 2
          ]
        ]
      ]
    ];

    $arr_result = ArrayUtils::group_by_map($arr_ungrouped, [
        ":user_id" => [ 
            "user_id",
            "email",
            "descriptions" => [
              ":description_type" => [
                "description", 
                "description_type"
              ]
            ]
          ]
        ]
      );

    $this->assertEquals(
      $arr_grouped,
      $arr_result
    );

    // NESTING
    $arr_ungrouped = [
      [
        "user_id" => 1,
        "email" => "test@test.com",
        "description" => "description 1",
        "description_type" => 1,
        "description_source" => 1
      ],
      [
        "user_id" => 1,
        "email" => "test@test.com",
        "description" => "description 1",
        "description_type" => 1,
        "description_source" => 2
      ],
      [
        "user_id" => 1,
        "email" => "test@test.com",
        "description" => "description 2",
        "description_type" => 2,
        "description_source" => 1
      ],
      [
        "user_id" => 1,
        "email" => "test@test.com",
        "description" => "description 2",
        "description_type" => 2,
        "description_source" => 2
      ],
      [
        "user_id" => 2,
        "email" => "test2@test.com",
        "description" => "description 1",
        "description_type" => 1,
        "description_source" => 1
      ],
      [
        "user_id" => 2,
        "email" => "test2@test.com",
        "description" => "description 1",
        "description_type" => 1,
        "description_source" => 2
      ],
      [
        "user_id" => 2,
        "email" => "test2@test.com",
        "description" => "description 2",
        "description_type" => 2,
        "description_source" => 1
      ],
      [
        "user_id" => 2,
        "email" => "test2@test.com",
        "description" => "description 2",
        "description_type" => 2,
        "description_source" => 2
      ]
    ];


    $arr_grouped = [
      1 => [
        "user_id" => 1,
        "email" => "test@test.com",
        "descriptions" => [
          1 => [
            "description" => "description 1",
            "description_type" => 1,
            "sources" => [
              [ "description_source" => 1 ],
              [ "description_source" => 2 ]
            ]

          ],
          2 => [
            "description" => "description 2",
            "description_type" => 2,
            "sources" => [
              [ "description_source" => 1 ],
              [ "description_source" => 2 ]
            ]
          ]
        ]
      ],
      2 => [
        "user_id" => 2,
        "email" => "test2@test.com",
        "descriptions" => [
          1 => [
            "description" => "description 1",
            "description_type" => 1,
            "sources" => [
              [ "description_source" => 1 ],
              [ "description_source" => 2 ]
            ]
          ],
          2 => [
            "description" => "description 2",
            "description_type" => 2,
            "sources" => [
              [ "description_source" => 1 ],
              [ "description_source" => 2 ]
            ]
          ]
        ]
      ]
    ];

    $arr_result = ArrayUtils::group_by_map($arr_ungrouped, [
          ":user_id" => [ 
            "user_id",
            "email",
            "descriptions" => [
              ":description_type" => [
                "description", 
                "description_type",
                "sources" => [
                  ["description_source"]
                ]
              ]
            ]
          ]
        ]        
      );
    
    $this->assertEquals($arr_grouped, $arr_result);
  }

  public function test_group_by_map_with_translations() {
    // NESTING
    $arr_ungrouped = [
      [
        "user_id" => 1,
        "email" => "test@test.com",
        "description" => "description 1",
        "description_type" => 1,
        "description_source" => 1
      ],
      [
        "user_id" => 1,
        "email" => "test@test.com",
        "description" => "description 1",
        "description_type" => 1,
        "description_source" => 2
      ],
      [
        "user_id" => 1,
        "email" => "test@test.com",
        "description" => "description 2",
        "description_type" => 2,
        "description_source" => 1
      ],
      [
        "user_id" => 1,
        "email" => "test@test.com",
        "description" => "description 2",
        "description_type" => 2,
        "description_source" => 2
      ],
      [
        "user_id" => 2,
        "email" => "test2@test.com",
        "description" => "description 1",
        "description_type" => 1,
        "description_source" => 1
      ],
      [
        "user_id" => 2,
        "email" => "test2@test.com",
        "description" => "description 1",
        "description_type" => 1,
        "description_source" => 2
      ],
      [
        "user_id" => 2,
        "email" => "test2@test.com",
        "description" => "description 2",
        "description_type" => 2,
        "description_source" => 1
      ],
      [
        "user_id" => 2,
        "email" => "test2@test.com",
        "description" => "description 2",
        "description_type" => 2,
        "description_source" => 2
      ]
    ];


    $arr_grouped = [
      1 => [
        "id" => 1,
        "email" => "test@test.com",
        "descriptions" => [
          1 => [
            "description" => "description 1",
            "type" => 1,
            "sources" => [
              [ "description_source" => 1 ],
              [ "description_source" => 2 ]
            ]

          ],
          2 => [
            "description" => "description 2",
            "type" => 2,
            "sources" => [
              [ "description_source" => 1 ],
              [ "description_source" => 2 ]
            ]
          ]
        ]
      ],
      2 => [
        "id" => 2,
        "email" => "test2@test.com",
        "descriptions" => [
          1 => [
            "description" => "description 1",
            "type" => 1,
            "sources" => [
              [ "description_source" => 1 ],
              [ "description_source" => 2 ]
            ]
          ],
          2 => [
            "description" => "description 2",
            "type" => 2,
            "sources" => [
              [ "description_source" => 1 ],
              [ "description_source" => 2 ]
            ]
          ]
        ]
      ]
    ];

    $arr_result = ArrayUtils::group_by_map($arr_ungrouped, [
          ":user_id" => [ 
            "user_id|id",
            "email",
            "descriptions" => [
              ":description_type" => [
                "description", 
                "description_type|type",
                "sources" => [
                  ["description_source"]
                ]
              ]
            ]
          ]
        ]        
      );
    
    $this->assertEquals($arr_grouped, $arr_result);
  }

}