<?php
/*
 * 	Plugin Name: VM Menu Reorder plugin
 * 	Description: Menu reorder plugin is used to rearrange your dashboard area.
 *	Author: Milankumar Kyada and Vimalkumar Gajera
 *	Version: 1.0.0		
 */

/*** Preserve original menu as BACKUP ***/
register_activation_hook(__FILE__,'add_scripts');
function add_scripts()
{
	global $menu;
	update_option('mr_original_position',$menu);
	$custom_orginal_menu = array();
	$menu_with_array_value = array();
	$i=0;
	foreach($menu as $key=>$value)
	{
		$custom_orginal_menu[$i]=$value[2];
		$menu_with_array_value[$value[2]]=(empty($value[0]) ? $value[2] : $value[0]);
		$i++;
	}
	update_option('mr_default_reorder_menu_position',json_encode($custom_orginal_menu));
	update_option('menu_with_array_value',json_encode($menu_with_array_value));
}

/*** Include stylesheets and JS ***/
add_action('admin_init','add_jquery_ui');
function add_jquery_ui(){
	wp_enqueue_script('j-ui',plugin_dir_url( __FILE__ ).'jquery/jquery-ui.js');
	wp_enqueue_script('plugin-menu',plugin_dir_url( __FILE__ ).'js/menu.min.js');
	wp_enqueue_style('j-ui-css',plugin_dir_url( __FILE__ ).'jquery/jquery-ui.css');	
	wp_enqueue_style('plugin-style',plugin_dir_url( __FILE__ ).'css/style.css');	
	wp_register_style('w3-css',plugin_dir_url( __FILE__ ).'css/w3.css');	
}

/*** Add setting page***/
add_action('admin_menu','add_setting_page1');
function add_setting_page1()
{
	if(is_super_admin())
	{
		add_options_page( 'Test Admin Menus', 'Test Admin Menus', 'manage_options', 'reorder_menu_test', 'add_menu_options_test');		
	}
	
}

/*** To save new post types in originail menu array ***/
add_action( 'admin_menu', 'my_plugin_override' );
function my_plugin_override() {
	
	global $menu;
	update_option('mr_original_position',$menu);

	$custom_orginal_menu = array();
	$menu_with_array_value = array();
	$user_cap = array();
	$i=0;
	
	foreach($menu as $key=>$value)
	{
		$custom_orginal_menu[$i]=$value[2];
		$user_cap[$value[2]]=$value[1];
		$menu_with_array_value[$value[2]]=(empty($value[0]) ? $value[2] : $value[0]);
		$i++;
	}
	update_option('mr_default_reorder_menu_position',json_encode($custom_orginal_menu));
	update_option('menu_with_array_value',json_encode($menu_with_array_value));
	update_option('mr_user_cap',json_encode($user_cap));
}

