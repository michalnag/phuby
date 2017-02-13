<?php
/**
 * Request
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby\Network
 */

namespace PHuby\Network;

use PHuby\AbstractNetwork;
use PHuby\Logger;
use PHuby\Error;
use PHuby\Network\Request\RequestParam;

class Request extends AbstractNetwork {

  private
    $arr_parameters = [];
   
  /**
   * Method retrieves parameters from GET, POST, FILES superglobals
   * example value: [["id:POST:integer", [, "required" => true ]], [...]]
   * @param mixed[] $params_details Array of Arrays with parameters details 
   * @return boolean true once process is completed
   * @throws \PHuby\Error\MissingParameterError if parameter is required and not found
   */
  public function get_params_from_request(Array $params_details) {
    
    // Iterate over parameters details passed to the method
    foreach($params_details as $param_details) {
      Logger::debug("Processing param details: $param_details[0]");
      $param_details_parts = explode(':', $param_details[0]);
      
      // Check if options have been passed
      $param_options = isset($param_details[1]) ? $param_details[1] : [];

      // Check if the parameter is required
      if(array_key_exists("required", $param_options)) {
        // This parameter is required. Check if the value exists
        if(!$this->is_parameter_passed($param_details_parts[0], $param_details_parts[1])) {
          throw new Error\NetworkRequestError("Required parameter $param_details_parts[0] is missing from the request", Error\NetworkRequestError::EC_MISSING_PARAMETER);
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
      if(!empty($param_options)) {
        $request_param->set_options($param_options);
      }

      // Retrieve paramter value from superglobal
      $request_param->retrive_value();

      // If we've got here, it means that we can now add this parameter to the array
      $this->add_to_params($request_param);

    }

    // Process completed
    return true;
  }


  /**
   * Adds the RequestParam object to the collection of parameters sent in the request
   * @param object RequestParam $obj_param representing an instance of the parameter
   * @return boolean true once paramter is added to parameters array
   * @throws \PHuby\Error\DuplicatedParameterError when parameter is duplicated
   */
  public function add_to_params(RequestParam $obj_param) {
    // Check if the parameter has already been set
    if(!array_key_exists($obj_param->get_name(), $this->arr_parameters)) {
      // Add parameter to parameters collection
      $this->arr_parameters[$obj_param->get_name()] = $obj_param;      
    } else {
      // Duplicated parameter. Throw an exception
      throw new Error\DuplicatedParameterError("Parameter {$obj_param->get_name()} is duplicated.");
    }
  }


  /**
   * Method checks if the parameter is passed in the given superglobal source
   * @param string $str_param_name representing a name of the parameter
   * @param string $str_source representing superglobal source, e.g. GET, POST, FILES
   * @return boolean true if parameter is available in the source, false otherwise
   * @throws \PHuby\Error\InvalidParameterError if unsupported source is passed
   */
  public function is_parameter_passed($str_param_name, $str_source) {
    // Based on the source
    switch(strtoupper($str_source)) {
      case "GET":
        return isset($_GET[$str_param_name]);
        break;
      case "POST":
        return isset($_POST[$str_param_name]);
        break;
      case "FILES":
        return isset($_FILES[$str_param_name]);
        break;
      default:
        throw new Error\InvalidParameterError("Invalid source parameter passed: $str_source");
        break;
    }
  }


  /**
   * Retrieves the parameter object based on the parameter name
   * @param string $str_param_name representing parameter name
   * @return object RequestParam representing requested parameter, null otherwise
   */
  public function get_param($str_param_name) {
    Logger::debug("Retrieving parameter $str_param_name");
    if (array_key_exists($str_param_name, $this->arr_parameters)) {
      return $this->arr_parameters[$str_param_name];      
    } else {
      return null;
    }
  }


  /**
   * Retrieves parameter value based on the parameter name
   * @param string $str_param_name representing requested parameter name
   * @return mixed representing parameter value, null if not found
   */
  public function get_param_value($str_param_name) {
    if ($this->get_param($str_param_name)) {
      return $this->get_param($str_param_name)->get_value();      
    } else {
      return null;
    }
  }

}