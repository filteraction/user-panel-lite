<?php
/*Plugin Name: User Panel Lite
Plugin URI:   https://github.com/filteraction/user-panel-lite
Description:  User Panel Lite is a responsive User Panel plugin for WordPress with which you could able to view all your users under a single roof and do some stuff with them.
Version:      1.0.4
Author:       Filter Action
Author URI:   https://www.filteraction.com
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  wporg
Domain Path:  /languages
License:     GPL2
 
User Panel Lite is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
User Panel Lite is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with {Plugin Name}. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
 # Option Page Generated by the WordPress Option Page generator
 # at http://jeremyhixon.com/wp-tools/option-page/
*/
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
// Define WC_PLUGIN_FILE.
if ( ! defined( 'UPL_FILE' ) ) {
	define( 'UPL_FILE', __FILE__ );
}
define("UPL_URL", plugin_dir_url( __FILE__ ));
define("UPL_ROOT_URI", plugins_url( __FILE__ ));
define("UPL_ADMIN_URI", admin_url());
define("UPL_PATH", __DIR__);
define('UPL_PLUGIN', plugin_basename( __FILE__ ));

class UPL{
	private $loader = '';
	private $user_table = '';
	public function __construct() {
		global $wpdb;
		$this->db = $wpdb;
		$this->user_table =  $wpdb->prefix . "users";
		add_filter( "plugin_action_links_".UPL_PLUGIN, array( $this, 'plugin_add_settings_link' ) );//add plugin settings link to plugins page	
		add_action( 'admin_menu', array( $this, 'create_upl_menu') );
		add_action( 'init', array( $this, 'upl_job_init' ) );	
		add_action( 'init', array( $this, 'upl_create_job_taxonomy' ) );	
		add_action( 'wp_head', array($this, 'upl_ajaxurl'));
        add_action( 'wp_ajax_nopriv_upl_ajax_delete_user', array($this, 'upl_ajax_delete_user'));
        add_action( 'wp_ajax_upl_ajax_delete_user', array($this, 'upl_ajax_delete_user'));	
        add_action( 'wp_ajax_upl_ajax_add_job', array($this, 'upl_ajax_add_job'));	
        add_action( 'wp_ajax_upl_ajax_status_update', array($this, 'upl_ajax_status_update'));
        require_once('admin-settings/theme-option.php');//generate option page	
	}


	public function plugin_add_settings_link( $links ) {
	    $settings_link = '<a href="admin.php?page=upl-option">' . __( 'Settings' ) . '</a>';
	    array_push( $links, $settings_link );
	  	return $links;
	}
		
