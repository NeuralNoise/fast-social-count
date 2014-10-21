<?php
/**
 * @package Fast_Social_Count
 * @version 1.6
 */
/*
Plugin Name: Fast Social Count
Plugin URI: http://wordpress.org/plugins/fast-social-count/
Description: This  plugin enables share buttons and share count for Twitter, FaceBook, GooglePlus, LinkedIn and Pinterest. Avoids loading social networks javascript just to display number of likes and buttons to share with.
Author: Mamilldo
Text Domain: fast-social-count
Version: 1.6
Author URI: http://mamilldo.se/fast-social-count/
*/

 /***********************************************************************************************
 * [fast-social-count]
 * [fast-social-count sharelabel="Share with your friends"]
 * [fast-social-count hassharedtext="people already done it!"]
 * [fast-social-count totalcount="no"]  ***if we don't want to show number of different likes
 * [fast-social-count googleplus='yes' pinterest='yes' facebook="no" twitter="no"]
 * [fast-social-count class="col-sm12"]
 * [fast-social-count iconclass="fa-2x text-danger"]
 * @mamilldo
 */

load_plugin_textdomain( 'fast-social-count', false, basename( dirname( __FILE__ ) ) . '/languages' );

function add_fsc_buttons_to_all_pages() {
	echo do_shortcode( '[fastsocial]' );
}
$options = get_option( 'fast_social_count_option_name' );
$option_add_to_all_footers       = $options['add_to_all_footers'];
if ( '1' == $option_add_to_all_footers ) {
	add_action( 'wp_footer', 'add_fsc_buttons_to_all_pages' );
}

add_shortcode( 'socialfast', 'fast_social' ); //provide option for shortcode
add_shortcode( 'fastsocial', 'fast_social' );
add_shortcode( 'fast-social', 'fast_social' );
add_shortcode( 'fast-social-count', 'fast_social' );

function fsc_add_stylesheets() {
	$version = '1.6';
	wp_register_style( 'fast_social_count_css', plugins_url( 'fsc-assets/fast-social-count.min.css', __FILE__ ), '', $version, false );
	wp_enqueue_style( 'fast_social_count_css' );
}
//add basic styles. Better to add your own, deregister theese files in your own functions file
add_action( 'wp_enqueue_scripts', 'fsc_add_stylesheets' );

