<?php

namespace Lumen\Network;

use Lumen\AbstractNetwork;
use Lumen\Logger;
use Lumen\Error;
use Lumen\Network\Request\RequestParam;

class Request extends AbstractNetwork {

  private 
    $raw_request_data,
    $parameters = [];
   
  /**
   * Method 
   *
   * @param string[] $params_details Array with strings 
   * @throws Lumen\Error\MissingParameterError if parameter is required and not found
   */
  public function get_params_from_request(Array $params_details) {
    
    foreach($params_details as $param_details) {
      Logger::debug("Processing param details: $param_details[0]");
      $param_details_parts = explode(':', $param_details[0]);
      
      // Check if options have been passed
      $param_options = isset($param_details[1]) ? $param_details[1] : null;

      // Check if the parameter is required
      if(array_key_exists("required", $param_options)) {
        // This parameter is required. Check if the value exists
        if(!$this->is_parameter_passed($param_details_parts[0], $param_details_parts[1])) {
          throw new Error\MissingParameterError("Required parameter $param_details_parts[0] is missing from the request");
        }
      }

      // Assign core details and extract options
      $request_param = new RequestParam();

      $request_param->populate_attributes([
          'name'    => $param_details_parts[0],
          'source'  => $param_details_parts[1],
          'type'    => $param_details_parts[2]
        ]);

      // Assign options
      if($param_options) {
        $request_param->set_param_options($param_options);
      }

      // Retrieve paramter value from superglobal
      $request_param->retrive_value();

      // Check if this is a required parameter and if so, check if the value has been
      if($request_param->is_required() && !$request_param->is_value_set()) {
        throw new Error\MissingParameterError("Parameter $request_param->name->__toString() cannot be found in $request_param->source->__toString() superglobal.");
      }

      // If we've got here, it means that we can now add this parameter to the array
      $this->add_to_parameters($request_param);

    }
    
  }

  /**
   *
   */
  public function add_to_params(RequestParam $param) {
    if(!array_key_exists($param->get_name(), $this->parameters)) {
      $this->parameters[$this->get_name()] = $param;      
    } else {
      // Duplicated parameter
      throw new Error\InvalidParameterError("Parameter is ");
    }
  }

  /**
   *
   */
  public function is_parameter_passed($param_name, $source) {    
    switch($source) {
      case "GET":
        return isset($_GET[$param_name]);
        break;
      case "POST":
        return isset($_POST[$param_name]);
        break;
      default:
        /** @todo Unsupported source - throw exception */
        break;
    }
  }

  /**
   *
   */
  public function get_param($param_name) {

  }


}

/**
 EXAMPLE SERVER REQUEST

     [HTTP_HOST] => dev.digitflow.com
    [HTTP_USER_AGENT] => curl/7.47.0
    [HTTP_ACCEPT] => * /*
    [PATH] => /usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin
    [SERVER_SIGNATURE] => <address>Apache/2.4.18 (Ubuntu) Server at dev.digitflow.com Port 80</address>

    [SERVER_SOFTWARE] => Apache/2.4.18 (Ubuntu)
    [SERVER_NAME] => dev.digitflow.com
    [SERVER_ADDR] => 127.0.0.1
    [SERVER_PORT] => 80
    [REMOTE_ADDR] => 127.0.0.1
    [DOCUMENT_ROOT] => /var/www/digitflow/public
    [REQUEST_SCHEME] => http
    [CONTEXT_PREFIX] => 
    [CONTEXT_DOCUMENT_ROOT] => /var/www/digitflow/public
    [SERVER_ADMIN] => [no address given]
    [SCRIPT_FILENAME] => /var/www/digitflow/public/index.php
    [REMOTE_PORT] => 55278
    [GATEWAY_INTERFACE] => CGI/1.1
    [SERVER_PROTOCOL] => HTTP/1.1
    [REQUEST_METHOD] => GET
    [QUERY_STRING] => 
    [REQUEST_URI] => /
    [SCRIPT_NAME] => /index.php
    [PHP_SELF] => /index.php
    [REQUEST_TIME_FLOAT] => 1478258972.255
    [REQUEST_TIME] => 1478258972
*/