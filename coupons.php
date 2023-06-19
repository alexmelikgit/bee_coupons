<?php 
    /*
    Plugin Name: Beekeeping coupons
    Author: Vlad
    Version: 1.0.0
    */


    define("BEECOUPON_DIR", plugin_dir_path(__FILE__));
    define("BEECOUPON_URL", plugin_dir_url(__FILE__));


    require_once(BEECOUPON_DIR . "classes/class.coupon.php"); 
    require_once(BEECOUPON_DIR . "classes/class.couponErrors.php"); 
    require_once(BEECOUPON_DIR . "classes/class.createCoupon.php"); 
    require_once(BEECOUPON_DIR . "classes/class.checkCoupon.php"); 
    require_once(BEECOUPON_DIR . "/coupons-ajax.php"); 
    
    $coupon = new Coupon();
    $coupon->init();
    $coupon->setSql();
    global $wpdb;
    
    register_activation_hook(__FILE__, [$coupon, "init"]);
    register_uninstall_hook(__FILE__, [$coupon, "uninstall"]);
    add_shortcode("bee_coupons", [$coupon, "doHtml"]);
?>
