<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.onlineoptimisation.com.au
 * @since      1.0.0
 *
 * @package    Plandat
 * @subpackage Plandat/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Plandat
 * @subpackage Plandat/public
 * @author     Online Optimisation <info@onlineoptimisation.com.au>
 */
class Plandat_PDF_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->templates = array(
			'page-template-login.php' => __('Login'),
			'page-template-signup.php' => __('Sign Up'),
			'page-template-report.php' => __('Report'),
			'page-template-account.php' => __('Acount'),
			'page-template-pre-search.php' => __('Previous Searches'),
			'page-template-test-report.php' => __('Test Report'),
			'page-template-order-report.php' => __('Order Report')
		);

	}

	public function load_templates($post_templates, $wp_theme, $post, $post_type){

		foreach ($this->templates as $file_name => $label) {
			$post_templates[$file_name] = $label;
		}

		return $post_templates;

	}

	public function page_template($page_template){
		foreach ($this->templates as $file_name => $label) {
			if ( get_page_template_slug() == $file_name ) {
	        $page_template = dirname( __FILE__ ) . '/templates/' . $file_name ;
	    }
		}
		return $page_template;
	}

	public function show_admin_bar(){
		 $user = wp_get_current_user();
		 if ( !empty($user) && !in_array('administrator',$user->roles) ) {
				 return false;
		 } else {
				 return true;
		 }
	}

	public function redirect_templates($template){

		// Get template file.
	  $file = basename($template);

		//Logined
	  if (is_user_logged_in() && ($file === 'page-template-signup.php' || $file === 'page-template-login.php')) {
	    // Your logic goes here.
			$user_report = get_report_current_user();
			if(!empty($user_report)){
				wp_redirect(home_url('/previous-searches/'));
			}else{
				wp_redirect(home_url('/report/'));
			}
	    exit;
	  }

		//Empty
		if((!isset($_GET['pdf_id']) && $file === 'page-template-account.php') || (isset($_GET['pdf_id']) && $_GET['pdf_id'] == '')){
      wp_redirect(home_url('/previous-searches/'));
			exit;
    }

		//Other author
		if(isset($_GET['pdf_id']) && $_GET['pdf_id'] != ''){
			$post_author_id = get_post_field( 'post_author', $_GET['pdf_id'] );
			$share_users 		= get_field('share_users' , $_GET['pdf_id']);
  		$curt_author_id = get_current_user_id();
			if($post_author_id != $curt_author_id && (empty($share_users) || !in_array($curt_author_id , $share_users))){
				wp_redirect(home_url('/previous-searches/'));
				exit;
			}
    }

		return $template;
	}

	public function block_wp_admin(){
		if ( is_admin() && current_user_can( 'reported' ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
        wp_safe_redirect( home_url('/previous-searches/') );
        exit;
    }
	}

	public function add_js_admin(){
		if(isset($_GET['post']) && $_GET['post'] != ''){
			?>
			<script type="text/javascript">

					// A $( document ).ready() block.
					jQuery( document ).ready(function() {
					 		jQuery('div[data-name="report_files"] input').each(function(){
								var $input = jQuery(this);
								$input.prop('disabled',true);
							});
					});

			</script>
			<?php
		}
	}

	public function add_user_top_right_header(){
		if(is_user_logged_in()){
			?>
			<div class="top-user-header">
				<a href="<?php echo home_url('/previous-searches/') ?>"> <img src="<?php echo PLANDAT_PDF_URL . 'public/images/icon-user.png' ?>" alt=""> </a>
			</div>
			<?php
		}else{
			?>
			<div class="top-user-header">
				<a href="/login/" class="login-user"> Login → </a>
			</div>
			<?php
		}
	}


	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plandat_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plandat_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/plandat-pdf-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'magnific-popup' , plugin_dir_url( __FILE__ ) . 'css/magnific-popup.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plandat_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plandat_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( 'magnific-popup' , plugin_dir_url( __FILE__ ) . 'js/magnific-popup.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/plandat-pdf-public.js', array( 'jquery' ), $this->version, false );

		// in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
		$curt_author_id = get_current_user_id();
		$discount = get_field('discount', 'user_' . $curt_author_id);
		$order_report_page = get_field('choose_order_report_page', 'options');
		wp_localize_script( $this->plugin_name , 'ajax_object',
	            array(
								'ajax_url' => admin_url( 'admin-ajax.php' ),
								'discount' => $discount,
								'home_url' => home_url(),
								'link_order_report' => $order_report_page ? get_permalink($order_report_page) : ''
							) );
	}

}