function fast_social( $atts ) {
	 $version = '1.6';
	//only on pages using the shortcode
	wp_register_script( 'fast_social_count_js', plugins_url( 'fsc-assets/fast_social_count.min.js', __FILE__ ), array('jquery'), $version, true );
	wp_enqueue_script( 'fast_social_count_js' );

	global $post;
	//Labels and defaults from options page get_options()
	$options = get_option( 'fast_social_count_option_name' );
	$option_class                = $options['class'];
	$option_icon_classes    = $options['iconclass'];
	$option_sharelabel       = $options['sharelabel'];
	$option_hassharedtext = $options['hassharedtext'];
	$option_totalcount       = $options['enable_totalcount'];
	$option_facebook         = $options['enable_facebook'];
	$option_twitter              = $options['enable_twitter'];
	$option_googleplus      = $options['enable_googleplus'];
	$option_linkedin           = $options['enable_linkedin'];
	$option_pinterest          = $options['enable_pinterest'];
	$option_cachetimer      = $options['cachetimer'];

	if ( '' == $option_cachetimer ) {
		$option_cachetimer   = '3600';
	}

	// extract atts
	//shortcode atts replaces default values from settings per share bar
	extract(
		shortcode_atts(
			array(
				'class'         => ''. $option_class . '',
				'iconclass'     => ''. $option_icon_classes . '',
				'sharelabel'    => ''. $option_sharelabel . '',
				'hassharedtext' => ''. $option_hassharedtext . '',
				'totalcount'    => ''. $option_totalcount . '',
				'facebook'      => ''. $option_facebook . '',
				'twitter'       => ''. $option_twitter . '',
				'googleplus'    => ''. $option_googleplus . '',
				'linkedin'      => ''. $option_linkedin . '',
				'pinterest'     => ''. $option_pinterest . '',
			),
			$atts
		)
	);
	// Check if any social is active, then draw html
	if ( $facebook == 'yes' || $facebook == 1 ) {
		$facebook_enabled       = true;
	} else {
		$facebook_enabled       = false;
	}
	if ( $twitter == 'yes' || $twitter == 1 ) {
		$twitter_enabled            = true;
	} else {
		$twitter_enabled            = false;
	}
	if ( $googleplus == 'yes' || $googleplus == 1 ) {
		$googleplus_enabled    = true;
	} else {
		$googleplus_enabled    = false;
	}
	if ( $linkedin == 'yes' || $linkedin == 1 ) {
		$linkedin_enabled         = true;
	} else {
		$linkedin_enabled         = false;
	}
	if ( $pinterest == 'yes' || $pinterest == 1 ) {
		$pinterest_enabled        = true;  //OBS! Selected or large image must exist per page for Pinterest to work
	} else {
		$pinterest_enabled        = false;
	}

	if ( $facebook_enabled OR $twitter_enabled OR $googleplus_enabled OR $linkedin_enabled  OR $pinterest_enabled ) {
		ob_start();
		// we add featured image if any, ranks higher than pic coming from API (see fast-social-count.js there another check is made)
		if ( isset( $post->ID ) && has_post_thumbnail( $post->ID ) ) {
			$selectedimage = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large' );
			$selectedimage = $selectedimage[0];
		} else {
			$selectedimage = '';
		}

		$output = '<ul class="list-inline fast-social-count" data-selected-image="' . esc_attr( $selectedimage ) . '">';

			$output .= '<li class="share_label">';
			$output .= $sharelabel; //defaults to empty but can be set with option or shortcode
			$output .= '</li>';

			if ( $facebook_enabled ) {
				$output .= '<li class="fsc_share_button share_facebook"><span class="fa fa-fw fa-facebook ' . $iconclass . '"></span>';
				$output .= '</li>';
			}

			if ( $twitter_enabled ) {
				$output .= '<li class="fsc_share_button share_twitter"><span class="fa fa-fw fa-twitter ' . $iconclass . '"></span>';
				$output .= '</li>';
			}

			if ( $googleplus_enabled ) {
				$output .= '<li class="fsc_share_button share_googleplus"><span class="fa fa-fw fa-google-plus ' . $iconclass . '"></span>';
				$output .= '</li>';
			}

			if ( $linkedin_enabled ) {
				$output .= '<li class="fsc_share_button share_linkedin"><span class="fa fa-fw fa-linkedin ' . $iconclass . '"></span>';
				$output .= '</li>';
			}

			if ( $pinterest_enabled ) {
				$output .= '<li class="fsc_share_button share_pinterest"><span class="fa fa-fw fa-pinterest ' . $iconclass . '"></span>';
				$output .= '</li>';
			}
			//Get total shares for this page
			$this_url = get_permalink();

			//if settings tells us to show total count of shares, then we fetch and show total count
			if ( 'yes' == $totalcount || 1 == $totalcount ) {
				list( $total_shares, $individual_shares, $latest_share_count ) = social_total_count( $this_url, $option_cachetimer, $facebook_enabled, $twitter_enabled, $googleplus_enabled, $linkedin_enabled, $pinterest_enabled );
				// print section only if there is  shares
				if ( ! empty( $total_shares ) || ( '0' != $total_shares ) ) {
					$output .= '<li class="total_shares" data-share="' . $individual_shares .'">' .  $latest_share_count . '<small class="text-muted"><span class="fa fa-fw fa-heart"></span>';
					$output .= $total_shares . ' ' . $hassharedtext;
					$output .= '</small></li>';
				}
			} else {
					$output .= '<li class="total_shares_hide"><!-- ' . __( 'Fetching of Fast Social Count is turned off', 'fast-social-count' ) . ' --></li>';
			}

		$output .= '</ul>';

		$innerhtml = '<div class="row"><div class="' . $class . '">' . $output . '</div></div>';
		echo $innerhtml;
		return ob_get_clean();
	}
}

