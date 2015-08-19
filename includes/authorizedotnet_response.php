<?php session_start(); //echo TEMPL_PAYMENT_FOLDER_PATH;exit;?>
<div class="wrapper" >
	<div class="clearfix container_message" align="center">
		<h1 class="head2"><?php echo "Payment Processing for Authorize.net, Please wait...";?></h1>
	</div>
<?php
/*  Demonstration on using authorizenet.class.php.  This just sets up a
*  little test transaction to the authorize.net payment gateway.  You
*  should read through the SIM documentation at authorize.net to get
*  some familiarity with what's going on here.  You will also need to have
*  a login and password for an authorize.net SIM account and PHP with SSL and
*  curl support.
*
*  Reference http://www.authorize.net/support/SIM_guide.pdf for details on
*  the SIM API.
*/
global $General, $Cart, $payable_amount,$post_title,$last_postid,$trans_id;
$paymentOpts = templatic_get_payment_options($_REQUEST['paymentmethod']);
$gateway_mode = $paymentOpts['authorize_mode'];
$accountid = $paymentOpts['accountid'];
$returnUrl = $paymentOpts['returnUrl'];
if($merchantid == '')
{
	$merchantid = '1303908';
}
$ipnfilepath = $paymentOpts['ipnfilepath'];
if($ipnfilepath == '')
{
	$ipnfilepath = site_url()."/?ptype=notifyurl&pmethod=2co";
}
$currency_code = templatic_get_currency_type();
$post = get_post($last_postid);
$post_title = $post->post_title;
$post_desc = $post->post_content;
$user_info = get_userdata($post->post_author);
$address1 = get_post_meta($post->post_author,'address');
$address2 = get_post_meta($post->post_author,'area');
$country = get_post_meta($post->post_author,'add_country');
$state = get_post_meta($post->post_author,'add_state');
$city = get_post_meta($post->post_author,'add_city');
if(!defined('TEMPLATIC_AUTHORIZE_DIR')) {
	define('TEMPLATIC_AUTHORIZE_DIR', dirname(__FILE__));
}
//echo TEMPLATIC_AUTHORIZE_DIR;
require_once(TEMPLATIC_AUTHORIZE_DIR . '/authorizedotnet.class.php');
require_once(TEMPLATIC_AUTHORIZE_DIR . '/php_sdk/authorizedotnet.php'); // Include the SDK you downloaded in Step 2
$api_login_id = $paymentOpts['loginid'];
$transaction_key = $paymentOpts['transkey'];
$amount = $payable_amount;
$fp_timestamp = time();
$fp_sequence = "ECOM" . time(); // Enter an invoice or other unique number.
$fingerprint = AuthorizeNetSIM_Form::getFingerprint($api_login_id,$transaction_key, $amount, $fp_sequence, $fp_timestamp);

$returnUrl = apply_filters('tmpl_returnUrl',site_url()."/?page=authorizedotnet_success&paydeltype=authorizedotnet&pid=".$last_postid."&trans_id=".$trans_id);
$cancel_return = apply_filters('tmpl_cancel_return',site_url());

