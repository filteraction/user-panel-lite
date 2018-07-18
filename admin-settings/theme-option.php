<?php
class UPL_Settings{
	private $general;
	private $header;
	private $footer;
	private $social;
	public function __construct(){
		require_once('option-fields.php');
		add_action('admin_menu', array( $this, 'create_admin_menu' ) );
		//add_action('admin_init', array($this, 'create_settings'));		
	}	

	public function create_admin_menu(){
		$parent = 'upl';
		$page_title = 'Options';
		$menu_title = 'Options';
		$capability = 'manage_options';
		$slug = 'upl-option';
		$callback = array($this, 'my_new_option_page');
		$icon = 'dashicons-admin-settings';
		$position = 3;
		add_submenu_page($parent,$page_title,$menu_title,$capability,$slug,$callback,$icon,$position);

	}	
	public function my_new_option_page(){ 

		$GeneralTab = ( ! isset( $_GET['tab'] ) || isset( $_GET['tab'] ) && 'header' != $_GET['tab']  && 'footer' != $_GET['tab']  && 'social' != $_GET['tab'] ) ? true : false;
		$HeaderTab = (isset($_GET['tab']) && 'header' == $_GET['tab']) ? true : false;
		$FooterTab = (isset($_GET['tab']) && 'footer' == $_GET['tab']) ? true : false;
		$SocialTab = (isset($_GET['tab']) && 'social' == $_GET['tab']) ? true : false;
		?>
		<div class="wrap">

			<h1><b>UPL Option Page</b></h1>
			<?php settings_errors(); ?>
			<h2 class="nav-tab-wrapper">
				<a href="<?php echo admin_url( 'admin.php?page=upl-option' ); ?>" class="nav-tab <?php if( $GeneralTab ) echo 'nav-tab-active'; ?>">General</a>
				<a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'header' ), admin_url( 'admin.php?page=upl-option' ) ) ); ?>" class="nav-tab <?php if( $HeaderTab ) echo 'nav-tab-active'; ?> ">Header</a>
				<a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'footer' ), admin_url( 'admin.php?page=upl-option' ) ) ); ?>" class="nav-tab <?php if( $FooterTab ) echo 'nav-tab-active'; ?>">Footer</a>
				<a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'social' ), admin_url( 'admin.php?page=upl-option' ) ) ); ?>" class="nav-tab <?php if( $SocialTab ) echo 'nav-tab-active'; ?>">Social</a>
			</h2>
			 <form method="post" action="options.php">
			 	<?php 
				 if($HeaderTab) { 					
					settings_fields( 'option_settion_header' );
					do_settings_sections( 'option_settion_header' );
					submit_button();				
				} elseif($FooterTab) {
					settings_fields( 'option_settion_footer' );
					do_settings_sections( 'option_settion_footer' );
					submit_button();
				} elseif($SocialTab) {
					settings_fields( 'option_settion_social' );
					do_settings_sections( 'option_settion_social' );
					submit_button();
				}else { 
					settings_fields( 'option_settion_general' );//same as register_setting()
					do_settings_sections( 'option_settion_general' );
					submit_button(); 
				} ?>
			</form>

		</div>
	<?php 	
	}
	public function create_child_option_page(){
		settings_fields( 'option_settion_social' );
					do_settings_sections( 'option_settion_social' );
					submit_button();
		
	}	
	public function create_settings(){
		//require_once('option-fields.php');
		

	}
}//end class
new UPL_Settings();