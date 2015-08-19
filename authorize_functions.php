<?php
/* NAME : CREATE DIRECTORIES AND COPY FILES
DESCRIPTION : THIS FUNCTION WILL CREATE DIRECTORIES AND COPY FILES TO THEM */
function templ_add_method_install_authorizenet()
{
	update_option('authorize_plugin_active', 'true');
	require_once(TEMPL_FILE_PATH_AUTHORIZE .'includes/install.php');
}

/* NAME : DELETE DIRECTORIES AND DELETE FILES
DESCRIPTION : THIS FUNCTION WILL DELETE DIRECTORIES AND DELETE FILES TO THEM */
function templ_add_method_deactivate_authorizenet()
{	
	delete_option('authorize_plugin_active');
	//remove the "Authorize.net" option from the payment options.
	global $wpdb;
	$paymentmethodname = 'authorizedotnet';
	$paymethodinfo = array(
					"name" 		=> 'Authorize.net',
					"key" 		=> $paymentmethodname,
					"isactive"	=>	'1', // 1->display,0->hide
					"display_order"=>'18',
					"payOpts"	=>	$payOpts,
					);
	$paymentsql = "select * from $wpdb->options where option_name like 'payment_method_".$paymethodinfo['key']."' order by option_id asc";
	$paymentinfo = $wpdb->get_results($paymentsql);
	$wpdb->query("DELETE FROM $wpdb->options where option_name like 'payment_method_".$paymethodinfo['key']."'");
}


//Create Setting link for plugin: Start
add_filter( 'plugin_action_links_' . AUTHORIZE_PLUGIN_BASENAME,'authorize_action_links'  );
function authorize_action_links($links){

	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=monetization&tab=payment_options' ) . '">' . __( 'Settings', DOMAIN ) . '</a>',			
	);
	return array_merge( $plugin_links, $links );
}
//Create Setting link for plugin: End

//Plugin activation message notification:start
add_action('admin_init','authorize_init_function_callback');
if(!function_exists('authorize_init_function_callback')){
	function authorize_init_function_callback(){
		if( !isset($_GET['act_plug']) ){
			if(is_plugin_active("Tevolution-authorizedotnet/Tevolution-authorizedotnet.php")){
				if(!is_plugin_active("Tevolution/templatic.php")){?>
					<div id="message" class="error">
						<p>
							<?php  _e("You have not activated a base plugin Tevolution! Please activate it to start using Templatic Authorize.net",DOMAIN);?>
						</p>
					</div>
			<?php
				}
			}
		}
		if(get_option('authorize_plugin_active') == 'true'){
			update_option('authorize_plugin_active', 'false');
			wp_safe_redirect(admin_url('plugins.php?act_plug=authorize'));
		}
		if( isset($_GET['act_plug']) && $_GET['act_plug'] == "authorize" ){
			if(is_plugin_active("Tevolution-authorizedotnet/Tevolution-authorizedotnet.php")){
				if(!is_plugin_active("Tevolution/templatic.php")){?>
					<div id="message" class="error">
						<p>
							<?php  _e("You have not activated a base plugin Tevolution! Please activate it to start using Templatic Authorize.net",DOMAIN);?>
						</p>
					</div>
			<?php
				}else{
			?>	
					<div id="message" class="updated">
						<p>
							<?php  _e("Templatic Authorize.net is active now, click <a href='".admin_url( 'admin.php?page=monetization&tab=payment_options' )."'>here</a> to get started. For detailed information, refer the <a href='http://templatic.com/docs/authorizenet/' target='_blank'>users guide</a>",DOMAIN);?>
						</p>
					</div>
			<?php	
				}
			}
		}
	}
}
//Plugin activation message notification:End

/* if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'preview')
{
	add_action('wp_header','templ_add_authorize_toggle_script');
}

function templ_add_authorize_toggle_script()
{
	echo "";
} */