//$a->add_field('x_card_num', '4007000000027');   // test successful visa
//$a->add_field('x_card_num', '370000000000002');   // test successful american express
//$a->add_field('x_card_num', '6011000000000012');  // test successful discover
//$a->add_field('x_card_num', '5424000000000015');  // test successful mastercard
// $a->add_field('x_card_num', '4222222222222');    // test failure card number
if( @$gateway_mode == 1 ){
?>
	<form  name="frm_payment_auth"  method='post' action="https://test.authorize.net/gateway/transact.dll">
<?php 
}else{
?>
	<form  name="frm_payment_auth"  method='post' action="https://secure.authorize.net/gateway/transact.dll">
<?php
}
?>
<input type="hidden" name="x_response_code" value="1"/>
<input type="hidden" name="x_response_subcode" value="1"/>
<input type="hidden" name="x_response_reason_code" value="1"/>
<input type="hidden" name="x_response_reason_text" value="This transaction has been approved."/>
<input type='hidden' name="x_recurring_billing" value="TRUE" />
<input type='hidden' name="x_login" value="<?php echo $api_login_id; ?>" />
<input type='hidden' name="x_email" value="<?php echo $user_info->user_login; ?>" />
<input type='hidden' name="x_Description" value="<?php echo $post_title ; ?>" />
<input type='hidden' name="x_first_name" value="<?php echo $user_info->first_name; ?>" />
<input type='hidden' name="x_last_name" value="<?php echo $user_info->last_name; ?>" />
<input type='hidden' name="x_phone" value="<?php echo $userInfo['user_phone']; ?>" />
<input type='hidden' name="x_country" value="<?php echo $userInfo['user_country']; ?>" />
<input type='hidden' name="x_zip" value="<?php echo  $userInfo['user_postalcode']; ?>" />
<input type='hidden' name="x_state" value="<?php echo $userInfo['user_state']; ?>" />
<input type='hidden' name="x_city" value="<?php echo $userInfo['user_city']; ?>" />
<input type='hidden' name="x_address" value="<?php echo $address; ?>" />
<input type='hidden' name="x_fp_hash" value="<?php echo $fingerprint; ?>" />
<input type='hidden' name="x_amount" value="<?php echo $payable_amount; ?>" />
<input type='hidden' name="x_fp_timestamp" value="<?php echo $fp_timestamp; ?>" />
<input type='hidden' name="x_fp_sequence" value="<?php echo $fp_sequence; ?>" />
<INPUT TYPE="HIDDEN" name="x_test_request" VALUE="FALSE">
<input type='hidden' name="x_version" value="3.1">
<input type='hidden' name="x_show_form" value="payment_form">
<INPUT TYPE='hidden' name="x_receipt_link_method" VALUE="LINK">
<INPUT TYPE='hidden' name="x_receipt_link_text" VALUE="Click here to complete your transaction">
<INPUT TYPE='hidden' name="x_receipt_link_URL" VALUE="<?php echo $returnUrl; ?>">
<INPUT TYPE='hidden' NAME="x_cust_id" value="<?php echo $buser_shipping_lname.$last_postid; ?>">
<input type='hidden' name="x_invoice_num" value="<?php echo $last_postid; ?>">
<input type='hidden' name="x_cancel_url_text" value="Click here to cancel your transaction">
<input type='hidden' name="x_cancel_url" value="<?php echo $cancel_return; ?>">

<input type='hidden' name="x_ship_to_zip" value="<?php if($user_spostalcode !=""){ echo $user_spostalcode; }else{ echo $user_bpostalcode; } ?>">
<input type='hidden' name="x_ship_to_country" value="<?php if($user_scountry !=""){ echo $user_scountry; }else{ echo $user_bcountry; }  ?>">
<input type='hidden' name="x_ship_to_state" value="<?php if($user_sstate !=""){ echo $user_sstate; }else{ echo $user_bstate; } ?>">
<input type='hidden' name="x_ship_to_city" value="<?php if($user_scity !=""){ echo $user_scity; }else{ echo $user_bcity; }  ?>">
<input type='hidden' name="x_ship_to_address" value="<?php if($user_saddr !=""){ echo $user_saddr; }else{ echo $address; } ?>">
<input type='hidden' name="x_ship_to_first_name" value="<?php if($buser_shipping_fname != ""){ echo $buser_shipping_fname; }else{ echo $user_fname; }  ?>">
<input type='hidden' name="x_ship_to_last_name" value="<?php if($buser_shipping_lname != ""){ echo $buser_shipping_lname; }else{ echo $user_lname; }  ?>">

<input type='hidden' name="x_method" value="<?php echo $_REQUEST['authorize_cc_type']; ?>">
<input type='hidden' name="x_card_num" value="<?php echo $_REQUEST['authorize_cc_number']; ?>">
<input type='hidden' name="x_exp_date" value="<?php echo $_REQUEST['authorize_cc_month'].substr($_REQUEST['authorize_cc_year'],2,strlen($_REQUEST['authorize_cc_year'])); ?>">
</form>