	public function create_upl_menu(){
		$my_page = add_menu_page('upl', 'User Panel Lite', 'manage_options', 'upl', array($this, 'upl_callback'), 'dashicons-groups', 70);
		add_submenu_page('upl','Assign Job', 'Assign Job', 'manage_options','edit.php?post_type=job', null);// add post type into the sub-menu
		$my_page1 = add_submenu_page(null,'User Details', 'User Details', 'read','user-details', array( $this, 'user_details')); 
		$my_page2 = add_submenu_page('upl','Settings', 'Settings', 'manage_options','upl-settings', array( $this, 'upl_settings_create_admin_page' ),'dashicons-admin-tools');
		add_action( 'admin_init', array( $this, 'upl_settings_page_init' ) );//admin setting page
		add_action( "admin_print_scripts-$my_page", array( $this, 'upl_enqueue_script' ) );
		add_action( "admin_print_scripts-$my_page1", array( $this, 'upl_enqueue_script' ) );
		add_action( "admin_print_scripts-$my_page2", array( $this, 'upl_enqueue_script' ) );
		
	}
	public function upl_callback(){
		include_once('inc/upl.php');	
	}
	public function user_details(){
		require_once('inc/view-user.php');
	}
	//#############################  Start Setting Page ################################
	public function upl_settings_page_init() {
	    register_setting('upl-settings-group', 'cookies');
	    register_setting('upl-settings-group', 'header_text');
	    register_setting('upl-settings-group', 'logo_url');
	    register_setting('upl-settings-group', 'api_login_id');
	    register_setting('upl-settings-group', 'transaction_key');
	    register_setting('upl-settings-group', 'auth_description');
	    register_setting('upl-settings-group', 'button_image');
	    register_setting('upl-settings-group', 'default_amount');
	    register_setting('upl-settings-group', 'placeholder_text');
	    register_setting('upl-settings-group', 'transaction_mode');       
	    register_setting('upl-settings-group', 'receipt_header_text');
	    register_setting('upl-settings-group', 'enable_invoice');
	}
	public function upl_settings_create_admin_page() {
		$this->upl_settings_options = get_option( 'upl_settings_option_name' ); ?>

	<div class="wrap">
	        <h2>UPL Settings</h2>

	        <form method="post" action="options.php">
	            <?php settings_fields('upl-settings-group'); ?>
	            <?php do_settings_sections('upl-settings-group'); ?>
	            <table class="form-table">
	                <tr valign="top">
	                    <th scope="row">Enable Cookies</th>
	                    <td>
						<input type="checkbox" name="cookies" value="yes" <?php echo (get_option('cookies') == 'yes')?'checked="checked"':'' ?>>		
	                    </td>
	                </tr>
	                <tr valign="top">
	                    <th scope="row">Header Text (displayed on top of the payment page)</th>
	                    <td><input type="text" name="header_text" value="<?php echo get_option('header_text'); ?>" /></td>
	                </tr>

	                <tr valign="top">
	                    <th scope="row">Logo URL</th>
	                    <td><input type="text" name="logo_url" value="<?php echo get_option('logo_url'); ?>" /></td>
	                </tr>

	                <tr valign="top">
	                    <th scope="row">API Login ID</th>
	                    <td><input type="text" name="api_login_id" value="<?php echo get_option('api_login_id'); ?>" /></td>
	                </tr>

	                <tr valign="top">
	                    <th scope="row">Transaction Key</th>
	                    <td><input type="text" name="transaction_key" value="<?php echo get_option('transaction_key'); ?>" /></td>
	                </tr>

	                <tr valign="top">
	                    <th scope="row">Description To be displayed on payment page</th>
	                    <td><input type="text" name="auth_description" value="<?php echo get_option('auth_description'); ?>" /></td>
	                </tr>
	                <!--Added in v 0.2-->
	                <tr valign="top">
	                    <th scope="row">Header Text To be displayed on Receipt page</th>
	                    <td><input type="text" name="receipt_header_text" value="<?php echo get_option('receipt_header_text'); ?>" /></td>
	                </tr>
	                <!--end Added in v 0.2-->
	                
	                <tr valign="top">
	                    <th scope="row">Submit Button Image</th>
	                    <td><input type="text" name="button_image" value="<?php echo get_option('button_image'); ?>" /></td>
	                </tr>
	                
	                <tr valign="top">
	                    <th scope="row">Default Amount</th>
	                    <td><input type="text" name="default_amount" value="<?php echo get_option('default_amount'); ?>" /></td>
	                </tr>
	                
	                <tr valign="top">
	                    <th scope="row">Placeholder Text like "Enter your amount here"</th>
	                    <td><input type="text" name="placeholder_text" value="<?php echo get_option('placeholder_text'); ?>" /></td>
	                </tr>
	                
	                <tr valign="top">
	                    <th scope="row">Transaction Mode</th>
	                    <?php 
	                        $live_checked = $test_checked  = '';
	                        $live_checked = (get_option('transaction_mode')=='live') ? 'checked="checked"' : '';
	                        $test_checked = (get_option('transaction_mode')=='test') ? 'checked="checked"' : '';
	                         
	                    ?>
	                    <td><input type="radio" name="transaction_mode" value="live" <?php echo $live_checked;?> >live (Production Environment)<br><input type="radio" name="transaction_mode" value="test" <?php echo $test_checked;?>>test</td>
	                </tr>
	                
	                <tr valign="top">
	                    <th scope="row">Invoice</th>
	                    <?php 
	                        $enable_invoice_checked = $disable_invoice_checked  = '';
	                        $enable_invoice_checked = (get_option('enable_invoice')=='enable') ? 'checked="checked"' : '';
	                        $disable_invoice_checked = (get_option('enable_invoice')=='disable') ? 'checked="checked"' : '';
	                         
	                    ?>
	                    <td><input type="radio" name="enable_invoice" value="enable" <?php echo $enable_invoice_checked;?> >enable<br><input type="radio" name="enable_invoice" value="disable" <?php echo $disable_invoice_checked;?>>disable</td>
	                </tr>
	                
	                
	                        
	            </table>

	            <?php submit_button(); ?>

	        </form>
	    </div>
	<?php }


//############################# End Setting Page ################################