add_action('authorizedotnet_successfull_return_content','successfull_return_authorizedotnet_content');
function successfull_return_authorizedotnet_content($filecontent)
{

	$tmpdata = get_option('templatic_settings');
	$post_default_status = $tmpdata['post_default_status'];

	if($post_default_status == 'publish'){
		$post_link = "<p><a href='".get_permalink($_REQUEST['pid'])."'  class='btn_input_highlight' >View your submitted Post &raquo;</a></p>";
	}else{
		$post_link ='';
	}
	$store_name = get_option('blogname');
	
	if($paymentmethod == 'prebanktransfer')
	{
		$paymentupdsql = "select option_value from $wpdb->options where option_name='payment_method_".$paymentmethod."'";
		$paymentupdinfo = $wpdb->get_results($paymentupdsql);
		$paymentInfo = unserialize($paymentupdinfo[0]->option_value);
		$payOpts = $paymentInfo['payOpts'];
		$bankInfo = $payOpts[0]['value'];
		$accountinfo = $payOpts[1]['value'];
	}
					
	$orderId = $_REQUEST['pid'];
	$siteName = "<a href='".site_url()."'>".$store_name."</a>";
	$search_array = array('[#payable_amt#]','[#bank_name#]','[#account_number#]','[#submition_Id#]','[#store_name#]','[#submited_information_link#]','[#site_name#]');
	$replace_array = array($paid_amount,@$bankInfo,@$accountinfo,$order_id,$store_name,$post_link,$siteName);
	$filecontent = str_replace($search_array,$replace_array,$filecontent); 
	echo $filecontent;	
}

