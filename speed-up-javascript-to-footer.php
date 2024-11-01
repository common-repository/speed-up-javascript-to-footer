<?php
/*
 Plugin Name: Speed Up - JavaScript To Footer
 Plugin URI: http://wordpress.org/plugins/speed-up-javascript-to-footer/
 Description: Move all the possible JavaScript files from head to footer and improve page load times.
 Version: 1.0.11
 Author: Simone Nigro
 Author URI: https://profiles.wordpress.org/nigrosimone
 License: GPLv2 or later
 License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( !defined('ABSPATH') ) exit;

class SpeedUp_JavaScriptToFooter {
    
    /**
     * Instance of the object.
     *
     * @since  1.0.0
     * @static
     * @access public
     * @var null|object
     */
    public static $instance = null;
    
    
    /**
     * Access the single instance of this class.
     *
     * @since  1.0.0
     * @return SpeedUp_JavaScriptToFooter
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     *
     * @since  1.0.0
     * @return SpeedUp_JavaScriptToFooter
     */
    private function __construct(){
        
        if( !is_admin() ){
            add_action( 'wp_enqueue_scripts', array( $this, 'move_scripts') );
            add_action( 'wp_head',            array( $this, 'preload_scripts'), PHP_INT_MAX -1 );
        }
        
    }
    
    /**
     * Move scripts from head to bottom/footer.
     * 
     * @since  1.0.1
     * @return void
     */
    public function move_scripts(){
        // clean head
        remove_action('wp_head', 'wp_print_scripts');
        remove_action('wp_head', 'wp_print_head_scripts', 9);
        remove_action('wp_head', 'wp_enqueue_scripts', 1);
        
        // move script to footer
        add_action('wp_footer', 'wp_print_scripts', 5);
        add_action('wp_footer', 'wp_print_head_scripts', 5);
        add_action('wp_footer', 'wp_enqueue_scripts', 5);
    }
    
    /**
     * Preloads script in the head
     * 
     * @since  1.0.3
     * @return void
     */
    public function preload_scripts(){
    	$wp_scripts = wp_scripts();
    	
    	foreach( $wp_scripts->queue as $handle ){
			if( !empty($wp_scripts->registered[$handle]->src) ){
				
				if( isset($wp_scripts->registered[$handle]->extra['conditional']) ){
					echo '<!--[if '.$wp_scripts->registered[$handle]->extra['conditional'].'>'."\r\n";
				}
				
    			echo '<link rel="preload" href="'.$wp_scripts->registered[$handle]->src.'" as="script">'."\r\n";
    			
    			if( isset($wp_scripts->registered[$handle]->extra['conditional']) ){
    				echo '<![endif]-->'."\r\n";
    			}
			}
    	}
    }
}

// Init
SpeedUp_JavaScriptToFooter::get_instance();