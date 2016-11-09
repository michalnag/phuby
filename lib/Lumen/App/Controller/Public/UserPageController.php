<?php

namespace App\Controller\Public;

use App\Controller\PageCommon;
use Logger;
use Network\Request;
use App\Model\User;
use Attribute\PasswordAttr;
use Attribute\UUIDAttr;
use Process\UserProcess;

class UserPageController extends PageCommon {
    
  protected $template = "public/pages/user_page.twig";
  public $template_vars = [];

  public function post() {
    try {
      $request = new Request();
      $request->get_params_from_request([
        [ "email:POST:String", [ "required" => true ] ],
        [ "password:POST:String", [ "required" => true ] ]
      ]);
    } catch(Error\MissingParameterError $e) {
      Logger::error($e);
      http_response_code(400);
      return false;
    }

    // Create new user
    try {
      $user = new User();
      $user->populate_attributes([
          "email" => $request->get_param("email"),
          "password" => PasswordAttr::hash_password($request->get_param("password")),
        ]);
      
      $user->generate_uuid();
      $user->generate_activation_token();

    } catch(Error\InvalidAttributeError $e) {
      Logger::error($e);
      http_response_code(400);
      return false;
    }    

    Logger::debug("New user will be created. Starting User signup process");

    $user_process = new UserProcess();
    $user_process->signup($user);

  }



}