/*** Setting page starts from here ***/
function add_menu_options_test()
{
	global $menu;
	wp_enqueue_style('w3-css');
?>


<div class="w3-container main-area">


<div class="w3-leftbar w3-border-blue w3-container">
    <p><strong>Note:</strong> First priority is given to user role.</p>
  </div>

<!--- Response message -->
<div class="w3-container w3-section w3-red resp_msg" style="display: none;">
<span onclick="this.parentElement.style.display='none'" class="w3-closebtn">&times;</span>
  <h3>Danger!</h3>
  
</div>
<!--- Response message -->

<ul class="w3-navbar w3-black">
  <li><a href="#" class="tablink" onclick="openCity(event, 'Newmenu');">Set new menu</a></li>
  <li><a href="#" class="tablink" onclick="openCity(event, 'c_user');">Change menu by user role </a></li>
  <li><a href="#" class="tablink" onclick="openCity(event, 'c_menu');">Reset Menu order</a></li>
  
</ul>

<div id="Newmenu" class="w3-container w3-border city">
	 <div class="w3-row w3-card-4">
	 	
	 	<div class="w3-container">
			
			<h2 class="w3-large">Set new menu</h2>
		</div>

		<div class="w3-third w3-container ">
			<p>Add or Reserve Menu</p>
		  <ul id="sortable1" class="connectedSortable">
			
			  <?php
			  if(json_decode(get_option('mr_reorder_menu_position')))
			  {
			  	$reorder_admin_menu = json_decode(get_option('mr_reorder_menu_position'));
			  }
			  else
			  {
			  	$reorder_admin_menu=array();	
			  }
			  	
			  	$reordered_array = array();
			  	$admin_menu = get_option('mr_original_position');
			  	$res_of_menu = array_keys(json_decode(get_option("menu_with_array_value"),true));
			  	 // echo '<pre>';
			  	 // print_r(json_decode(get_option("menu_with_array_value"),true));
			  	 // echo '</pre>';
			  	/*** Find the difference between original menu and reordered menu ***/
			  	$resultant = array_diff($res_of_menu,$reorder_admin_menu);


				if(isset($reorder_admin_menu))
				{
					
					$original_menu = json_decode(get_option('mr_default_reorder_menu_position'));
					
					foreach($original_menu as $key=>$value)
					{
						if(!in_array($value,$reorder_admin_menu))
						{
							remove_menu_page($value);
							
						}
						else
						{
							
							$ordered_key = array_search($value, $reorder_admin_menu);
							
						}
					}
					foreach ($admin_menu as $key=>$value ) {
						
						if(in_array($value[2], $resultant))
						{

						?>
						<li class=" w3-btn w3-hover-blue w3-card-4" data-page="<?php echo $value[2]; ?>"><?php echo (empty($value[0]) ? $value[2] : $value[0]); ?></li>
						<?php
						}

					}
		
				}
			  ?>
			</ul>
		</div>

		
		<div class="w3-third w3-container">
		<p>Final or Current menu</p>
		  	<ul id="sortable2" class="connectedSortable">
		 		<?php
		 		foreach ($menu as $key=>$value ) {
						?>
						<li class="w3-btn w3-hover-blue w3-card-4" data-page="<?php echo $value[2]; ?>"><?php echo (empty($value[0]) ? $value[2] : $value[0]); ?></li>
						<?php
					}
				?>
			</ul>
		</div>


		<div class="w3-third w3-container">
		  <input type="submit" class="w3-btn w3-hover-green w3-ripple set_rm" value="Set New Menu Order">
		</div>
		</div>


	 <div class="set_order">
		
		 
		 <!--- Droppable area -->
		
		
	</div>
</div>


<div id="c_user" class="w3-container w3-border city w3-card-4">
<?php 
global $wp_roles;
$user_role = $wp_roles->get_names();
?>
  <h2 class="w3-large">Set new menu</h2>
  	<select class="w3-select user_role" name="option">
	  <option value="" disabled selected>Choose user role</option>
	  <?php 
	  foreach($user_role as $key=>$value)
	  {
	  	?>
	  	<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
	  	<?php	
	  }
	  ?>
	  
	</select>
	<div class="w3-row ">
		<div class="w3-third w3-container ">
		<p>Add or Reserve Menu</p>
		  <ul id="user_opt" class="connectedSortable_user">
			
			</ul>
		</div>

		
		<div class="w3-third w3-container">
		<p>Final or Current menu</p>
		  	<ul id="user_selected_opt" class="connectedSortable_user">
		 		
		 		
			</ul>
		</div>


		<div class="w3-third w3-container">
		  <input type="submit" class="w3-btn w3-hover-blue w3-ripple set_rm_for_user" value="Set New Menu Order">
		</div>
		</div>
  
</div>


<!-- Reset area -->
<div id="c_menu" class="w3-container w3-border city">


	<div class="w3-row w3-card-4">
		<div class="w3-container ">
			
			<h3 class="w3-large">Reset Menu Order</h3>
		</div>
	  <div class="w3-container w3-half">
	  	<form class="w3-container" id="reset_role">
	  <?php 
	  foreach ($user_role as $key => $value) {
	  	$role_order="";

	  	$role_order = get_option('reorder_'.$key.'_by_role');
	  	if(!isset($role_order) || empty($role_order) )
	  	{
	  		echo '<p><input class="w3-check" disabled type="checkbox" name="u_role[]" value="'.$key.'"><label class="w3-validate">'.$value.'</label></p>';	
	  	}
	  	else
	  	{
	  		echo '<p><input class="w3-check nodisable" type="checkbox" name="u_role[]" value="'.$key.'"><label class="w3-validate">'.$value.'</label></p>';	
	  	}
	  	
	  }
	  ?>
	  
	    </form>
	    <p><input type="submit" class="w3-btn w3-hover-yellow reset_u_role" value="Reset selected role"></p>
	  </div>


	  <div class="w3-container w3-half">
	  <p>Reset All Changes</p>
	  <p><input type="submit" class="w3-btn w3-hover-blue reset_all_menu" value="Reset Menu"></p>
	  </div>


	</div>

	  <div class="current_menu">
		
		
	</div>
</div>

</div>
<!-- main-area -->


<input type="hidden" class="ajax-url" value="<?php echo admin_url('admin-ajax.php'); ?>">
<input type="hidden" class="re-order-nonce" value="<?php echo wp_create_nonce('re-order'); ?>">


	<?php
}
/*** Setting page END here ***/


