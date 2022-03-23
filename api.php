<?php

require 'social_posts.php';

add_action( 'rest_api_init', 'loadRestRoutes' );
function loadRestRoutes() {
    // Declare our namespace
    $namespace = 'rest/v2';

    // Register the route
    register_rest_route( $namespace, '/socialPosts', array(
        'methods'   => 'GET',
        'callback'  => 'get_posts_social',
    ) );
}


function get_posts_social($request)
{
  ob_start();
  facebook_feed();
/* PERFORM COMLEX QUERY, ECHO RESULTS, ETC. */
  $page = ob_get_contents();
  ob_end_clean();
  return($page);
}
