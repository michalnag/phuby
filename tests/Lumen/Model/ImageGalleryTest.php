<?php

require_once __DIR__ . "/../../lib/autoload.php";
require_once __DIR__ . "/../../vendor/autoload.php";

use App\Model\ImageGallery;
use PHPUnit\Framework\TestCase;

class ImageGalleryTest extends TestCase {

  public function __construct() {
    $this->obj_image_gallery = new ImageGallery();
  }

  public function test_instance() {
    $this->assertInstanceOf('App\Model\ImageGallery', $this->obj_image_gallery);
  }

  public function test_poulate_attributes() {
    $data = [
      "id" => 123,
      "title" => "Test title",
      "url_title" => "test-name",
      "description" => "this is just a test description<br>with very little <span>of html</span>",
      "dtm_added" => "2016-02-02 12:45:12",
      "published" => 0
    ];

    $this->obj_image_gallery->poulate_attributes($data);

    $this->assertInstanceOf("Attribute\IDAttr", $this->obj_image_gallery->id);
    $this->assertEquals($data["id"], $this->obj_image_gallery->id->to_db_format());

    $this->assertInstanceOf("Attribute\StringAttr", $this->obj_image_gallery->title);
    $this->assertEquals($data["title"], $this->obj_image_gallery->title->to_db_format());

    $this->assertInstanceOf("Attribute\StringAttr", $this->obj_image_gallery->url_title);
    $this->assertEquals($data["url_title"], $this->obj_image_gallery->url_title->to_db_format());

    $this->assertInstanceOf("Attribute\TextAttr", $this->obj_image_gallery->description);
    $this->assertEquals($data["description"], $this->obj_image_gallery->description->to_db_format());

    $this->assertInstanceOf("Attribute\DateTimeAttr", $this->obj_image_gallery->dtm_added);
    $this->assertEquals($data["dtm_added"], $this->obj_image_gallery->dtm_added->to_db_format());

    $this->assertInstanceOf("Attribute\BooleanAttr", $this->obj_image_gallery->published);
    $this->assertEquals($data["published"], $this->obj_image_gallery->published->to_db_format());

    // Let's test if it throws the exception if non-supported argument is passed
    try {
      $this->obj_image_gallery->poulate_attributes(["ball" => 123]);
    } catch(Error\InvalidAttributeError $e) {
      $this->assertTrue(true);
    }
  }

  public function test_poulate_attributes_with_child_classes() {
    $data = [
      "id" => 123,
      "title" => "Test title",
      "url_title" => "test-name",
      "description" => "this is just a test description<br>with very little <span>of html</span>",
      "dtm_added" => "2016-02-02 12:45:12",
      "published" => 0,
      "images" => [
        [
          "id" => 1,
          "title" => "testing 1"
        ],
        [
          "id" => 2,
          "title" => "testing 2"
        ]
      ]
    ];

    $this->obj_image_gallery->poulate_attributes($data);

    $this->assertInstanceOf("App\Model\ImageGallery\ImageCollection", $this->obj_image_gallery->images);

    $this->assertInstanceOf("App\Model\ImageGallery\Image", $this->obj_image_gallery->images->collection[0]);

    $this->assertEquals($data["images"][0]["id"], $this->obj_image_gallery->images->collection[0]->id->to_int());

  }

}
