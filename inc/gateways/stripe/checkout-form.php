<?php
defined( 'ABSPATH' ) || exit();
?>
<div id="donate-stripe-form">
    <p class="description"><?php _e( 'Pay securely using your credit card.', 'tp-donate' ); ?></p>
    <div class="donate_field">
        <label for="cc-number" class="label-field"><?php _e( 'Card Number', 'tp-donate' ) ?></label>
        <input name="stripe[cc-number]" id="cc-number" type="tel" class="required cc-number" autocomplete="cc-number" placeholder="•••• •••• •••• ••••" required/>
    </div>

    <div class="donate_field">
        <label for="cc-exp" class="label-field"><?php _e( 'Expires (MM / YY)', 'tp-donate' ) ?></label>
        <input name="stripe[cc-exp]" id="cc-exp" type="tel" class="required cc-exp" autocomplete="cc-exp" placeholder="•• / ••" required/>
    </div>
    
    <div class="donate_field">
        <label for="cc-cvc" class="label-field"><?php _e( 'Card Code (CVC)', 'tp-donate' ) ?></label>
        <input name="stripe[cc-cvc]" id="cc-cvc" type="tel" class="required cc-cvc" autocomplete="off" placeholder="•••" required/>
    </div>
    
</div>
