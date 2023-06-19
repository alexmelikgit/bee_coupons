<?php
    $coupon = new coupon();
    $existcoupon = isset($_GET['edit']) ? $coupon->getcoupons((int)$_GET['edit']) : null;
    $existcoupon = $existcoupon[0] ?? null;
?>
<div class="wrap">
    <h1 class="wp-heading-inline">Add New Coupon</h1>
    
    <div id="post-body-content">
    <div class="postbox">
 
    <form id="<?=isset($_GET['edit']) ? "couponEdit" : "couponCreate"?>" action="" class="beeform" method="POST">

        <div id="poststuff" class="postbox-header">
            <h2>Coupon Parameters</h2>
            <button  class="button button-primary button-large"><?=isset($_GET['edit']) ? "Edit" : "Publish"?></button>
        </div>
        <div class="inside beebody">

            <?php foreach($coupon->fields as $field => $type) : if($field != "used_discount"): $label = preg_replace("/\_/", " ", $field)?>
                <div class="beeinput-style">

                <?php if(is_array($type)) :?>
                    <?php foreach($type as $key => $options) : if($key == "select"):  ?>
                        <label for="<?=$field?>"><?= ucwords($label)?></label>
                        <select name="<?=$field ?>" id="<?=$field?>">
                            <?php foreach($options as $value => $option) : ?>
                                <option value="<?=$value?>" <?=$existcoupon ? ($value == $existcoupon[$field] ? "selected" : null ) : null?>><?=ucwords($option) ?></option>
                            <?php endforeach ; ?>
                        </select>
                <?php endif; endforeach; else : ?>
                    <label for="<?=$field?>"><?=ucwords($label)?></label>
                    <input id="<?=$field?>"type="<?=$type ?? "text"?>" name="<?=$field?>"
                    value="<?= $existcoupon ? $existcoupon[$field] : ($field == "coupon_code" ? $coupon->couponCodeGenerator() : null)?>"
                    >
                    <div class="err-msg"></div>
                
            <?php endif ; ?>
            </div>
            <?php endif; endforeach ; ?>
            <div style="display: none">
                    <input type="text" name="id" value="<?=$_GET["edit"]?>">
            </div>
        </div>
    </form>

    </div>
    
    </div>
    
</div>