	//User Capabilities
	public function add_user_capability(){
	   $current_user   = wp_get_current_user();
	    $role_name      = $current_user->roles[0];
	    switch($role_name) {
	        case upl:
	            return 'upl';
	            break;
	        case Subscriber:
	            return 'read';
	            break;
	        case Administrator:
	            return 'manage_options';
	            break;
	        case Editor:
	            return 'edit_pages';
	            break; 
	        case Contributor:
	            return 'edit_posts';
	            break;
	        default:
	            return 'read';
	    }

	}


	public function test(){
		//test some quick stuffs
	}

	public function upl_job_init() {
	    $labels = array(
	        'name'               => _x( 'My Jobs', 'post type general name', 'your-plugin-textdomain' ),
	        'singular_name'      => _x( 'My Job', 'post type singular name', 'your-plugin-textdomain' ),
	        'add_new'            => 'Add Job',
	        'all_items'          => 'All Job',
	        'edit_item'          =>'Edit Job',
	    );
	    $args = array(
	        'labels'             => $labels,
	        'description'        => __( 'Description.', 'your-plugin-textdomain' ),
	        'public'             => true,
	        'publicly_queryable' => true,
	        'show_ui'            => true,
	        'show_in_menu'       => false,
	        'query_var'          => true,
	        'rewrite'            => array( 'slug' => 'job' ),
	        'capability_type'    => 'post',
	        'has_archive'        => true,
	        'menu_position'      => 2,
	        'menu_icon'			 =>'dashicons-pressthis',
	        'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'comments' )
	    );
	    register_post_type( 'job', $args );
	}
	/*
	 * Register taxonomy job
	 */
	function upl_create_job_taxonomy() {
	    register_taxonomy(
	        'job-cat',
	        'job',
	        array(
	            'label' => 'Category',
	            'hierarchical' => true,
	        )
	    );
	}
	public function upl_ajaxurl(){
		?>
        <script type="text/javascript">
            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        </script>
        <?php
	}

