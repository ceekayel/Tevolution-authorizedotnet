<?php 
$order_id = $_REQUEST['pid'];
global $page_title,$wpdb;
if(isset($_REQUEST['renew']) && $_REQUEST['renew']!="")
{
	$page_title = RENEW_SUCCESS_TITLE;
}else
{
	$page_title = POSTED_SUCCESS_TITLE;
}
get_header(); 

do_action('templ_before_success_container_breadcrumb');
//MAIL SENDING TO CLIENT AND ADMIN START
global $payable_amount,$last_postid,$stripe_options,$wpdb,$monetization,$sql_post_id;
$transaction_tabel = $wpdb->prefix."transactions";
$user_id = $wpdb->get_var("select user_id from $transaction_tabel order by trans_id DESC limit 1");
$user_id = $user_id;
$sql_transaction = "select max(trans_id) as trans_id from $transaction_tabel where user_id = $user_id and status=0 ";
$sql_data = $wpdb->get_var($sql_transaction);
$sql_status_update = $wpdb->query("update $transaction_tabel set status=1 where trans_id=$sql_data");
$get_post_id = $wpdb->get_var("select post_id from $transaction_tabel where trans_id=$sql_data");
$wpdb->query("UPDATE $wpdb->posts SET post_status='publish' where ID = '".$order_id."'");
//$trans_status = $wpdb->query("update $transaction_tabel SET status = 1 where post_id = '".$get_post_id."'");

$sql_post_id = $wpdb->get_var("select post_id from $transaction_tabel where user_id = $user_id and trans_id=$sql_data");
$suc_post = get_post($sql_post_id);
$post_title = $suc_post->post_title;
$post_content = $suc_post->post_content;
$paid_amount = display_amount_with_currency_plugin(get_post_meta($sql_post_id,'paid_amount',true));
$user_details = get_userdata( $user_id );
$first_name = $user_details->first_name;
$last_name = $user_details->last_name;
$fromEmail = get_site_emailId_plugin();
$fromEmailName = get_site_emailName_plugin(); 	
$toEmail = $user_details->user_email;
$toEmailName = $first_name.' '.$last_name;

$theme_settings = get_option('templatic_settings');

//	Payment success Mail to client END		
$client_mail_subject =  apply_filters('authorize_client_subject',$theme_settings['payment_success_email_subject_to_client']);
$client_mail_content = $theme_settings['payment_success_email_content_to_client'];

$client_transaction_mail_content = '<p>Thank you for your cooperation with us.</p>';
$client_transaction_mail_content .= '<p>You successfully completed your payment by Authorize.net.</p>';
$client_transaction_mail_content .= "<p>".__('Your submitted id is:')." : ".$sql_post_id."</p>";
$client_transaction_mail_content .= '<p>'.__('View more detail from').' <a href="'.get_permalink($sql_post_id).'">'.$suc_post->post_title.'</a></p>';

$search_array = array('[#to_name#]','[#transaction_details#]','[#site_name#]');
$replace_array = array($toEmailName,$client_transaction_mail_content,$fromEmailName);
$client_message = apply_filters('authorize_client_message',str_replace($search_array,$replace_array,$client_mail_content),$toEmailName,$fromEmailName);
templ_send_email($fromEmail,$fromEmailName,$toEmail,$toEmailName,$client_mail_subject,$client_message,$extra='');///To client email

//Payment success Mail to admin START
$admin_mail_subject =  apply_filters('authorize_admin_subject',$theme_settings['payment_success_email_subject_to_admin']);
$admin_mail_content = $theme_settings['payment_success_email_content_to_admin'];

$admin_transaction_mail_content .= "<p>Payment recieved from $toEmailName via Authorice.net.</p>";
$admin_transaction_mail_content .= "<p>".__('Submitted listing id is:')." : ".$sql_post_id."</p>";
$admin_transaction_mail_content .= '<p>'.__('View more detail from').' <a href="'.get_permalink($sql_post_id).'">'.$suc_post->post_title.'</a></p>';

$search_array = array('[#to_name#]','[#transaction_details#]','[#site_name#]');
$replace_array = array($fromEmailName,$admin_transaction_mail_content,$fromEmailName);
$admin_message = apply_filters('authorize_admin_message',str_replace($search_array,$replace_array,$admin_mail_content),$fromEmailName,$toEmailName);
templ_send_email($toEmail,$toEmailName,$fromEmail,$fromEmailName,$admin_mail_subject,$admin_message,$extra='');///To client email
//Payment success Mail to admin FINISH
$paymentmethod = get_post_meta($_REQUEST['pid'],'paymentmethod',true);
$paid_amount = display_amount_with_currency_plugin(get_post_meta($_REQUEST['pid'],'paid_amount',true));
global $upload_folder_path,$wpdb;
if($paymentmethod == 'prebanktransfer')
{
	$filecontent = stripslashes(get_option('post_pre_bank_trasfer_msg_content'));
	if(!$filecontent)	{
		$filecontent = POST_POSTED_SUCCESS_PREBANK_MSG;
	}
}else
{
	$filecontent = stripslashes(get_option('post_added_success_msg_content'));
	if(!$filecontent)
	{
		$filecontent = POST_SUCCESS_MSG;
	}
}

?>
    <div class="content_<?php echo stripslashes(get_option('ptthemes_sidebar_left'));  ?>" id="content">
	 <h1 class="page_head"><?php _e("Payment successfully completed"); ?></h1>
     <div class="posted_successful">
		<?php do_action('authorizedotnet_successfull_return_content',$filecontent);?> 
	</div>
     <!-- Short Detail of post -->
    <?php do_action('authorizedotnet_submit_post_details');?>
	<!-- End Short Detail of post -->


</div> <!-- content #end -->

<div class="sidebar" id="sidebar-primary">
<?php dynamic_sidebar($cus_post_type.'_detail_sidebar');?>
</div>
<?php
	unset($_SESSION['category']);
	unset($_SESSION['custom_fields']);
	unset($_SESSION['upload_file']);

get_footer(); ?>