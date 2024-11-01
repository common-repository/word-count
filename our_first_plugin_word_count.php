<?php
/*
Plugin Name: Word Count
Plugin URI: https://wordpress.org/plugins/
Description: A truly amazing plugin.
Author: Obeyda Ferwana
Author URI:https://profiles.wordpress.org/obeyda/
Version: 1.0
Text Domain : wcpdomain
*/

class OBOUR_WordCountAndTimePlugin {
    function  __construct() {
        add_action('admin_menu',array($this,'OBOUR_adminPage'));
        add_action('admin_init',array($this,'OBOUR_settings'));
        add_filter('the_content',array($this,'OBOUR_ifWrap'));

    }

    function  OBOUR_ifWrap($content) {
        if(is_main_query() AND is_single() AND
            (
            esc_attr(get_option('wcp_wordcount','1')) OR
            esc_attr(get_option('wcp_charactercount','1')) OR
                esc_attr(get_option('wcp_readtime','1'))
            ) ) {
                return $this->OBOUR_createHTML($content);
        }
        return $content;
    }

    function  OBOUR_createHTML($content){
        $html = '<h3>' . esc_html( get_option('wcp_headline','Post Statistics')) .'</h3><p>';

        // get word count once because both wordcount and read time will need it.
        if(esc_attr(get_option('wcp_wordcount','1')) OR esc_attr(get_option('wcp_readtime','1'))) {
            $wordCount = str_word_count(strip_tags($content));

        }

        if(get_option('wcp_wordcount','1')) {
            $html .= esc_html(__('This post has','wcpdomain')) . '' . $wordCount . ''. __(' words','wcpdomain').'.<br>';
        }

        if(get_option('wcp_charactercount','1')) {
            $html .= __('This post has') . strlen(strip_tags($content)) . __(' characters').'.<br>';
        }

        if(get_option('wcp_readtime','1')) {
            $html .= __( 'This post will take about') . round($wordCount / 225)  . __(' minute(s) to read').'.<br>';
        }

            $html .= '</p>';

        if(esc_attr(get_option('wcp_location','0')) == '0') {
            return $html . $content;
        }
        return $content . $html;
    }

    function OBOUR_settings() {
        add_settings_section('wcp_first_section',null,null,__('word-count-settings-page'));

        add_settings_field('wcp_location',__('Display Location'),array($this,'OBOUR_locationHTML'),__('word-count-settings-page'),'wcp_first_section');
        register_setting('OBOUR_wordcountplugin','wcp_location',array('sanitize_callback' => array($this,'OBOUR_sanitizelocation'),'default'=> '0'));

        add_settings_field('wcp_headline',__('Headline Text'),array($this,'OBOUR_headlineHTML'),__('word-count-settings-page'),'wcp_first_section' );
        register_setting('OBOUR_wordcountplugin','wcp_headline',array('sanitize_callback' => 'sanitize_text_field','default'=> 'Post Statistics'));

        add_settings_field('wcp_wordcount',__('Word Count'),array($this,'OBOUR_wordcountHTML'),__('word-count-settings-page'),'wcp_first_section',array('theName'=>'wcp_wordcount'));
        register_setting('OBOUR_wordcountplugin','wcp_wordcount',array('sanitize_callback' => 'sanitize_text_field','default'=> '1'));

        add_settings_field('wcp_charactercount',__('Character Count'),array($this,'OBOUR_checboxtHTML'),__('word-count-settings-page'),'wcp_first_section',array('theName'=>'wcp_charactercount'));
        register_setting('OBOUR_wordcountplugin','wcp_charactercount',array('sanitize_callback' => 'sanitize_text_field','default'=> '1'));

        add_settings_field('wcp_readtime',__('Read Time'),array($this,'OBOUR_checboxtHTML'),__('word-count-settings-page'),'wcp_first_section',array('theName'=> 'wcp_readtime'));
        register_setting('OBOUR_wordcountplugin','wcp_readtime',array('sanitize_callback' => 'sanitize_text_field','default'=> '1'));
    }

    function OBOUR_sanitizelocation($input) {
        if($input !='0' AND $input != '1') {
            add_settings_error('wcp_location','wcp_location_error',__('Display location must be either beginning or end.'));
            return esc_attr(get_option('wcp_location'));
        }
        return $input;
    }

    function  OBOUR_wordcountHTML() {?>
        <input type="checkbox" name="<?php _e("wcp_wordcount") ?>" value="1" <?php esc_html_e( checked(get_option('wcp_wordcount')),'1') ?> >
   <?php }

    function OBOUR_checboxtHTML($args) {?>
        <input type="checkbox" name="<?php echo $args['theName']?>" value="1" <?php esc_html_e( checked(get_option($args['theName'])),'1') ?>>
  <?php  }


    function OBOUR_headlineHTML() { ?>
        <input type="text" name="<?php _e("wcp_headline") ?>" value="<?php echo  esc_attr(get_option('wcp_headline'))?>">
    <?php  }

    function OBOUR_locationHTML() {?>
     <select name="wcp_location" >
         <option value="0" <?php esc_html_e( selected(esc_attr(get_option('wcp_location')),'0')) ?>>Beginning of post</option>
         <option value="1" <?php esc_html_e( selected(esc_attr(get_option('wcp_location')),'1')) ?>>End of post</option>
     </select>
   <?php }

    function OBOUR_adminPage() {
        add_options_page(__('Word Count Settings'),__('Word Count','wcpdomain'),'manage_options','word-count-settings-page',array($this,'OBOUR_ourHTML'));
    }

    function OBOUR_ourHTML() { ?>
       <div class="wrap">
           <h1 style="padding: 10px; background: #333;color: #fff"><?php _e('Word Count Settings') ?></h1>
           <form action="options.php" method="POST">
               <?php
               settings_fields('OBOUR_wordcountplugin');
               do_settings_sections(__('word-count-settings-page'));
               submit_button();
               ?>
           </form>
       </div>
    <?php }
}

$oBOUR_WordCountAndTimePlugin = new OBOUR_WordCountAndTimePlugin();



