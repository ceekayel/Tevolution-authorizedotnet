<?php
$paymentmethodname = 'authorizedotnet'; 
if(strtolower($_REQUEST['install'])== strtolower($paymentmethodname))
{
	$payOpts = array();
	$paymethodinfo = array();
	$payOpts = array();
	//	supported input types text,checkbox and radio for radio button options use extra parameter "options" eg.( "options" =>	array('Male','Female')) if you leave type 
	//	parameter blank then we automaticaly consider input type text.
	$payOpts[] = array(
					"title"			=>	"Use Authorize.net in test mode?",
					"fieldname"		=>	"authorize_mode",
					"type"			=>  'checkbox',
					"value"			=>	"1",
					"description"	=>	__('Check this if you want to use Authorize.net in test mode.')
					);
	$payOpts[] = array(
					"title"			=>	"Login ID",
					"fieldname"		=>	"loginid",
					"type"			=>  'text',
					"value"			=>	"yourname@domain.com",
					"description"	=>	__('Example')." : yourname@domain.com"
					);
	$payOpts[] = array(
					"title"			=>	"Transaction Key",
					"fieldname"		=>	"transkey",
					"type"			=>  'text',
					"value"			=>	"1234567890",
					"description"	=>	__('Example')." : 1234567890",
					);
					
	$paymethodinfo = array(
						"name" 		=> 'Authorize.net',
						"key" 		=> $paymentmethodname,
						"isactive"	=>	'1', // 1->display,0->hide
						"display_order"=>'3',
						"payOpts"	=>	$payOpts,
						);
	
	update_option("payment_method_$paymentmethodname", $paymethodinfo );
	$install_message = __("Payment Method integrated successfully");
	$option_id = $wpdb->get_var("select option_id from $wpdb->options where option_name like \"payment_method_$paymentmethodname\"");
	wp_redirect("admin.php?page=monetization&tab=payment_options");
}elseif($_REQUEST['uninstall']==$paymentmethodname)
{
	delete_option("payment_method_$paymentmethodname");
	$install_message = __("Payment Method deleted successfully");
}
?>