function social_total_count( $url, $option_cachetimer, $facebook_enabled, $twitter_enabled, $googleplus_enabled, $linkedin_enabled, $pinterest_enabled ) {
	//see if we have a cached number to use
	$key               = 'social_total_count_' . $option_cachetimer . '_' . md5( $url );
	$keyindividual     = 'social_individual_count_' . $option_cachetimer . '_' . md5( $url );
	$counted_date_key  = 'counted_date_' . $option_cachetimer . '_' . md5( $url );
	$total_shares      = wp_cache_get( $key,'social_total_count' );
	$individual_shares = wp_cache_get( $keyindividual, 'social_individual_count' );
	$counted_date      = wp_cache_get( $counted_date_key, 'counted_date' );

	if ( false === $total_shares ) {
	//if no cache, run the other functions, which also are cached if hosts supports Wordpress Object Cache
		//http://codex.wordpress.org/Class_Reference/WP_Object_Cache
		if ( $facebook_enabled ) {
			$fbshares  = get_facebook_count( $url );
			$fbshares  = '-Facebook-' . $fbshares . '-';
		}
		if ( $twitter_enabled ) {
			$tweets     = get_tweet_count( $url );
			$tweets     = 'Twitter-' . $tweets;
		}
		if ( $googleplus_enabled ) {
			$gshares   = get_googleplus_count( $url, $option_cachetimer );
			$gshares   = 'GooglePlus-' . $gshares . '-';
		}
		if ( $linkedin_enabled ) {
			$lnshares  = get_linkedin_count( $url );
			$lnshares  = 'LinkedIn-' . $lnshares . '-';
		}
		if ( $pinterest_enabled ) {
			$pins = get_pinterest_count( $url );
			$pins = 'Pinterest-' . $pins;
		}
		//See individual count in source (data-tag/comment)
		$individual_shares = $tweets . $fbshares . $gshares . $lnshares . $pins;
		//Format total for balloon
		$total_shares = $tweets + $fbshares + $gshares + $lnshares + $pins;
		$total_shares = nice_number( $total_shares );
		//information about when it was cached last
		$thismoment            = current_time( 'Y-m-d H:i:s' );
		wp_cache_set( $key, $total_shares, 'social_total_count', $option_cachetimer );
		wp_cache_set( $keyindividual, $individual_shares, 'social_individual_count', $option_cachetimer );
		wp_cache_set( $counted_date_key, $thismoment, 'counted_date', $option_cachetimer );

		$latest_share_count = '<!-- ' . __( 'Last share count by Fast Social Count just this minute. Result not cached.', 'fast-social-count' ) . '  -->';
	} else {
		$formatcachetime    = sprintf( '%02d hours and %02d minutes', ($option_cachetimer / 3600), ($option_cachetimer / 60 % 60) );
		$latest_share_count = '<!-- ' . __( 'Last share count by Fast Social Count on', 'fast-social-count' ) . ' ' .  $counted_date .  __( 'Cache set to', 'fast-social-count' )  . ' ' . $formatcachetime . ' -->';
	}
	return array( $total_shares, $individual_shares, $latest_share_count );
}

/******/// functions for counting the likes, shares, tweets, pinterests from different network
// gets used if no number already is cached
function get_tweet_count( $url ) {
	$url              = urlencode( $url );
	$twitter_endpoint = 'http://cdn.api.twitter.com/1/urls/count.json?url=%s';
	$file_data        = get_response( sprintf( $twitter_endpoint, $url ) );
	$json             = json_decode( $file_data, true );
	return $json['count'];
}

function get_facebook_count( $url ) {
	//$facebook_endpoint = 'http://graph.facebook.com/?id=' . $url ;
	$url               = urlencode( $url );
	$facebook_endpoint = 'http://api.ak.facebook.com/restserver.php?v=1.0&method=links.getStats&urls='. $url .'&format=json';
	$file_data         = get_response( $facebook_endpoint );
	$json              = json_decode( $file_data, true );
	$total_fb_count          = $json[0]['total_count'];
	return $total_fb_count;
}

