<?php
/*
Plugin name: social_plug
Description: Plugin permettant d'afficher les posts Facebook et Instagram
Version: 1.0
Author: Emre (Soutenu par Waouh)
*/


class social_plugin
{
    public function __construct()
    {
        /* Adding script and style */
        // Chargement des styles et des scripts sur WordPress

        function custom_styles_scripts()
        {
            wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js', false, null, true);
            wp_enqueue_script('fb_script', '/wp-content/plugins/social_plugin/js/social_script.js', array('jquery'), 1, true);
            wp_localize_script('fb_script','ajax_object' , array(
              'ajaxurl' => site_url('/wp-json/rest/v2/'),
               'nonce' => wp_create_nonce('social_nonce')
             ));
            wp_enqueue_script('colcade_masonry', '/wp-content/plugins/social_plugin/js/colcade.js', array('jquery'), false);
            wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css');
            wp_enqueue_style('facebook_styles', '/wp-content/plugins/social_plugin/css/facebook_style.css');
        }

        add_action('wp_enqueue_scripts', 'custom_styles_scripts');

        require 'api.php';

        function sh_code()
        {
          $html = '<div class="wh_social_container"></div>';
          echo $html;
        }
        add_shortcode('fb_feed', 'sh_code');
    }
}

new social_plugin();
