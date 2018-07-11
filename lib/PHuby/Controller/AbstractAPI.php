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
use PHuby\Error;

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

  public function api_forbidden($msg = null, $data = null) {
    http_response_code(403);
    return json_encode([
        "status" => "forbidden",
        "responseCode" => 403,
        "data" => $data,
        "message" => $msg
      ]);
  }

  protected function get_body() {
    return $this->obj_body;
  }

  protected function get_body_param($str_param, $obj_data = null) {
    $arr_parts = explode(':', $str_param);
    $obj_source = $obj_data ? $obj_data : $this->get_body();
    if (count($arr_parts) == 1) {
      if (isset($obj_source->{$str_param})) {
        return $obj_source->{$str_param};
      } else {
        return null;
      }      
    } else {
      $str_first_param = array_shift($arr_parts);
      if (isset($obj_source->{$str_first_param})) {
        return $this->get_body_param(
          join(':', $arr_parts),
          $obj_source->{$str_first_param}
        );
      } else {
        return null;
      }   
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
          throw new Error\NetworkRequestError("Missing $str_key from request.");
        }
      }
    }
    $this->obj_body = $obj_body;
    return $this;
  }

}