<?php
   $errors = array();
	
    if ($_POST)
    {
        $credit_card           = sanitize($_REQUEST['authorize_cc_number']);
        $expiration_month      = (int) sanitize($_POST['authorize_cc_month']);
        $expiration_year       = (int) sanitize(substr($_REQUEST['authorize_cc_year'],2,strlen($_REQUEST['authorize_cc_year'])));
        $cvv                   = sanitize($_POST['authorize_cv2']);
        $cardholder_first_name = sanitize($display_name);
        $cardholder_last_name  = sanitize('');

        $email                 = sanitize($user_email);
       
		/* if($credit_card == ""){
		echo "<script>alert('Please enter a credit card number'); location.href='".site_url()."/?page=preview&aneterror=1';</script>";
		}
        if (!validateCreditcard_number($credit_card))
        {
            $errors['credit_card'] = "Please enter a valid credit card number";
			echo "<script>alert('Please enter a valid credit card number'); location.href='".site_url()."/?page=preview&aneterror=1';</script>";
        }
        
        if (!validateCVV($credit_card, $cvv))
        {
            $errors['cvv'] = "Please enter the security code (CVV number) for your credit card";
			echo "<script>alert('Please enter the security code (CVV number) for your credit card'); location.href='".site_url()."/?page=preview&aneterror=1';</script>";
        }
        if (empty($cardholder_first_name))
        {
            $errors['cardholder_first_name'] = "Please provide the card holder's first name";
			echo "<script>alert('Please provide the card holder's first name'); location.href='".site_url()."/?page=preview&aneterror=1';</script>";
        } */
        

        // If there are no errors let's process the payment
        if (count($errors) != 0 || $errors !="")
        { ?>
		<script>
			setTimeout("document.frm_payment_auth.submit()",20); 
		</script> 
 <?php       }
    }

    function sanitize($value)
    {
        return trim(strip_tags($value));
    }

    function validateCreditcard_number($credit_card_number)
    {
        $firstnumber = substr($credit_card_number, 0, 1);

        switch ($firstnumber)
        {
            case 3:
                if (!preg_match('/^3\d{3}[ \-]?\d{6}[ \-]?\d{5}$/', $credit_card_number))
                {
                    return false;
                }
                break;
            case 4:
                if (!preg_match('/^4\d{3}[ \-]?\d{4}[ \-]?\d{4}[ \-]?\d{4}$/', $credit_card_number))
                {
                    return false;
                }
                break;
            case 5:
                if (!preg_match('/^5\d{3}[ \-]?\d{4}[ \-]?\d{4}[ \-]?\d{4}$/', $credit_card_number))
                {
                    return false;
                }
                break;
            case 6:
                if (!preg_match('/^6011[ \-]?\d{4}[ \-]?\d{4}[ \-]?\d{4}$/', $credit_card_number))
                {
                    return false;
                }
                break;
            default:
                return false;
        }

        $credit_card_number = str_replace('-', '', $credit_card_number);
        $map = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 0, 2, 4, 6, 8, 1, 3, 5, 7, 9);
        $sum = 0;
        $last = strlen($credit_card_number) - 1;
        for ($i = 0; $i <= $last; $i++)
        {
            $sum += $map[$credit_card_number[$last - $i] + ($i & 1) * 10];
        }
        if ($sum % 10 != 0)
        {
            return false;
        }

        return true;
    }

    function validateCreditCardExpirationDate($month, $year)
    {
        if (!preg_match('/^\d{1,2}$/', $month))
        {
            return false;
        }
        else if (!preg_match('/^\d{4}$/', $year))
        {
            return false;
        }
        else if ($year < date("Y"))
        {
            return false;
        }
        else if ($month < date("m") && $year == date("Y"))
        {
            return false;
        }
        return true;
    }

    function validateCVV($cardNumber, $cvv)
    {
        $firstnumber = (int) substr($cardNumber, 0, 1);
        if ($firstnumber === 3)
        {
            if (!preg_match("/^\d{4}$/", $cvv))
            {
                return false;
            }
        }
        else if (!preg_match("/^\d{3}$/", $cvv))
        {
            return false;
        }
        return true;
    }
if (count($errors) === 0)
{
    // Format the expiration date
    $expiration_date = sprintf("%04d-%02d", $expiration_year, $expiration_month);

    // Include the SDK

    // Process the transaction using the AIM API
    $transaction = new AuthorizeNetAIM;
    $transaction->setSandbox(AUTHORIZENET_SANDBOX);
    $transaction->setFields(
        array(
        'amount' => '1.00',
        'card_num' => $credit_card,
        'exp_date' => $expiration_date,
        'first_name' => $cardholder_first_name,
        'last_name' => $cardholder_last_name,
        'address' => $billing_address,
        'city' => $billing_city,
        'state' => $billing_state,
        'zip' => $billing_zip,
        'email' => $email,
        'card_code' => $cvv,
        'ship_to_first_name' => $recipient_first_name,
        'ship_to_last_name' => $recipient_last_name,
        'ship_to_address' => $shipping_address,
        'ship_to_city' => $shipping_city,
        'ship_to_state' => $shipping_state,
        'ship_to_zip' => $shipping_zip,
        )
    );
    $response = $transaction->authorizeAndCapture();
	if ($response->approved)
    {
        // Transaction approved. Collect pertinent transaction information for saving in the database.
        $transaction_id     = $response->transaction_id;
        $authorization_code = $response->authorization_code;
        $avs_response       = $response->avs_response;
        $cavv_response      = $response->cavv_response;

        // Put everything in a database for later review and order processing
        // How you do this depends on how your application is designed
        // and your business needs.

        // Once we're finished let's redirect the user to a receipt page
        header('Location: thank-you-page.php');
        exit;
    }
    else if ($response->declined)
    {
        // Transaction declined. Set our error message.
        $errors['declined'] = 'Your credit card was declined by your bank. Please try another form of payment.';
    }
    else
    {
        // And error has occurred. Set our error message.
        $errors['error'] = 'We encountered an error while processing your payment. Your credit card was not charged. Please try again or contact customer service to place your order.';
    }
}
?>