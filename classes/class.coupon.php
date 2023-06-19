<?php 
@ini_set("display_errors", true);
class Coupon{
    public $fields;
    function __construct($id = null){
      
        $this->fields = [
            "coupon_code" => "text",
            "discount_type" => ["select" => ["fix" => "Fixed", "percent" => "Percent"]],
            "discount" => "number",
            "discount_count" => "number",
            "used_discount" => "number",
            "status" => ["select" => ["consumable" => "consumable", "unconsumable" => "unconsumable"]],
            "email" => 'text',
            "phone" => "text",
        ]; 
        
    }
  
    function init(){
        global $wpdb;
        add_action("admin_menu", [$this, "create_pages"]);        

        $wpdb->query("CREATE TABLE IF NOT EXISTS `bee_coupons`(`id` int AUTO_INCREMENT PRIMARY KEY)");
        $this->set_all_assets();

    }
    function create_pages()
    {
        add_menu_page("Bee coupons", "Bee coupons", "publish_pages", "bee_coupons", [$this, "get_current_page"]);
        
        foreach ($this->get_all_pages() as $value) {
            $page = preg_replace("/(page\-|\.php)/", "", $value);
            $this->add_sub_menu($page, ucfirst($page));
            
        }
        
    }
    function add_sub_menu($menu_slug, $title){

        $titleArray = explode("_", $title);
        $title = "";
        foreach ($titleArray as $value) {
            $title .= ucfirst($value) . " ";
        }
        add_submenu_page("bee_coupons", $title, $title, "publish_pages", $menu_slug, [$this, "get_current_page"]);

    }
    function get_current_page()
    {
        if(isset($_GET['page'])){
            $page = "page-" . $_GET['page'] . ".php";

            require_once(BEECOUPON_DIR ."pages/". $page);
        }
    }
     function get_all_pages(){
        $files = [];
        foreach (scandir(BEECOUPON_DIR . "/pages") as $value) {
            if(preg_match("/^page\-.*\.php/", $value)){
                $files[] = $value;
            }
        }
        return $files;
    }
    function set_all_assets(){
        wp_enqueue_style("coupons", BEECOUPON_URL . "/assets/datatables.css");
        wp_enqueue_style("coupons-datatables", BEECOUPON_URL . "/assets/style.css");
        wp_enqueue_script("coupon-datatables", BEECOUPON_URL . "/assets/datatables.js", [], null, true);
        wp_enqueue_script("coupon-assets", BEECOUPON_URL . "/assets/assets.js", ["coupon-datatables"], null, true);
    }
    function plugin_activation(){
        $this->init();

    }
    function setSql(){
        global $wpdb; 
        $results = $wpdb->get_results("SELECT
                COLUMN_NAME
            FROM
                INFORMATION_SCHEMA.COLUMNS
            WHERE
                TABLE_NAME = 'bee_coupons'");
        $cols = [];
        foreach($results as $result){
            $cols[] = $result->COLUMN_NAME;
        }
        $create = [];
        foreach($this->fields as $field => $type){
            if(!in_array($field, $cols)){
                $create[] = $field;

            }else{
                $key = array_search($field,$cols);
                unset($cols[$key]);
            }
        }
        $id = array_search("id", $cols);
        unset($cols[$id]);
        $sql = "";
        foreach($create as $col){
            $keys = ""; 
            if(is_array($this->fields[$col])){
                $arrayKeys = array_keys($this->fields[$col]);
                $keys = $arrayKeys[0];
            }else{
                $keys = $this->fields[$col];
            }
            $type = "";
            if($keys == "number"){
                $type = "INT";
            }else{
                $type = "VARCHAR(255)";
            }
            $sql .= "ADD `$col` $type,";

        }
        $sql = preg_replace("/\,$/", "" , $sql);

        $wpdb->query("ALTER TABLE `bee_coupons` " . $sql);
        $sql = "";
        foreach($cols as $col){
            $sql = "DROP `$col`,";
        }
        $sql = preg_replace("/\,$/", "" , $sql);
        $wpdb->query("ALTER TABLE `bee_coupons` " . $sql);
    }
    function getcoupons($id = null){
        global $wpdb;
        return($wpdb->get_results("SELECT * FROM `bee_coupons`" . ($id ? " WHERE `id` = $id" : ""), ARRAY_A));
    }
    function getcouponsbyCode(string $code){
        global $wpdb;
        $coupon = $wpdb->get_results("SELECT * FROM `bee_coupons` WHERE `coupon_code` = \"$code\"", ARRAY_A);
        $coupon = $coupon[0] ?? null;
        return $coupon; 
    }
  

    function couponCodeGenerator($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        global $wpdb;
        $exist = $wpdb->get_results("SELECT * FROM `bee_coupons` WHERE `coupon_code` = \"$randomString\"");
        if(count($exist)){
            return $this->couponCodeGenerator($length);
        }
        return $randomString;
    }
    function getActives(){
        global $wpdb;
        return($wpdb->get_results("SELECT * FROM `bee_coupons` WHERE `discount_count` > 0", ARRAY_A));

    }
    function getInactives(){
        global $wpdb;
        return($wpdb->get_results("SELECT * FROM `bee_coupons` WHERE `discount_count` <= 0", ARRAY_A));
    }
    
    static function couponAvaliable(string $code){
        global $wpdb;
        $results = $wpdb->get_results("SELECT `discount_count` FROM `bee_coupons` WHERE `coupon_code` = \"$code\"", ARRAY_A);
        return (int)$results[0]['discount_count'] > 0;
    }
    static function couponUpdateCount(string $code){
        global $wpdb;
        $coupon = $wpdb->get_results("SELECT `discount_count`, `status` FROM `bee_coupons` WHERE `coupon_code` = \"$code\"", ARRAY_A);
        $coupon = $coupon[0]??null;
        if($coupon && $coupon['status'] == "consumable"){
            $count = (int)$coupon['discount_count']-1;
            $wpdb->update("bee_coupons", ["discount_count" => $count], ["coupon_code" =>$code]);
        }
    }
    function doHtml(){
        require(BEECOUPON_DIR . "/classes/beecoupon-html.php");
    }
    static function addToUsed(string $code, $count){
        global $wpdb;
        $coupon = $wpdb->get_results("SELECT * FROM `bee_coupons` WHERE `coupon_code` = \"$code\"", ARRAY_A);
        $coupon = $coupon[0]?? null;
        if($coupon){
            $currentCount = (int)$coupon['used_discount'];
            $count += $currentCount;
            $error=$wpdb->update("bee_coupons", ['used_discount'=> $count], ["coupon_code" => $code]);
        }
    }
}

