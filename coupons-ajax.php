<?php 
    add_action("wp_ajax_beecoupon_create", "beecoupon_create");

    function beecoupon_create(){
        @ini_set("display_errors", true);
        $data = file_get_contents("php://input");
        $data = json_decode($data);
        $coupon = new CreateCoupon($data);
        if($_GET['type'] == "update"){
            try{
                $coupon->update($data->id);

            }catch(CouponErrors $e){
                $e->getError();
            }
        }else{
            try{
                $coupon->create();

            }catch(CouponErrors $e){
                $e->getError();
            }
        }
        wp_die();
    }

    add_action("wp_ajax_nopriv_coupon_check", "coupon_check");
    add_action("wp_ajax_coupon_check", "coupon_check");

    function coupon_check(){
        $code = file_get_contents("php://input");
        $code = strlen($code) ? json_decode($code, 1) : "";
        $coupon = new CheckCoupon($code["coupon"], $code['maxPrice']);
        
        wp_die();
    }
    