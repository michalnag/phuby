<?php
/**
 * AbstractDBI
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby
 */

namespace PHuby;

use PDO;
use PHuby\Config;
use PHuby\Logger;
use PHuby\Error;
use PHuby\Error\DBIError;
use PHuby\Helpers\Utils\ArrayUtils;

abstract class AbstractDBI {
   
  /** @var PDO object containing a connection do the db */
  private static
    $dbh,
    $last_inserted_id,
    $affected_rows;

  const
    MYSQL_EC_DUPLICATE_ENTRY      = 1062;

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
      throw new DBIError($exception->getMessage() , $exception->getCode());
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

  public static function query($sql, array $vars = null) {
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

        switch($e->errorInfo[1]) {
          case self::MYSQL_EC_DUPLICATE_ENTRY:
            throw new DBIError\DBIDuplicateEntryError($e->getMessage());

          default:
            throw new DBIError($e->getMessage());
        }
      }

      self::$last_inserted_id = self::$dbh->lastInsertId();
      self::$affected_rows = $sth->rowCount();
      Logger::debug("Statement last inserted id: ".self::$last_inserted_id.", row count: ".self::$affected_rows);
      return $sth;
    } else {
      throw new DBIError("SQL is not provided");
    }
  }

  public function query_and_fetch($sql, array $vars = null) {
    if($sql) {
      $sth = self::query($sql, $vars);
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

  // DEFAULT ACTIONS AND HELPERS
  protected static function create_values_placeholders_from_arrays(array $arr_data) {
    $arr_str_parts = [];
    foreach ($arr_data as $arr_record) {
      $str_values = "(";
      $arr_values = [];
      foreach($arr_record as $key => $value) {
        $arr_values[] = "?";
      }
      $str_values .= join(', ', $arr_values);
      $str_values .= ")";

      $arr_str_parts[] = $str_values;
    }

    return join(', ', $arr_str_parts);
  }

  protected static function get_values_from_arrays(array $arr_data) {
    $arr_values = [];
    foreach($arr_data as $arr_record) {
      foreach($arr_record as $key => $value) {
        $arr_values[] = $value;
      }
    }
    return $arr_values;
  }

  protected static function array_to_query_args(array $arr_data) {
    $arr_query_args = [];
    foreach($arr_data as $str_field => $value) {
      $arr_query_args[$str_field] = $value;
    }
    return $arr_query_args;
  }

  protected static function array_to_query_update_sets(array $arr_field_names, array $arr_exclude_keys) {
    $arr_pairs = [];
    foreach ($arr_field_names as $str_field_name) {
      if (!array_key_exists($str_field_name, array_flip($arr_exclude_keys))) {
        $arr_pairs[] = "$str_field_name = :$str_field_name";
      }
    }
    return join(', ', $arr_pairs);
  }

  protected static function array_to_insert_args(array $arr_field_names) {
    return join(', ', $arr_field_names);
  }

  protected static function array_to_insert_placeholders(array $arr_field_names) {
    return ":" . join(', :', $arr_field_names);
  }

  protected static function default_insert(array $arr_required_fields, array $arr_data, $str_table_name) {
    if (!ArrayUtils::keys_exist($arr_required_fields, $arr_data)) {
      throw new Error\MissingAttributeError(__METHOD__ . " is missing required attributes");
    }
    $q = "INSERT INTO $str_table_name (".self::array_to_insert_args($arr_required_fields).") VALUES (".self::array_to_insert_placeholders($arr_required_fields).")";
    self::query($q, self::array_to_query_args($arr_data));
    return self::get_last_inserted_id();
  }

  protected static function default_update(array $arr_required_fields, array $arr_data, $str_table_name, $str_key_value) {
    if (!ArrayUtils::keys_exist($arr_required_fields, $arr_data)) {
      throw new Error\MissingAttributeError(__METHOD__ . " is missing required attributes. Expecting: " . json_encode($arr_required_fields) . " . Got " . json_encode(array_keys($arr_data)));
    }
    $q = "UPDATE $str_table_name SET ".self::array_to_query_update_sets($arr_required_fields, [$str_key_value])." WHERE $str_key_value = :$str_key_value LIMIT 1";
    self::query($q, self::array_to_query_args($arr_data));
    return self::get_affected_rows() == 1;
  }

  protected static function default_delete($str_table_name, $arr_param) {
    $q = "DELETE FROM $str_table_name WHERE " . array_keys($arr_param)[0] . " = :" . array_keys($arr_param)[0];
    self::query($q, $arr_param);
    return self::get_affected_rows();
  }

  
}