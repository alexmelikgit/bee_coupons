<form action="" id="orderCoupon">
    <div class="d-flex align-center">
        <div class="input-style coupon-input">
            <input name="code" type="text" tabindex="1"
                    placeholder="<?=__("Կտրոնի կոդը","beekeeping")?>">
        </div>
        <div class="form-btn">
            <button aria-label="Activate" title="Activate"
                    tabindex="2"><?= __('Ակտիվացնել', 'beekeeping'); ?>
                <div class="coupon-preloader preloader">
                    <div class="lds-dual-ring"></div>
                </div>    
            </button>
        </div>
        <div class="err-msg fs-12 c-red fw-600 "></div>

    </div>
</form>