/**** This is what you are looking for :) ****/
add_filter('custom_menu_order', 'custom_menu_order');
add_filter('menu_order', 'custom_menu_order');

function custom_menu_order($menu_ord){
	
	if (!$menu_ord) return true;
		$reorder_admin_menu="";
			global $current_user;
		    
		    $user=wp_get_current_user();
		    $user = new WP_User( $current_user->ID);

			if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
			    foreach ( $user->roles as $role )
			    $user = $role;
			}
		if(get_option('reorder_'.$user.'_by_role'))
		{
			$reorder_admin_menu = json_decode(get_option('reorder_'.$user.'_by_role'));
			if(isset($reorder_admin_menu) && !empty($reorder_admin_menu))
			{
				
				$original_menu = json_decode(get_option('mr_default_reorder_menu_position'));
				
				foreach($original_menu as $key=>$value)
				{
					if(!in_array($value,$reorder_admin_menu))
					{
						remove_menu_page($value);
					}
				}
				return $reorder_admin_menu;
			}
		}
		if(get_option('mr_reorder_menu_position'))
		{
			$reorder_admin_menu = json_decode(get_option('mr_reorder_menu_position'),true);
		}
		else
		{
			$reorder_admin_menu = array_keys(json_decode(get_option("menu_with_array_value"),true));
		}
			
			if(isset($reorder_admin_menu) && !empty($reorder_admin_menu))
			{
				
				$original_menu = json_decode(get_option('mr_default_reorder_menu_position'));
				
				foreach($original_menu as $key=>$value)
				{
					if(!in_array($value,$reorder_admin_menu))
					{
						remove_menu_page($value);
					}
				}
				return $reorder_admin_menu;
			}
				
		
}
/**** setting menu option is over here ****/


/**** AJAX for adding new set menu ****/
add_action('wp_ajax_set_menuorder','set_menuorder');
function set_menuorder()
{
	if(isset($_REQUEST['_nonce']))
	{
		$nonce = $_REQUEST['_nonce'];
	}
	
	if(!wp_verify_nonce($nonce,'re-order'))
	return;
	
	if(isset($_REQUEST['order']))
	{
		$menu_position = $_REQUEST['order'];
		$menu_order = json_encode(explode(",",$menu_position));
		update_option('mr_reorder_menu_position',$menu_order);
		return true;
		
	}
	return false;
}

/**** AJAX for adding new set menu ****/
add_action('wp_ajax_set_menuorder_by_user_role','set_menuorder_by_user_role');
function set_menuorder_by_user_role()
{
	if(isset($_REQUEST['_nonce']))
	{
		$nonce = $_REQUEST['_nonce'];
	}
	
	if(!wp_verify_nonce($nonce,'re-order'))
	return;
	
	if(isset($_REQUEST['order']) && isset($_REQUEST['user_role']))
	{
		$menu_position = $_REQUEST['order'];
		$menu_order = json_encode(explode(",",$menu_position));
		$role = strip_tags($_REQUEST['user_role']);
		update_option('reorder_'.$role.'_by_role',$menu_order);
		return true;
		
	}
	return false;
}