add_action('authorizedotnet_submit_post_details','successfull_return_authorizedotnet_post_details');
function successfull_return_authorizedotnet_post_details()
{
	?>
     <!-- Short Detail of post -->
	<div class="title-container">
		<h1><?php echo POST_DETAIL;?></h1>
	</div>
    <div class="submited_info">
	<?php
	global $wpdb,$post;
	remove_all_actions('posts_where');
	$cus_post_type = get_post_type($_REQUEST['pid']);
	$args = 
	array( 'post_type' => 'custom_fields',
	'posts_per_page' => -1	,
	'post_status' => array('publish'),
	'meta_query' => array(
	   'relation' => 'AND',
		array(
			'key' => 'post_type_'.$cus_post_type.'',
			'value' => $cus_post_type,
			'compare' => '=',
			'type'=> 'text'
		),
		array(
			'key' => 'show_on_page',
			'value' =>  array('user_side','both_side'),
			'compare' => 'IN'
		),
		array(
			'key' => 'is_active',
			'value' =>  '1',
			'compare' => '='
		),
		array(
			'key' => 'show_on_success',
			'value' =>  '1',
			'compare' => '='
		)
	),
		'meta_key' => 'sort_order',
		'orderby' => 'meta_value',
		'order' => 'ASC'
	);
	$post_query = null;
	$post_meta_info = new WP_Query($args);
	$suc_post = get_post($_REQUEST['pid']);
		if($post_meta_info)
		  {
			echo "<div class='grid02 rc_rightcol'>";
			echo "<ul class='list'>";
			echo "<li><p>Post Title : </p> <p> ".$suc_post->post_title."</p></li>";
			while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
				
				if(get_post_meta($_REQUEST['pid'],$post->post_name,true))
					  {
						if(get_post_meta($post->ID,"ctype",true) == 'multicheckbox')
						  {
							foreach(get_post_meta($_REQUEST['pid'],$post->post_name,true) as $value)
							 {
								$_value .= $value.",";
							 }
							 echo "<li><p>".$post->post_title." : </p> <p> ".substr($_value,0,-1)."</p></li>";
						  }
						else
						 {
							 $custom_field=get_post_meta($_REQUEST['pid'],$post->post_name,true);
							 if(substr($custom_field, -4 ) == '.jpg' || substr($custom_field, -4 ) == '.png' || substr($custom_field, -4 ) == '.gif' || substr($custom_field, -4 ) == '.JPG' 
											|| substr($custom_field, -4 ) == '.PNG' || substr($custom_field, -4 ) == '.GIF'){
								  echo "<li><p>".$post->post_title." : </p> <p> <img src='".$custom_field."' /></p></li>";
							 }							 
							 else
							 echo "<li><p>".$post->post_title." : </p> <p> ".get_post_meta($_REQUEST['pid'],$post->post_name,true)."</p></li>";
						 }
					  }
					if($post->post_name == 'post_content')
					 {
						$suc_post_con = $suc_post->post_content;
					 }
					if($post->post_name == 'post_excerpt')
					 {
						$suc_post_excerpt = $suc_post->post_excerpt;
					 }

					if(get_post_meta($post->ID,"ctype",true) == 'geo_map')
					 {
						$add_str = get_post_meta($_REQUEST['pid'],'address',true);
						$geo_latitude = get_post_meta($_REQUEST['pid'],'geo_latitude',true);
						$geo_longitude = get_post_meta($_REQUEST['pid'],'geo_longitude',true);
						$map_view = get_post_meta($_REQUEST['pid'],'map_view',true);
					 }
  
			endwhile;
				fetch_payment_description($_REQUEST['pid']);
		  }		 
		
		?>
		</div>
		<?php if(isset($suc_post_con)): ?>
		    <div class="row">
			  <div class="twelve columns">
				  <div class="title_space">
					 <div class="title-container">
						<h1 class="title_green"><span><?php _e('Post Description');?></span></h1>
						<div class="clearfix"></div>
					 </div>
					 <p><?php echo nl2br($suc_post_con); ?></p>
				  </div>
			   </div>
		    </div>
		<?php endif; ?>
		
		<?php if(isset($suc_post_excerpt)): ?>
			 <div class="row">
				<div class="twelve columns">
					<div class="title_space">
						<div class="title-container">
							<h1 class="title_green"><span><?php _e('Post Excerpt');?></span></h1>
							<div class="clearfix"></div>
						</div>
						<p><?php echo nl2br($suc_post_excerpt); ?></p>
					</div>
				</div>
			</div>
		<?php endif; ?>
		
		<?php
		if(@$add_str)
		{
		?>
			<div class="row">
				<div class="title_space">
					<div class="title-container">
						<h1 class="title_green"><span><?php _e('Map'); ?></span></h1>
						<div class="clearfix"></div>
					</div>
					<p><strong><?php _e('Location : '); echo $add_str;?></strong></p>
				</div>
				<div id="gmap" class="graybox img-pad">
					<?php if($geo_longitude &&  $geo_latitude):
							$pimgarr = bdw_get_images_plugin($_REQUEST['pid'],'thumb',1);
							$pimg = $pimgarr[0]['file'];
							if(!$pimg):
								$pimg = TEMPL_PLUGIN_URL."/tmplconnector/monetize/templatic-custom_fields/images/img_not_available.png";
							endif;	
							$title = $suc_post->post_title;
							$post_link = get_permalink($_REQUEST['pid']);
							$address = $add_str;
							require_once (TEMPL_MONETIZE_FOLDER_PATH . 'templatic-custom_fields/preview_map.php');
							$retstr ="";
							/*$retstr .= "<div class=\"forrent\"><img src=\"$pimg\" width=\"192\" height=\"134\" alt=\"\" />";
							$retstr .= "<h6><a href=\"\" class=\"ptitle\" style=\"color:#444444;font-size:14px;\"><span>$title</span></a></h6>";
							if($address){$retstr .= "<span style=\"font-size:10px;\">$address</span>";}
							$retstr .= "</div>";*/
							
							$retstr .= "<div class=\"google-map-info map-image\"><div class=map-inner-wrapper> <div class=map-item-info><div class=map-item-img><img src=\"$pimg\" width=\"150\" height=\"150\" alt=\"\" /></div>";
							$retstr .= "<h6><a href=\"$post_link\" class=\"ptitle\" ><span>$title</span></a></h6>";
							if($address){$retstr .= "<p class=address>$address</p>";}
							$retstr .= "</div></div></div>";
							
							preview_address_google_map_plugin($geo_latitude,$geo_longitude,$retstr,$map_view);
						  else:
					?>
							<iframe src="http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=<?php echo $add_str;?>&amp;ie=UTF8&amp;z=14&amp;iwloc=A&amp;output=embed" height="358" width="100%" scrolling="no" frameborder="0" ></iframe>
					<?php endif; ?>
				</div>
			</div>
		<?php } ?>
		
		
		<!-- End Short Detail of post -->
     <?php	
}
?>