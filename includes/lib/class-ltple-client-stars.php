<?php

	if ( ! defined( 'ABSPATH' ) ) exit;

	class LTPLE_Client_Stars {
		
		var $parent;
		var $triggers;
		
		/**
		 * Constructor function
		 */
		 
		public function __construct ( $parent ) {

			$this->parent 	= $parent;
			
			$this->triggers = $this->get_triggers();
			
			if( !empty($this->triggers) ){
				
				foreach( $this->triggers as $trigger => $description ){
					
					add_action( $trigger, array( $this,  'add_stars') );
				}
			}
			
			add_action( 'show_user_profile', array( $this, 'get_user_stars' ) );
			add_action( 'edit_user_profile', array( $this, 'get_user_stars' ) );
			add_action( 'edit_user_profile_update', array( $this, 'save_user_stars' ) );
		}
		
		public function get_triggers(){
			
			$triggers = array();

			$triggers['user_register'] = array(
					
				'description' => 'when you register for the first time'
			);
				
			$triggers[$this->parent->_base . 'new_app_connected'] = array(
					
				'description' => 'when you connect a new App'
			);
			
			$triggers[$this->parent->_base . 'twitter_account_connected'] = array(
					
				'description' => 'when you connect a new Twitter account'
			);
			
			return $triggers;
		}

		public function get_count( $user_id = null ){
			
			if( !is_numeric($user_id) ){
				
				$user_id = $this->parent->user->ID;
			}
			
			$stars = get_user_meta( $user_id, $this->parent->_base . 'stars', true );
			
			if(is_numeric($stars)){
				
				$stars = intval($stars);
			}
			else{
				
				// set first count
				
				$stars = 0;
				
				// set stars
				
				update_user_meta( $user_id, $this->parent->_base . 'stars', $stars );
			}
			
			if( $stars === 0 ){
				
				// TODO update stars
				
			}
			
			return $stars;
		}
		
		public function add_stars( $user_id = null ){
			
			if( !is_numeric($user_id) ){
				
				$user_id = $this->parent->user->ID;
			}			
			
			$option_name = $this->parent->_base . current_filter().'_stars';
			
			$stars = get_option($option_name);
			
			if( !is_numeric($stars) ){
				
				$stars = 0;
			}
			
			if( $stars != 0 ){
				
				// get user stars
				
				$user_stars = $this->get_count( $user_id );
				
				$user_stars = $user_stars + $stars;
				
				// update user stars
				
				update_user_meta( $user_id, $this->parent->_base . 'stars', $user_stars );
				
				if( $user_id == $this->parent->user->ID){
					
					// set current user stars
					
					$this->parent->user->stars = $user_stars;
				}
			}
		}
			
		public function get_user_stars( $user ) {
			
			if( current_user_can( 'administrator' ) ){
				
				echo '<div class="postbox" style="min-height:45px;">';
					
					echo '<h3 style="margin:10px;width:300px;display: inline-block;">' . __( 'Stars', 'live-template-editor-client' ) . '</h3>';

					$field =  array(
			
						'id' 			=> $this->parent->_base . 'stars',
						'description' 	=> '',
						'type'			=> 'number',
						'placeholder'	=> 'stars',
					);
					
					$this->parent->admin->display_field( $field, $user );
						
				echo'</div>';
			}	
		}
		
		public function save_user_stars( $user_id ) {
			
			if( current_user_can( 'administrator' ) ){
				
				$field = $this->parent->_base . 'stars';
				
				if( isset($_REQUEST[$field]) && is_numeric($_REQUEST[$field]) ){
					
					$user_stars = floatval($_REQUEST[$field]);
					
					// update user stars
					
					update_user_meta( $user_id, $field, $user_stars );
				}
			}	
		}
		
		/**
		 * Main LTPLE_Client_Stars Instance
		 *
		 * Ensures only one instance of LTPLE_Client_Stars is loaded or can be loaded.
		 *
		 * @since 1.0.0
		 * @static
		 * @see LTPLE_Client()
		 * @return Main LTPLE_Client_Stars instance
		 */
		public static function instance ( $parent ) {
			
			if ( is_null( self::$_instance ) ) {
				
				self::$_instance = new self( $parent );
			}
			
			return self::$_instance;
			
		} // End instance()

		/**
		 * Cloning is forbidden.
		 *
		 * @since 1.0.0
		 */
		public function __clone () {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
		} // End __clone()

		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @since 1.0.0
		 */
		public function __wakeup () {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
		} // End __wakeup()
	}