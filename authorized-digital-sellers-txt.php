<?php
/*
Plugin Name: Authorized Digital Sellers TXT
Description: This is a simple plugin that provides you with the option of making a Authorized Digital Sellers -file (ads.txt) that is placed in the root of your WordPress installation.
Author: jeffreyvr
Author URI: https://profiles.wordpress.org/jeffreyvr/
Text Domain: authorized-digital-sellers-txt
Domain Path: /languages
Version: 0.1
License: GPLv2 or later
*/

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Authorized_Digital_Sellers_Txt' ) )  {

class Authorized_Digital_Sellers_Txt {

  /**
   * Construct
   *
   * @since 1.0
   */
  public function __construct() {
    $this->init();
  }

  /**
   * Admin actions
   *
   * @since 1.0
   */
  public function admin_actions() {
    add_action( 'admin_menu', array( $this, 'register_settings_page' ) );
    add_action( 'admin_init', array( $this, 'register_settings' ) );
    add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

    if ( isset( $_GET['page'] ) && $_GET['page'] == 'authorized-digital-sellers-txt' ) {
      add_action( 'admin_head', array( $this, 'settings_page_css' ) );
    }
  }

  /**
   * Init
   *
   * @since 1.0
   */
  public function init() {
    if ( is_admin() ) {
      $this->admin_actions();
    }
  }

  /**
   * Load textdomain
   *
   * @since 1.0
   */
  public function load_textdomain() {
    load_plugin_textdomain( 'authorized-digital-sellers-txt', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
  }

  /**
   * Register settings page
   *
   * @since 1.0
   */
  public function register_settings_page() {
    add_options_page(
      __( 'ADS.txt options', 'authorized-digital-sellers-txt' ),
      __( 'ADS.txt', 'authorized-digital-sellers-txt' ),
      'manage_options',
      'authorized-digital-sellers-txt',
      array( $this, 'settings_page_callback' )
    );
  }

  /**
   * Settings page css.
   *
   * @since 1.0
   */
  public function settings_page_css() {
    $css = '<style>
      .authorized-digital-sellers-txt-block {
        max-width: 800px;
        padding: 5px 15px 15px 15px;
        border: 1px solid #ddd;
        margin-bottom: 15px;
        background-color: #fff;
      }
      .authorized-digital-sellers-txt-block textarea {
        font-family: consolas, courier;
      }
      .authorized-digital-sellers-txt-block .submit {
        margin-bottom: 0;
        padding-bottom: 0;
      }
      .authorized-digital-sellers-txt-block code {
        display: block;
        padding: 15px;
      }
    </style>';

    echo $css;
  }

  /**
   * Settings page html
   *
   * @since 1.0
   */
  public function settings_page_callback() {

    if ( isset( $_POST['authorized_digital_sellers_txt'] ) && current_user_can( 'manage_options' ) ) {

      if ( wp_verify_nonce( $_POST['_wpnonce'], 'authorized_digital_sellers_txt_nonce' ) ) {
        $this->write_txt_file( filter_input( INPUT_POST, 'authorized_digital_sellers_txt', FILTER_SANITIZE_STRING ) );
      }

    }

    $file         = get_home_path() . 'ads.txt';
    $file_content = file_get_contents( $file );
    $txt_content  = filter_var( $file_content, FILTER_SANITIZE_STRING );

    include 'partials/admin-settings-page.php';
  }

  /**
   * Write txt file
   *
   * @since 1.0
   */
  public function write_txt_file( $txt ) {
    $file = get_home_path() . 'ads.txt';

    if ( is_writable ( $file ) ) {
      file_put_contents( $file, filter_var( $txt, FILTER_SANITIZE_STRING ), LOCK_EX );
    } else {
      wp_die( __( 'Not able to write the ads.txt file in the WordPress root directory. Please add it manually or edit the permissions of the folder.', 'authorized-digital-sellers-txt' ) );
      exit;
    }
  }

}

new Authorized_Digital_Sellers_Txt();

}
