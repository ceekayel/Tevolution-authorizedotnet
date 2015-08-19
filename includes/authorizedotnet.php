<table class="table" id="authorizedotnetoptions" style="display:none">
	<tr id="cardholder_name_tr">
		<td class="row3" width="150"><?php _e("Card Holder Name :",DOMAIN); ?></td>
		<td class="row3"><input type="text" value="" id="cardholder_name" name="cardholder_name" class="form_row"/>
        <span class="payment_error"></span>
        </td>
	</tr>
	<tr id="cc_type_tr">
		<td class="row3"><?php _e("Card Type : ",DOMAIN);?></td>
		<td class="row3"><select class="form_row" id="cc_type" name="authorize_cc_type" >
			<option value=""><?php _e("-- select card type --",DOMAIN);?></option>
			<option value="VISA"><?php _e("Visa",DOMAIN);?></option>
			<option value="AMEX"><?php _e("American Express",DOMAIN);?></option>
			<option value="DISC"><?php _e("Discover",DOMAIN);?></option>
			<option value="DELTA"><?php _e("Visa Delta",DOMAIN);?></option>
			<option value="UKE"><?php _e("Visa Electron",DOMAIN);?></option>
			<option value="MC"><?php _e("Master Card",DOMAIN);?></option></select>
            <span class="payment_error"></span>
		</td>
    </tr>
    <tr id="cc_number_tr">
      <td class="row3"><?php _e("Credit/Debit Card number : ",DOMAIN);?></td>
      <td class="row3"><input type="text" autocomplete="off" size="25" maxlength="25" id="cc_number" name="authorize_cc_number" class="form_row"/><span class="payment_error"></span></td>
    </tr>
    <tr id="cc_month_tr">
      <td class="row3"><?php _e("Expiry Date :",DOMAIN);?> </td>
      <td class="row3"><select class="form_row" id="cc_month" name="authorize_cc_month">
          <option selected="selected" value=""><?php _e("month",DOMAIN);?></option>
          <option value="01"><?php _e("01",DOMAIN);?></option>
          <option value="02"><?php _e("02",DOMAIN);?></option>
          <option value="03"><?php _e("03",DOMAIN);?></option>
          <option value="04"><?php _e("04",DOMAIN);?></option>
          <option value="05"><?php _e("05",DOMAIN);?></option>
          <option value="06"><?php _e("06",DOMAIN);?></option>
          <option value="07"><?php _e("07",DOMAIN);?></option>
          <option value="08"><?php _e("08",DOMAIN);?></option>
          <option value="09"><?php _e("09",DOMAIN);?></option>
          <option value="10"><?php _e("10",DOMAIN);?></option>
          <option value="11"><?php _e("11",DOMAIN);?></option>
          <option value="12"><?php _e("12",DOMAIN);?></option>
        </select>
        <select class="form_row" id="cc_year" name="authorize_cc_year">
          <option selected="selected" value=""><?php _e("year",DOMAIN);?></option>
          <?php for($y=date('Y');$y<date('Y')+5;$y++){?>
          <option value="<?php echo $y;?>"><?php echo $y;?></option>
          <?php }?>
        </select>
        <span class="payment_error"></span>
      </td>
    </tr>
    <tr id="cv2_tr">
      <td class="row3"><?php _e("CV2 (3 digit security code) : ",DOMAIN);?></td>
      <td class="row3"><input type="text" autocomplete="off" size="4" maxlength="4" id="cv2" name="authorize_cv2" class="form_row"/><span class="payment_error"></span></td>
    </tr>
  </table>