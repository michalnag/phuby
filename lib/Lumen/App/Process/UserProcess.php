<?php

namespace App\Process;

use App\AbstractProcess;
use App\Model\User;
use Helpers\Utils\ObjectUtils;
use App\DBI;
use Logger;

class UserProcess extends AbstractProcess {

  /**
   * This process registers user in the system by performing following steps:
   * ....
   *
   * @param User $user representing user that will be registered
   * @throws Error\MissingAttributeError is one of the required attribute is missing
   */
  public function signup(User &$user) {
    // Check if all required arguments are assigned to the object
    ObjectUtils::check_required_attributes($user, ['uuid', 'email', 'password', 'activation_token']);

    // Once checked, save this user in the database
    // Email should already be unique but we still may encounter the error
    try {
      // Attempt to create user in the DB
      DBI::create_user([
          'uuid' => $user->uuid,
          'email' => $user->email,
          'password' => $user->password,
          'activation_token' => $user->activation_token
        ]);
    
    } catch(\PDOException $e) {
      
      if($e->errorInfo[1] == 1062) {
        // We have a duplicated entry for unique field
        // Let's check which field is duplicated and give relevant feedback to user
        
      
      } else {
        Logger::error($e);
      }
      
      return false;
      
    }

    // Assign luser ID to the object and return it
    $user->poulate_attributes(['id' => DBI::get_last_inserted_id()]);

    Logger::debug("User has been created succesfully with an ID ".$user->id->to_int().". Send a welcome email.");
    
    return true;

  }

}