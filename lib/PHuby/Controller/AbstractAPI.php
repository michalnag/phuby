<?php
/**
 * AbstractAPI
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby
 */

namespace PHuby\Controller;

use PHuby\Logger;
use PHuby\Helpers\Utils;
use PHuby\AbstractCore;

abstract class AbstractAPI extends AbstractCore {

  public function api_success($mix_data, $response_code = 200, $message = null) {
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

  public function api_not_found() {
    http_response_code(404);
    return json_encode([
        "status" => "error",
        "responseCode" => 404,
        "data" => null
      ]);
  }

}