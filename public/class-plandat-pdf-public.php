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
			'page-template-login.php' => __('Login')
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/plandat-pdf-public.js', array( 'jquery' ), $this->version, false );

	}

}
