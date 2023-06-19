<?php 
    $coupon = new coupon();
    $coupons = isset($_GET['type']) && $_GET['type'] == "inactives" ? $coupon->getInactives() : $coupon->getActives();

?>

<div class="wrap">
    <h1 class="wp-heading-inline">Bee coupons</h1>
    <form action="" style="display: inline">
        <input type="text" name="page" value="new_coupon" style="display: none">
        <button class="page-title-action">Add New</button>
    </form>
    <form action="" method="GET" id="couponsType">
        <input type="text" style="display:none" name="page" value="<?=$_GET['page']?>">
        <input type="text" style="display:none" name="type" value="">
        <button value="actives">Active(<?=count($coupon->getActives())?>)</button>
        <button value="inactives">Used (<?=count($coupon->getInactives())?>)</button>
    </form>
    <table id="couponsTable" class="wp-list-table widefat fixed striped coupons-table table-view-list posts">
	<thead>
	<tr>
        <?php foreach($coupon->fields as $title => $value): $title = str_replace("_", " ", $title)?>
        <th scope="col" id="date" class="manage-column column-date sortable asc">
           <span><?=ucwords($title)?></span>
        </th>
        <?php endforeach ; ?>
	</tr>
	</thead>
    <tbody>
    <?php foreach($coupons as $thecoupon) : ?>

    <tr id="<?=$thecoupon['id']?>" class="iedit coupon-row">
        <?php foreach($coupon->fields as $field => $type) : ?>
        <td>
            <?php if(is_array($type)) : $key = array_keys($type) ; $key = $key[0]?>
                <?=ucwords($type[$key][$thecoupon[$field]]); ?>
            <?php else : ?>
            <?=$thecoupon[$field]?>
            <?php endif ; ?>
        </td>
        <?php endforeach ; ?>
    </tr>
    <?php endforeach ; ?>
    
</table>
</div>
