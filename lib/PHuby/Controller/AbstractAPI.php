<?php
/**
 * AbstractAPI
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby
 */

namespace PHuby\Controller;

use PHuby\Logger;
use PHuby\Helpers\Utils\ArrayUtils;
use PHuby\AbstractCore;

abstract class AbstractAPI extends AbstractCore {

  protected $obj_body;

  public function api_success($mix_data = null, $response_code = 200, $message = null) {
    http_response_code($response_code);
    return json_encode([
        "status" => "success",
        "responseCode" => $response_code,
        "message" => $message,
        "data" => $mix_data
      ]);
  }

  public function api_error($response_code = 500, $message = null) {
    http_response_code($response_code);
    return json_encode([
        "status" => "error",
        "responseCode" => $response_code,
        "message" => $message,
        "data" => null
      ]);
  }

  public function api_not_found($data = null) {
    http_response_code(404);
    return json_encode([
        "status" => "error",
        "responseCode" => 404,
        "data" => $data
      ]);
  }

  protected function get_body() {
    return $this->obj_body;
  }

  protected function get_body_param($str_param) {
    if (isset($this->get_body()->{$str_param})) {
      return $this->get_body()->{$str_param};
    } else {
      return null;
    }
  }

  protected function get_body_params($str_params) {
    $arr_requested_params = ArrayUtils::keymap_to_array($str_params);
    $arr_params = [];
    foreach ($arr_requested_params as $str_param) {
      $arr_params[$str_param] = $this->get_body_param($str_param);
    }
    return $arr_params;
  }

  protected function get_request_body($str_required = null) {
    // Get input 
    $str_input = file_get_contents('php://input');
    $obj_body = json_decode($str_input);
    if ($str_required !== null) {
      // Check for required fields
      $arr_required = ArrayUtils::keymap_to_array($str_required);
      foreach ($arr_required as $str_key) {
        if(!isset($obj_body->{$str_key}) || is_null($obj_body->{$str_key})) {
          throw new NetworkRequestError("Missing $str_key from request.");
        }
      }
    }
    $this->obj_body = $obj_body;
    return $this;
  }

}