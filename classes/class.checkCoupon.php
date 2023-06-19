<?php 

    class CheckCoupon{
        public $coupon, $id, $email, $phone, $coupon_code, $discount, $discount_type,$coupon_type, $maxCount;
        function __construct(string $code, $maxCount = null){
            $coupon = new coupon();
            $this->coupon = $coupon->getcouponsbyCode($code);
            $this->maxCount = $maxCount;
            try{
                $this->check();
            }catch(Exception $e){
                echo $e->getMessage();
            }
        }

        function check(){
            $email = $this->coupon['email']?? null;
            $phone = $this->coupon['phone'] ?? null;
            if(!$this->coupon){
                throw new Exception(__("Կտրոնը գոյություն չունի", "beekeeping"));
            }
            if(strlen($email) || strlen($phone)){
                    $notAllowed = new Exception(__("Դուք չեք կարող օգտագործել այս կտրոնը", "beekeeping"));
                    if(!is_user_logged_in()){
                        throw $notAllowed;
                    }else{
                        $user = wp_get_current_user();
                        if(strlen($email) && $user->user_email != $email){
                            throw $notAllowed;
                        }
                        if(strlen($phone)){
                            $user = get_user_by_tel($phone);
                            $currentUser = wp_get_current_user();
                            if(!$user || ($user && $user->user_email != $currentUser->user_email)){
                                throw $notAllowed;
                            }
                        }
                        
                    }
                    
            }
            if($this->coupon["discount_count"] < 1){
                throw new Exception("Կտրոնն վերջացել է");
            }
            if($this->maxCount && $this->coupon['discount_type'] == "fix"){
                if($this->maxCount < $this->coupon['discount']){
                    throw new Exception(__("Կուպոնի գինը գերազանցում է ընդհանուր գինը", "beekeeping"));
                }
            }
            echo json_encode(["discount" => $this->coupon['discount'], "type" => $this->coupon['discount_type']]);
        }
    }



   