<?php
    class CouponErrors extends Exception{
        public $key;
        function __construct($key, $message){
            $this->key = $key;
            $this->message = $message;
        }
        function getError(){
            $error = [];
            if(is_array($this->key)){
                $error['key'] = [];
                foreach($this->key as $key){
                    $error["key"][] = $key;    
                }
            }else{
                $error = [
                    "key" => $this->key,
                ];
            }
            $error["message"] = $this->message;
            echo json_encode($error);
        }
    }