	public function upl_ajax_add_job(){
		if( isset( $_POST['installer_id'] ) && $_POST['installer_id'] != null ){
			if(!check_ajax_referer( 'upl-special-string', 'security',false)){
				 	echo 'Security Issue!';
				 	die;
				 }
		$job_title = sanitize_title($_POST['job_title']);
		$job_description = sanitize_textarea_field($_POST['job_description']);
		$file1 = sanitize_file_name($_FILES['featured_img' ]);
		$customer_name = sanitize_user($_POST['customer_name']);
		$customer_email = sanitize_email($_POST['customer_email']);
		$customer_contact = intval( $_POST['customer_contact']);
		$customer_address = sanitize_textarea_field($_POST['customer_address']);
		$installer_name = sanitize_user( $_POST['installer_name']);
		$installer_id = intval($_POST['installer_id']);
		$author_obj = get_user_by('id', $installer_id);
		$wp_upload_dir = wp_upload_dir();
		$upload_path      = str_replace( '/', DIRECTORY_SEPARATOR, $wp_upload_dir['path'] ) . DIRECTORY_SEPARATOR;
		//echo 'upload path='.$upload_path.'<br>';
		//output=>C:\xampp\htdocs\egates\wp-content\uploads\2018\05\
		$upload_url      = str_replace( '/', DIRECTORY_SEPARATOR, $wp_upload_dir['url'] ) . DIRECTORY_SEPARATOR;
		//echo $wp_upload_dir['url'];
		//output=>http://localhost/testing/wp-content/uploads/2018/05/
		//echo 'upload url='.$upload_url.'<br>';
		//output=>http:\\localhost\egates\wp-content\uploads\2018\05\

		        $img =  sanitize_file_name($_POST['featured_img']) ;
		        if(!empty($img)){
		        $img = str_replace('data:image/jpeg;base64,', '', $img);
		        $img = str_replace(' ', '+', $img);
		        $decoded = base64_decode($img) ;
		        $filename         = 'job.jpeg';
		        $hashed_filename  = md5( $filename . microtime() ) . '_' . $filename;
		        $image_upload     = file_put_contents( $upload_path . $hashed_filename, $decoded );
		        //output=>780831

		        //HANDLE UPLOADED FILE
		        $file             = array();
		        $file['error']    = 'UPLOAD_ERR_OK';
		        $file['tmp_name'] = $upload_path . $hashed_filename;
		        $file['name']     = $hashed_filename;
		        $file['type']     = 'image/jpeg';
		        $file['size']     = filesize( $upload_path . $hashed_filename );
		        $file['url']     = $wp_upload_dir['url'] .'/'. basename($file['name']);
		        
		        $filename = $file['url'];
		            // $upload_overrides = array( array('test_form' => FALSE, 'test_upload' => FALSE, 'test_type' => FALSE) ); // if you don’t pass 'test_form' => FALSE the upload will be rejected
		            // $file_return = wp_handle_upload( $file, $upload_overrides );
		            // var_dump($file_return);
		            //die;
		            //IO=> array(3) { ["file"]=> string(60) "C:\xampp\htdocs\egates/wp-content/uploads/2018/05/Tulips.jpg" ["url"]=> string(61) "http://localhost/egates/wp-content/uploads/2018/05/Tulips.jpg" ["type"]=> string(10) "image/jpeg" } 

		        $attachment = array(
		         'post_mime_type' => $file['type'],
		         'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
		         'post_content' => '',
		         'post_status' => 'inherit',
		         'guid' => $wp_upload_dir['url']. '/' . basename( $filename ),
		         );
		        //  var_dump($attachment);
		        // echo 'TEMPNAME='.$filename.'<br>';
		        // echo 'basename='.basename($filename).'<br>';
		        // echo 'NAME='.$file['name'] .'<br>';
		       
		        $attach_id = wp_insert_attachment( $attachment, $filename );
		        $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
		        wp_update_attachment_metadata( $attach_id, $attach_data );
		    }
				$post_info = array(
		        'post_title' =>  wp_strip_all_tags($job_title),
		        'post_content' => $job_description,
		        'post_type' => 'job',
		        'post_author' => $installer_id,
		        'post_status'=>'publish'
		);
		$pid = wp_insert_post( $post_info );
		if($attach_id){
		add_post_meta($pid, 'image', $file );
		add_post_meta($pid, 'image_ID', $attach_id );
		update_post_meta($pid,'_thumbnail_id',$attach_id);
		set_post_thumbnail( $pid, $thumbnail_id );
		}
		update_post_meta($pid,'customer_name',$customer_name);
		update_post_meta($pid,'customer_email',$customer_email);
		update_post_meta($pid,'customer_contact',$customer_contact);
		update_post_meta($pid,'customer_address',$customer_address);
		// wp_set_object_terms($pid, $country,'country'); //for taxonomy
		// wp_set_object_terms($pid, $zip,'zip');
		// wp_set_object_terms($pid, $city,'city');
		// update_field('email', $email, $pid);

		    if($pid){
		        $from_email = $author_obj->user_email;
		        $user_name = $author_obj->user_nicename;
		        $admin_email = get_option( 'admin_email' );
		        $subject = 'New job from egates';
		        $body = '<html><head></head> 
		                      <body>
		                          <h3>Dear<em> '.$user_name.',</em></h3>
		                          <h3 style="color: #004496;">There is a new job for you. Please login to see. </h3>
		                      </body>
		                  </html>';
		        $headers="From:".$user_name."< ".$from_email. " >\r\n";
		        $headers .= "Reply-To:".$admin_email."\r\n";
		        $headers .= "MIME-Version: 1.0\r\n";
		        $to = $from_email;
		        $headers .= "Content-Type: text/html; charset=UTF-8\r\n"; 
		        wp_mail( $to, $subject, $body, $headers );
		        $message =  '<div class="alert alert-success" role="alert">
		                    <button style="width: 50px" type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		                      <i class="fa fa-check alert-icon"></i> Job assigned successfully.
		                </div>';
		           
		    }
		    else{ 
		        $message = 
		         '<div class="alert alert-danger" role="alert">
		        <button style="width: 50px" type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		              <strong>Oh snap!</strong> Change a few things up and try submitting again.
		            </div>';
		 
		     }
			echo $message;
		 
		}
        die();

	}
	public function upl_ajax_status_update(){

			if(isset($_REQUEST['status']) && $_REQUEST['user_id']){
				 if(!check_ajax_referer( 'upl-special-string', 'security',false)){
				 	echo 'Security Issue!';
				 	die;
				 }
			$status = sanitize_title($_REQUEST['status']);
			$user_id = intval($_REQUEST['user_id']);
			$user_name = sanitize_user($_REQUEST['user_name']);
			$user_email = sanitize_email($_REQUEST['user_email']);
			$admin_email = get_option( 'admin_email' );

			// $sqli = "update my_order set status= $status where order_id= $order_id";
			$results = $this->db->update($this->user_table, array('user_status'=>$status), array('ID'=>$user_id));

			if($results>0){
				$status = ($status == 1) ? 'active' : 'inactive';
				$from_email = $user_email;
				$user_name = $user_name;
				$admin_email = $admin_email;
				$subject = 'Account Status Changed';
				$body = '<html><head></head> 
				              <body>
					              <h3>Dear<em> '.$user_name.',</em></h3>
					              <h3 style="color: #004496;">Your account is now '.$status.'.</h3>
				              </body>
			              </html>';
				$headers="From:".$user_name."< ".$from_email. " >\r\n";
				$headers .= "Reply-To:".$admin_email."\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$to = $from_email;
				$headers .= "Content-Type: text/html; charset=UTF-8\r\n"; 
				if(wp_mail( $to, $subject, $body, $headers )){
					echo 'Status has been changed successfully!';
				}
				else{
					//failed to deliver mail
					echo 'Status has been changed successfully with error code 471';
				}
			}
			else{
				//update failed
				echo 'Server error';
			}
		}
		else{
			//status or user_id not set
			echo 'Opps! Something went wrong.';
		}
        die();
	}
	public function upl_ajax_delete_user(){
		if (isset($_REQUEST)) {
			 if(!check_ajax_referer( 'upl-special-string', 'security',false)){
				 	echo 'Security Issue!';
				 	die;
				 }
		$user_id = intval($_REQUEST['user_id']);
		$results = $this->db->delete($this->user_table, array( 'ID'=>$user_id) );
			if($results>0){
				echo 'User has been deleted successfully!';
			}
			else{
				echo 'Server error';	
			}
		}
		else{
			echo 'Opps! Something went wrong.';
		}
           
        die();

	}
		
