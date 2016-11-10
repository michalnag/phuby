<?php

namespace App;

use PDO;
use Config;
use Logger;

class DBI {
   
  /** @var PDO object containing a connection do the db */
  private static
    $dbh,
    $last_inserted_id,
    $affected_rows;

  public static function connect_to_db(\stdClass $db_details = null) {
    // If there are no DB details passed then we fall back to default
    if(!$db_details) {
      $db_details = Config::get_data("db:default");
    }

    try {
      self::set_dbh(
          new PDO(
          "mysql:host=".$db_details->host
          .";dbname="
          .$db_details->db_name, 
          $db_details->user, 
          $db_details->pass,
          [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8MB4'"
          ]
        )
      );
      Logger::debug("Connection with the database established");
      return true;
    } catch(PDOException $exception) {
      throw new Error\DBIError($exception->getMessage() , $exception->getCode());
    }    
  }

  private static function check_connection() {
    if(!self::$dbh) {
      self::connect_to_db();
    }
  }


  /**
   * Sets an instance of the PDO to the dbh
   *
   * @param PDO $dbh an instance of the PDO object
   * @return boolean true once $dbh is set
   */
  public function set_dbh(PDO $dbh) {
    self::$dbh = $dbh;
    return true;
  }

  public static function get_dbh() {
    return self::$dbh;
  }

  public static function query($sql, Array $vars = null) {
    self::check_connection();
    if($sql) {

      Logger::debug("Preparing SQL statement $sql");
      $sth = self::$dbh->prepare($sql);

      try {  
        // Check if any variables have been passed          
        if($vars) {
          Logger::debug("Executing $sql with following variables: " . Logger::array_to_string($vars));
          $sth->execute($vars);
        } else {
          Logger::debug("Executing $sql without variables.");
          $sth->execute();
        }
      } catch(\PDOException $e) {
        Logger::error($e);
        throw $e;
      }

      self::$last_inserted_id = self::$dbh->lastInsertId();
      self::$affected_rows = $sth->rowCount();
      Logger::debug("Statement last inserted id: ".self::$last_inserted_id.", row count: ".self::$affected_rows);
      return $sth;
    } else {
      throw new Error\DBIError("SQL is not provided");
    }
  }

  public function query_and_fetch($sql, Array $vars = null) {
    if($sql) {
      $sth = $this->query($sql, $vars);
      return $sth->fetchAll(PDO::FETCH_ASSOC);
    } else {
      throw new Error\DBIError("SQL is not provided");
    }
  }

  public function disconnect_from_db() {
    Logger::debug("Disconnecting from the database");
    self::$dbh = false;
    return true;
  }

  public function get_last_inserted_id() {
    Logger::debug("Returning last inserted ID ".self::$last_inserted_id);
    return self::$last_inserted_id;
  }

  public function get_affected_rows() {
    Logger::debug("Returning affected rows ".self::$affected_rows);
    return self::$affected_rows;
  }
  
}