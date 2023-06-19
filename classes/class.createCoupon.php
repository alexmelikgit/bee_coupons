<?php 
    class CreateCoupon{
        private $fields;
        private $needData;
        function __construct($data){
            $this->fields = (array)$data;
            $this->needData = ["coupon_code", "discount_type", "discount", "discount_count"];
        }

        function doValidate(){
            $emptyData = [];
            $validateData = [];
            foreach($this->fields as &$fields){
                $fields = trim($fields);
            }
            foreach($this->needData as $needle){
                if(!array_key_exists($needle, $this->fields) || !strlen(trim($this->fields[$needle]))){
                    $emptyData[] = $needle;
                }

            }
            if(array_key_exists("email", $this->fields) && strlen($this->fields['email'])){
                if(!preg_match("/[a-z0-9]+@[a-z]+\.[a-z]{2,3}/", $this->fields['email'])){
                    $validateData[] = "email";
                }
            }
            if(array_key_exists("phone", $this->fields) && strlen($this->fields['phone'])){
                if(!preg_match("/^((0|\+?374)(47|97|91|99|96|43|33|79|55|95|41|44|66|50|93|94|77|98)[0-9]{6}$|\+(?!374)[0-9].)/", $this->fields['phone'])){
                    $validateData[] = "phone";
                }
            }
            if(count($emptyData)){
                throw new couponErrors($emptyData, "this field could't to be empty");
            }
            if(count($validateData)){
                throw new couponErrors($validateData, "this field is not valid");
            }
            $coupon = new coupon();
            if($coupon->getcouponsbyCode($this->fields['coupon_code'])){
                throw new couponErrors("coupon_code", "exists");
            }
        }
        function create(){
            try{
                $this->doValidate();
                $id = $this->doCreate();
                echo $id;
            }catch(couponErrors $e){
                $e->getError();
            }
        }
        private function doCreate(){
            global $wpdb;
            $cols = "";
            $values = [];
            $dataType = null;
            foreach($this->fields as $field => &$value){
                $cols .= "$field,";
                $dataType = $wpdb->get_results("SELECT
                        DATA_TYPE
                    FROM
                        INFORMATION_SCHEMA.COLUMNS
                    WHERE
                        TABLE_NAME = 'bee_coupons'
                    AND COLUMN_NAME = '$field'");
                $dataType = $dataType[0]->DATA_TYPE;
                if($dataType == "int"){
                    $value = (int)$value ;
                }
            }
            $cols = preg_replace("/\,$/", "" , $cols);
            $values = preg_replace("/\,$/", "" , $values);
            $exist = $wpdb->get_results("SELECT `coupon_code` FROM `bee_coupons` WHERE `coupon_code` = \"".$this->fields['coupon_code']."\"");
            if(count($exist)){
                throw new couponErrors("global", "This coupon code is exist");
            }
            $wpdb->insert('bee_coupons', $this->fields);
            return $wpdb->insert_id;
            
        }
        function update($id){
            global $wpdb;
            $coupon = new coupon();
            $thecoupon = $coupon->getcouponsbyCode($this->fields['coupon_code']);
            if($thecoupon && $thecoupon['id'] != $this->fields['id']){
                throw new couponErrors("coupon_code", "This coupon code is already exists");
            }
            $error = $wpdb->update("bee_coupons",$this->fields, ['id' => $id]);

            if($error === false){
                throw new couponErrors("global", "Database Error");
            }else{
                echo $id;

            }
        }
    }