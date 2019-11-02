<?php
namespace system\models;

use Exception;

/**
 * Handle subscriber model functions.
 * NOTE: Catch will only exit. This is just an example. Really it should be write to log or something more meaningful.
 */
class SubscriberModel
{
    private $_db = null;
    private $_stateOptions = ["active", "unsubscribed", "junk", "bounced", "unconfirmed"];
    public $SubscriberFieldModel = null;

    public function __construct($db)
    {
        if (empty($db)) {
            throw new Exception("Failed to initialize class " . \get_class($this) . "!");
        }
        $this->_db = $db; 
        $this->SubscriberFieldModel = new \system\models\SubscriberFieldModel($db); 
    }
  
    /**
     * Make sure data is valid
     *
     * @param Array $data
     * @param Array $errors
     * @return boolean
     */
    public function validate(Array $data, Array &$errors): bool{
        $errors = [];
        //State: Only require state after subscriber has been created
        if(isset($data["id"]) && (!isset($data["state"]) || empty($data["state"])) ){
            $errors["state"] = "State is required!";
        } else if (isset($data["state"]) && !in_array(strtolower($data["state"]), $this->_stateOptions)) {
            $errors["state"] = "State is invalid!";
        }
        //Email
        if(!isset($data["email"]) || empty($data["email"])) {
            $errors["email"] = "Email is required!";
        }else if($this->emailIsDuplicate($data["email"], isset($data["id"])?$data["id"]:0)){
            $errors["email"] = "Duplicate email!";
        }else if(!preg_match('/\@/', $data["email"]) ) {
            $errors["email"] = "Invalid email";
        } else{ 
            list($recipient, $domain) = explode("@", $data["email"]);
            if(empty($domain)){
                $errors["email"] = "Invalid email!";
            }else if(filter_var(gethostbyname($domain), FILTER_VALIDATE_IP) == false){
                $errors["email"] = "Email domain, $domain, is not valid!";
            }  
        }
        //Name
        if(!isset($data["name"]) || empty($data["name"])) {
            $errors["name"] = "Name is required!";
        }
        //Fields
        if (isset($data["fields"]) && count($data["fields"]) > 0) {
            foreach ($data["fields"] as $row) { 
                $errors2 = [];
                $row["subscriber_id"] = isset($data["id"]) ? $data["id"] : 0;
                if (!$this->SubscriberFieldModel->validate($row, $errors2)) {
                    $errors["fields"] = implode("; ", $errors2);
                    break;      //There is an error, no need to check fields anymore
                } 
            }
        }

        return count($errors) > 0 ? false : true;
    }

    /**
     * Insert subscriber & fields. $data keys must match table fields.
     *
     * @param Array $data
     * @return integer
     */
    public function insert(array $data): int
    { 
        try {
            $query = "INSERT INTO subscriber (`state`, email, `name`) VALUES(:state, :email, :name);";
            //Start transaction 
            $this->_db->beginTransaction();
            $statement = $this->_db->prepare($query);
            $statement->execute(["state"=> isset($data["state"])? $data["state"]:"unconfirmed", "email" => $data["email"], "name" => $data["name"]]);
            if ($statement->rowCount() > 0) {  
                $id = $this->_db->lastInsertId();
                //Add fields... if any 
                if(isset($data["fields"]) && count($data["fields"]) > 0){
                    foreach($data["fields"] as $row){      
                        $this->SubscriberFieldModel->insert(array_merge(["subscriber_id" => $id], $row));
                    } 
                }
                $this->_db->commit(); //Commit transaction 
                return $id;
            } 
        } catch (\PDOException $e) {
            exit($e->getMessage()());
        }
        $this->_db->rollback(); //Rollback transaction 
        return 0;
    }

    /**
     * Update subscriber. $data keys must match table fields.
     *
     * @param Array $data
     * @return boolean
     */
    public function update(array $data): bool
    {
        try {
            $query = "UPDATE subscriber SET `state` = :state, email = :email, `name` = :name WHERE id = :id;";
            $statement = $this->_db->prepare($query); 
            $statement->execute(["state" => $data["state"], "email" => $data["email"], "name" => $data["name"], "id" => $data["id"]]);
             
            //Update fields... if any 
            if (isset($data["fields"]) && count($data["fields"]) > 0) {
                foreach ($data["fields"] as $row) { 
                    $this->SubscriberFieldModel->update($row); //Subscriber field is expected to have subscriber ID.
                }
            }
            return true; 
        } catch (\PDOException $e) {
            exit($e->getMessage()());
        }
        return false;
    }

    /**
     * Delete subscriber and all related fields that link to this subscriber.
     * NOTE: There is a trigger that auto delete fields. So no need to manually delete them here.
     * 
     * @param integer $id
     * @return boolean
     */
    public function delete(int $id): bool
    {
        try {
            $query = "DELETE FROM subscriber WHERE id = :id;";
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
     * Find subscribers by email or name 
     *
     * @param string $str
     * @return array
     */
    public function findRecords(string $str): array{
        try {
            $query = "SELECT * FROM subscriber WHERE email LIKE :email OR `name` LIKE :name;";
            $params = ["email" => "%$str%", "name" => "%$str%"];
            $statement = $this->_db->prepare($query);
            $statement->execute($params);
            if ($records = $statement->fetchAll(\PDO::FETCH_ASSOC)) {
                //Get fields... if any 
                for ($i = 0; $i < count($records); $i++) {
                    $records[$i]["fields"] = $this->SubscriberFieldModel->getRecords($records[$i]["id"]);
                }
                return $records;
            }
        } catch (\PDOException $e) {
            exit($e->getMessage()());
        }
        return [];
    }
    /**
     * Get subscribers. Notice no inner join with subscriber_field.
     *  
     * @return Array
     */
    public function getRecords(): array
    { 
        try {
            $query = "SELECT * FROM subscriber;";
            $statement = $this->_db->prepare($query);
            $statement->execute();
            if($records = $statement->fetchAll(\PDO::FETCH_ASSOC)){  
                //Get fields... if any 
                for($i=0; $i<count($records); $i++){
                    $records[$i]["fields"] = $this->SubscriberFieldModel->getRecords($records[$i]["id"]);
                }
                return $records;
            } 
        } catch (\PDOException $e) {
            exit($e->getMessage()());
        }
        return [];
    }

    /**
     * Get subscriber. Notice no inner join with subscriber_field.
     *
     * @param integer $id
     * @return Array
     */
    public function getRecord(int $id): array
    {
        try {
            $query = "SELECT * FROM subscriber WHERE id = :id LIMIT 1;";
            $statement = $this->_db->prepare($query);
            $statement->execute(["id" => $id]);
            if($records = $statement->fetchAll(\PDO::FETCH_ASSOC)){  
                $records[0]["fields"] = $this->SubscriberFieldModel->getRecords($records[0]["id"]);
                return $records[0];
            } 
        } catch (\PDOException $e) {
            exit($e->getMessage()());
        }
        return [];
    }

    /**
     * Check for email duplication. Email is unique.
     *
     * @param string $email
     * @param integer $id
     * @return boolean
     */
    public function emailIsDuplicate(string $email, int $id = 0): bool
    {
        try {
            $query = "SELECT * FROM subscriber WHERE email = :email AND id != :id LIMIT 1;";
            $statement = $this->_db->prepare($query);
            $statement->execute(["email" => $email, "id" => $id]);
            if ($record = $statement->fetchAll(\PDO::FETCH_ASSOC)) {
                return true;
            }
        } catch (\PDOException $e) {
            exit($e->getMessage()());
        }
        return false;
    }
}
