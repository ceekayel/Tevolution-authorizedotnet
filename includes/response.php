<script>
document.getElementById('cart_information_span1').innerHTML ="0";
</script>
<?php session_start();
$_SESSION['CART_INFORMATION'] = array();
$_SESSION['couponcode'] = array();
$post_id = $_REQUEST['post_id'];
$redirectUrl = get_option('siteurl')."/?page=payment_success&post_id=".$post_id;
//$General->set_ordert_status($post_id,'approve');
global $wpdb,$General,$current_user;
//$order_number = preg_replace('/([0-9]*([_]))/','',$orderId);
$userId = $current_user->ID;
//$ordersql = "select * from $wpdb->usermeta um,$wpdb->users u where u.ID=um.user_id and um.meta_key = 'user_order_info' and u.ID='".$post_id."'";
$userdata = "select * from $wpdb->users where ID = '".$userId."'";
$post_info = $wpdb->get_results($userdata);

if($post_info)
{
	foreach($post_info as $postinfoObj)
	{
		$meta_value = unserialize(unserialize($postinfoObj->meta_value));
		$display_name= $postinfoObj->display_name;
		$user_email= $postinfoObj->user_email;
		$orderInformationArray = $meta_value[$order_number - 1];
		$user_info = $orderInformationArray[0]['user_info'];
		$cart_info = $orderInformationArray[0]['cart_info'];
		$payment_info = $orderInformationArray[0]['payment_info'];
		$order_info = $orderInformationArray[0]['order_info'];
		$affliate_info = $orderInformationArray[0]['affliate_info'];
	}
}

$fromEmail = $General->get_site_emailId();
$fromEmailName = $General->get_site_emailName();
global $userInfo;

if($_REQUEST['user_fname']){$userInfo['display_name'] = $_REQUEST['user_fname'];}
if($_REQUEST['user_email']){$userInfo['user_email'] = $_REQUEST['user_email'];}
$toEmailName = $display_name;
$toEmail = $user_email;
$subject = __('Payment success for post #$post_id - Autorize.net');
$transaction_details = $General->get_order_detailinfo_tableformat($orderNumber);
$General->sendEmail($fromEmail,$fromEmailName,$toEmail,$toEmailName,$subject,$transaction_details,$extra='');///To affiliate email
$authorize_net_order_success = 1;

get_header(); ?>
<div id="wrapper">
    <div id="main_top"></div> <!--main top #end -->
    <div id="main_center" class="clearfix">
        <div id="content"> 
		
<h1 class="head"><?php _e('Thank you'); ?></h1>
    <div class="breadcrumb clearfix">
        <?php if ( get_option( 'ptthemes_breadcrumbs' )) { yoast_breadcrumb('',' &raquo; Payment Success'); } ?>
    </div>
<?php
global $upload_folder_path;
$destinationfile =   ABSPATH . $upload_folder_path."notification/message/payment_success_paypal.txt";
if(file_exists($destinationfile))
{
	$filecontent = file_get_contents($destinationfile);
}
?>
<?php
$store_name = get_option('blogname');
$search_array = array('[#$store_name#]');
$replace_array = array($store_name);
$filecontent = str_replace($search_array,$replace_array,$filecontent);
if($filecontent)
{
echo $filecontent;
}else
{
?> 
<h4><?php _e('Thank you for your order'); ?></h4>
<h6><?php _e('Your payment has been successfully received and your order will be processed for shipping.'); ?></h6>
<h6><?php _e('Thank you for shopping at '.get_option('blogname').'.'); ?></h6>
<?php
}
?>
  			  </div> <!-- content #end -->
 	<div class="grid_4 sidebar_left fl" id="sidebar">
    	 <?php if ( function_exists('dynamic_sidebar') ) { // Show on the front page ?>
       <?php dynamic_sidebar('Inner Page Sidebar Left'); ?>
      <?php } ?>
    </div> <!-- sidebar #end -->
         </div> <!-- maincenter #end-->
    <div id="main_bottom"></div> 
</div> <!-- wrapper #end -->

 <?php get_footer(); ?>