	public function upl_enqueue_script() {   
	//enque style 
		wp_enqueue_style ('bootstrap',  UPL_URL. 'css/bootstrap.min.css'  );
		wp_enqueue_style ('dataTables',  UPL_URL. 'css/dataTables.bootstrap.min.css'  );
		wp_enqueue_style ('font-family', UPL_URL. 'css/font-family.css'  );
		wp_enqueue_style ('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'  );
		// wp_enqueue_style ('font-awesome', UPL_URL. 'css/font-awesome.min.css' );
		wp_enqueue_style ('style', UPL_URL. 'css/style.css'  );
// ----------------------------------------------------------------------------
	//enque script 
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script('bootstrapjs',  UPL_URL . 'js/bootstrap.min.js', array(), null, true);
		wp_enqueue_script('jquerydatatablesminjs', UPL_URL . 'js/jquery.dataTables.min.js', array(), false, true);
		wp_enqueue_script('bootstrapdatatablesminjs', UPL_URL . 'js/dataTables.bootstrap.min.js', array(), false, true);

		// wp_enqueue_script('datatablesjs', 'https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js', array(), null, true);
		// wp_enqueue_script('datatablesminjs', 'https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js', array(), null, true); 
	    wp_enqueue_script( 'my_custom_script', UPL_URL . 'js/script.js', array(), false, true);
	
	}
	
}
if ( is_admin() )
new UPL();