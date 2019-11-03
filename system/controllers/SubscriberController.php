<?php
namespace system\controllers;

class SubscriberController{ 
    private $SubscriberModel = null;

    public function __construct($db)
    {
        if (empty($db)) {
            throw new Exception("Failed to initialize class " . \get_class($this) . "!");
        } 
        $this->SubscriberModel = new \system\models\SubscriberModel($db); 
    }

    /**
     * This api uses bearer token. If the token matched, then return true.
     *
     * @return boolean
     */
    public function authenticated($headers): bool{
        //$headers = apache_request_headers(); 
        if(isset($headers["Authorization"])){
            $str = $headers["Authorization"];
            if(preg_match("/bearer/i", $str) && preg_match("/". TOKEN ."/", $str)){
                return true;
            }
        }
        return false;
    }

    /**
     * Process the request
     *
     * @param string $method
     * @param integer $id
     * @param string $str
     * @param Array $data
     * @return void
     */
    public function doRequest(string $method, int $id = 0, string $str, Array $data = []){ 
        switch(strtoupper($method)){ 
            case "POST": //Insert record  
                $response = $this->_insert($data);
                break;
            case "PUT": //Update record  
                $data["id"] = $id; //Make sure if the id passed in with the data is the same 
                $response = $this->_update($data);
                break;
            case "DELETE": //Delete record 
                $response = $this->_delete($id);
                break;
            case "GET": //Get record or records 
                if ($id > 0) {
                    $response = $this->_getRecord($id);
                } else if(!empty($str)){
                    $response = $this->_findRecords($str);
                } else {
                    $response = $this->_getRecords();
                }
                break;
            default:
                $response["status"] = "HTTP/1.1 404 Not Found";
                $response["body"] = "Request not found!";
                break; 
        } 
        //Update status and output body contents
        isset($response['status'])?header($response['status']):"";
        echo isset($response['body'])?json_encode($response['body']):""; 
    }

    /**
     * Insert subscriber and fields
     *
     * @param Array $data
     * @return array
     */
    private function _insert(Array $data): array{  
        $response["status"] = "HTTP/1.1 200 OK";
        $errors = [];
        if($this->SubscriberModel->validate($data, $errors) &&  $this->SubscriberModel->insert($data)){
            $response["status"] = "HTTP/1.1 201 Created";
            $response["body"] = "Successful";
        }else if(!empty($errors)){
            $response["body"] = implode("<br/>", $errors);
        }else {
            $response['status'] = 'Failed to create';
        } 
        return $response;
    }

    /**
     * Update subscriber & fields
     *
     * @param Array $data
     * @return array
     */
    private function _update(Array $data): array{
        $response["status"] = "HTTP/1.1 200 OK";
        $errors = [];
        if ($this->SubscriberModel->validate($data, $errors) &&  $this->SubscriberModel->update($data)) { 
            $response["body"] = "Successful";
        } else if (!empty($errors)) {
            $response["body"] = implode("<br/>", $errors);
        } else {
            $response['status'] = 'Failed to update';
        }
        return $response;
    }

    /**
     * Delete subscriber & fields
     *
     * @param integer $id
     * @return array
     */
    private function _delete(int $id): Array{
        $response["status"] = "HTTP/1.1 200 OK";
        if ($this->SubscriberModel->delete($id)) {
            $response["body"] = "Successful";
        }else{
             $response["body"] = "Failed";
        }
        return $response;
    }

    /**
     * Get a subscriber by ID
     *
     * @param integer $id
     * @return array
     */
    private function _getRecord(int $id): array{
        $response["status"] = "HTTP/1.1 200 OK";
        if($record = $this->SubscriberModel->getRecord($id)){
            $response["body"] = $record;
        }
        return $response;
    }

    /**
     * Get all subscribers & fields
     *
     * @return array
     */
    private function _getRecords(): array{
        $response["status"] = "HTTP/1.1 200 OK";
        if ($records = $this->SubscriberModel->getRecords()) {
            $response["body"] = $records;
        }
        return $response;
    }

    /**
     * Find a subscriber by name or email
     *
     * @param string $str
     * @return array
     */
    private function _findRecords(string $str): array{
        $response["status"] = "HTTP/1.1 200 OK";
        if ($records = $this->SubscriberModel->findRecords($str)) {
            $response["body"] = $records;
        }
        return $response;
    }
}