/**** AJAX for adding new set menu by user role ****/
add_action('wp_ajax_get_menuorder_by_user','get_menuorder_by_user');
function get_menuorder_by_user()
{
	if(isset($_REQUEST['_nonce']))
	{
		$nonce = $_REQUEST['_nonce'];
	}
	
	if(!wp_verify_nonce($nonce,'re-order'))
	return;
	
	/*** If menu is reordered for any role then it goes in below if loop ***/
	if(isset($_REQUEST['userrole']) && !empty($_REQUEST['userrole']))
	{
		$role = strip_tags($_REQUEST['userrole']);	
		$roleObject = get_role( $role );
		$admin_menu_cap = json_decode(get_option('mr_user_cap'),true);
		$admin_menu_key = array();
		
		$return_left = array();
		
		$user_selected_menu = json_decode(get_option('reorder_'.$role.'_by_role'));
		if(isset($user_selected_menu) && !empty($user_selected_menu))
		{
			$admin_menu = json_decode(get_option('menu_with_array_value'),true);
			
			$left_area = array();
			$right_area = array();
			foreach ($admin_menu as $key => $value) {
				if(!in_array($key, $user_selected_menu))
				{
					$left_area[$key]=$value;
				}
				
			}
	
			$admin_menu_key = array_keys($left_area);
			foreach ($admin_menu_cap as $r_key => $r_value) {
					if ($roleObject->has_cap($r_value) ) {
					    
					    if(in_array($r_key, $admin_menu_key))
					    {
					    	$return_left[$r_key]=$admin_menu[$r_key];
					    }
					    
					}
					
				}
				// print_r($left_area);
			$final_arr['left']=$return_left;
			
			/*** Set selected menu in arranged order ***/
			$temp_key = array();
			$temp_key = array_keys($admin_menu);
			foreach ($user_selected_menu as $key => $value) {
				
				if(in_array($value, $temp_key))
				{
					$right_area[$value]=$admin_menu[$value];
				}
				
			}
		
			$final_arr['right']=$right_area;
			echo json_encode($final_arr);
			wp_die();
			
			
		}
		else
		{

			/**** This will called until menu is not reordered for any user role ****/

			$admin_menu = json_decode(get_option('menu_with_array_value'),true);
			$admin_menu_cap = json_decode(get_option('mr_user_cap'),true);
			$roleObject = get_role( $role );
			$admin_menu_key = array();
			
			$return = array();
			$admin_menu_key = array_keys($admin_menu);

			/*** It will check the capability of the user by role ***/
			foreach ($admin_menu_cap as $key => $value) {
				
				if ($roleObject->has_cap($value) ) {
				    
				    if(in_array($key, $admin_menu_key))
				    {
				    	$return[$key]=$admin_menu[$key];
				    }
				    
				}
				
			}

			$return_arr['default_menu']=$return;
			echo json_encode($return_arr);
			
			wp_die();
			
		}
	}
	
		$user_role = $wp_roles->get_names();
		$admin_menu = get_option('mr_original_position');
		
		$user_selected_menu = get_option('reorder_administrator_by_role');	
	  	$reorder_admin_menu = json_decode(get_option('mr_reorder_menu_position'));

  		$reordered_array = array();
  	
	  	$res_of_menu = array_keys(json_decode(get_option("menu_with_array_value"),true));
	  	$resultant = array_diff($res_of_menu,$reorder_admin_menu);
	  	if(isset($reorder_admin_menu) && !empty($reorder_admin_menu))
		{
			
			$original_menu = json_decode(get_option('mr_default_reorder_menu_position'));
			
			foreach($original_menu as $key=>$value)
			{
				if(!in_array($value,$reorder_admin_menu))
				{
					remove_menu_page($value);
					
				}
				else
				{
					
					$ordered_key = array_search($value, $reorder_admin_menu);
					
				}
			}
			foreach ($admin_menu as $key=>$value ) {
				
				?>
				<li class="ui-state-default" data-page="<?php echo $value[2]; ?>"><?php echo (empty($value[0]) ? $value[2] : $value[0]); ?></li>
				<?php
				

			}
		//$resultant = array_diff_assoc($res_of_menu,$menu);
		}
		else
		{
			foreach ($admin_menu as $key=>$value ) {
				
				?>
				<li class="ui-state-default" data-page="<?php echo $value[2]; ?>"><?php echo (empty($value[0]) ? $value[2] : $value[0]); ?></li>
				<?php
				

			}
		}

}

/*** Retrieve Re-ordered Menu ***/
add_action('wp_ajax_set_to_default','set_to_default');
function set_to_default()
{
	global $wp_roles;
	delete_option('mr_reorder_menu_position');
	delete_option('mr_default_reorder_menu_position');
	
	$user_role = $wp_roles->get_names();
	foreach ($user_role as $key => $value) {
		delete_option('reorder_'.$key.'_by_role');
	}
	echo "done";
	wp_die();
}

add_action('wp_ajax_reset_for_userrole','reset_for_userrole');
function reset_for_userrole()
{
	if(isset($_REQUEST['_nonce']))
	{
		$nonce = $_REQUEST['_nonce'];
	}
	
	if(!wp_verify_nonce($nonce,'re-order'))
	{
		echo false;	
		wp_die();
	}
	
	
	if(isset($_REQUEST['u_role']))
	{
		$roles = array();
		$roles=$_REQUEST['u_role'];
		foreach ($roles as $key => $value) {
			delete_option('reorder_'.$value.'_by_role');
		}
		echo true;
		wp_die();
	}
}