function get_googleplus_count( $url ) {
	// do curl request
		$ch = curl_init();
		 curl_setopt( $ch, CURLOPT_URL, 'https://clients6.google.com/rpc?key=AIzaSyCKSbrvQasunBoV16zDH9R33D88CeLr9gQ' );
		 curl_setopt( $ch, CURLOPT_POST, 1 );
		 curl_setopt( $ch, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . $url . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]' );
		 curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		 curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-type: application/json' ) );
		 curl_setopt( $ch, CURLOPT_ENCODING , 'gzip' );
		 $curl_results = curl_exec( $ch );
		 curl_close( $ch );
		 $parsed_results = json_decode( $curl_results, true );
		 $count = $parsed_results[0]['result']['metadata']['globalCounts']['count'];
		 return $count;
}

//helper function for pinterest and linked in
function jsonp_decode( $jsonp, $assoc = false ) { // PHP 5.3 adds depth as third parameter to json_decode
	if ( $jsonp[0] !== '[' && $jsonp[0] !== '{' ) { // we have JSONP
	   $jsonp = substr( $jsonp, strpos( $jsonp, '(' ) );
	}
	return json_decode( trim( $jsonp, '();' ), $assoc );
}
function get_linkedin_count( $url ) {
	$url               = urlencode( $url );
	$linkedin_endpoint = 'http://www.linkedin.com/countserv/count/share?url=' . $url;
	$file_data         = get_response( $linkedin_endpoint );
	$json              = jsonp_decode( $file_data, true );
	return $json['count'];
}
function get_pinterest_count( $url ) {
	$url                = urlencode( $url );
	$pinterest_endpoint = 'http://api.pinterest.com/v1/urls/count.json?url=' . $url;
	$file_data          = get_response( $pinterest_endpoint );
	$json               = jsonp_decode( $file_data, true );
	return $json['count'];
}

function nice_number( $n ) {
	// first strip any formatting; (probably non exists - but..)
	$n = (0 + str_replace( ',', '', $n ) );
	// is this a number? (o yes, almost certain - but...)
	if ( ! is_numeric( $n ) ) return false;
	// now filter it;
	if ( $n > 1000000000000 ) return round( ($n / 1000000000000), 1 ) . 't';
	else if ( $n > 1000000000 ) return round( ($n / 1000000000), 1 ) . 'b';
	else if ( $n > 1000000 ) return round( ($n / 1000000), 1 ) . 'm';
	else if ( $n > 1000 ) return round( ($n / 1000), 1 ) . 'k';
	return number_format( $n );
}

/*** Retrieves the response from the specified URL using one of PHP's outbound request facilities.
 * @params  $url    The URL of the feed to retrieve.
 * @returns         The response from the URL; null if empty.
 */
function get_response( $url ) {
	$response = null;
	// First, we try to use wp_remote_get
	$response = wp_remote_get( $url );
	if ( is_wp_error( $response ) ) {
		// If that doesn't work, then we'll try file_get_contents
		$response = file_get_contents( $url );
		if ( false == $response ) {
			// And if that doesn't work, then we'll try curl
			$response = $this->curl( $url );
			if ( null == $response ) {
				$response = 0;
			}
		}
	}
	// If the response is an array, it's coming from wp_remote_get,
	// so we just want to capture to the body index for json_decode.
	if ( is_array( $response ) ) {
		$response = $response['body'];
	}
	return $response;
} // end get_response

/**Defines the function used to initial the cURL library.
 * @param  string  $url        To URL to which the request is being made
 * @return string  $response   The response, if available; otherwise, null
 */
function curl( $url ) {
	$curl = curl_init( $url );
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $curl, CURLOPT_HEADER, 0 );
	curl_setopt( $curl, CURLOPT_USERAGENT, '' );
	curl_setopt( $curl, CURLOPT_TIMEOUT, 10 );
	$response = curl_exec( $curl );
	if ( 0 !== curl_errno( $curl ) || 200 !== curl_getinfo( $curl, CURLINFO_HTTP_CODE ) ) {
		$response = null;
	}
	curl_close( $curl );
	return $response;
} // end curl

// Add settings link on plugin page
function fast_social_count_settings_link( $links ) {
  $settings_link = '<a href="options-general.php?page=fast-social-count-admin">' . __( 'Settings' ) . '</a>';
  array_unshift( $links, $settings_link );
  return $links;
}

$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'fast_social_count_settings_link' );

/****************************************************************************************/
require_once( 'fsc-lib/fast-social-count-options.php' );

/***********************************************************************************************/
