<?php 
namespace system\models;

use Exception;

/**
 * Handle subscriber field model functions.
 * NOTE: Catch will only exit. This is just an example. Really it should be write to log or something more meaningful.
 */
class SubscriberFieldModel{
    private $_db = null;
    private $_typeOptions = ["date", "number", "string", "boolean"];

    public function __construct($db){
        if(empty($db)){
            throw new Exception("Failed to initialize class ".\get_class($this)."!");
        }
        $this->_db = $db;
    }

    /**
     * Undocumented function
     *
     * @param Array $data
     * @param Array $errors
     * @return boolean
     */
    public function validate(Array $data, Array &$errors): bool
    {
        $errors = [];
        //Subscriber ID
        if(isset($data["subscriber_id"]) && !is_numeric($data["subscriber_id"])){
            $errors["subscriber_id"] = "Invalid subscriber ID!";
        }
        //ID
        if (!isset($data["title"]) || empty($data["title"])) {
            $errors["title"] = "Title is required!";
        } 
        //Type
        if (!isset($data["type"]) || empty($data["type"])) {
            $errors["type"] = "Type is required!";
        }else if(!in_array(strtolower($data["type"]), $this->_typeOptions)){
            $errors["type"] = "Type is invalid!";
        }
        //Value 
        if (!isset($data["value"]) || empty($data["value"])) {
            $errors["value"] = "Value is required!";
        }
        return count($errors)>0?false:true;
    }
    
    /**
     * Insert subscriber field. $data keys must match table fields.
     *
     * @param Array $data
     * @return integer
     */
    public function insert(Array $data): int{
        try{
            $query = "INSERT INTO subscriber_field (subscriber_id, title, `type`, `value`) VALUES(:subscriber_id, :title, :type, :value);";
            $params = [
                "subscriber_id" => $data["subscriber_id"], "title" => $data["title"],
                "type" => strtolower($data["type"]), "value" => $data["value"]
            ];
            $statement = $this->_db->prepare($query); 
            $statement->execute($params);
            if($statement->rowCount() > 0){
                return $this->_db->lastInsertId();
            }
        }catch(\PDOException $e){
            exit($e->getMessage()());  
        }
        return 0;
    }

    /**
     * Update subscriber field, except subscriber_id. $data keys must match table fields.
     *
     * @param Array $data
     * @return boolean
     */
    public function update(Array $data): bool{
        try {
            $query = "UPDATE subscriber_field SET title = :title, `type` = :type, `value` = :value WHERE id = :id;";
            $params = [
                "title" => $data["title"], "type" => strtolower($data["type"]), "value" => $data["value"], "id" => $data["id"]
            ];
            $statement = $this->_db->prepare($query);
            $statement->execute($params);
            return true;
        } catch (\PDOException $e) {
            exit($e->getMessage()());
        }
        return false;
    }

    /**
     * Delete subscriber field.
     *
     * @param integer $id
     * @return boolean
     */
    public function delete(int $id): bool{
        try {
            $query = "DELETE FROM subscriber_field WHERE id = :id;";
            $statement = $this->_db->prepare($query);
            $statement->execute(['id' => $id]);
            if ($statement->rowCount() > 0) {
                return true;
            }
        } catch (\PDOException $e) {
            exit($e->getMessage()());
        }
        return false;
    }

    /**
     * Get fields for this subscriber
     *
     * @param integer $subscriber_id 
     * @return Array
     */
    public function getRecords(int $subscriber_id): Array{
        try { 
            $query = "SELECT * FROM subscriber_field WHERE subscriber_id = :subscriber_id;";
            $statement = $this->_db->prepare($query);
            $statement->execute(["subscriber_id" => $subscriber_id]);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage()());
        }
        return [];
    }

    /**
     * Get field
     *
     * @param integer $id
     * @return Array
     */
    public function getRecord(int $id): Array{
        try {
            $query = "SELECT * FROM subscriber_field WHERE id = :id LIMIT 1;";
            $statement = $this->_db->prepare($query);
            $statement->execute(["id" => $id]);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result[0];
        } catch (\PDOException $e) {
            exit($e->getMessage()());
        }
        return [];
    }
}