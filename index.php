<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

//used in HTML
set_global_defaults();

$posts = array();
if ( ! isset( $_SESSION['access_token'] ) ){
	// we have a response from FB
	if ( isset( $_GET['code'] ) && $_GET['code'] != '' ){
		set_access_token();
		populate_user_content();
	}
}else{
	if ( isset( $_GET['logout'] ) && $_GET['logout'] == 'true' ){
		remove_access_token();
	}else{
		populate_user_content();
	}
}



// used in html
function set_global_defaults(){
	global $user_name, $avatar_src, $posts, $from_interval, $to_interval, $max_interval;

	$user_name = '';
	$avatar_src = '';

	$from_interval = date( 'Y-m-d', time() - ( 60 * 60 * 24 * 7 ) );
	if ( isset( $_GET['from_interval'] ) && $_GET['from_interval'] != '' ){
		$from_interval = $_GET['from_interval'];
	}

	$max_interval = date( 'Y-m-d', time() );
	$to_interval = $max_interval;
	if ( isset( $_GET['to_interval'] ) && $_GET['to_interval'] != '' ){
		$to_interval = $_GET['to_interval'];
	}
}

function populate_user_content(){
	$user = get_user();
	if ( $user !== false ){
		global $user_name, $avatar_src, $posts; 
		$user_name = $user['name'];
		$avatar_src = get_avatar( $user['id'] );
		$posts = get_feed( $user['id'] );

	}
}

// get FB access token based on the code received
function set_access_token(){
	$url = 'https://graph.facebook.com/v2.11/oauth/access_token?client_id=1709788505943326&redirect_uri=http://localhost/&client_secret=95db116abe47a843cfc97850b5701502&code=' . $_GET['code'];
	 
		//Use file_get_contents to GET the URL in question.
		$contents = file_get_contents($url);
		 
		//If $contents is not a boolean FALSE value.
		if($contents !== false){
			$contents=json_decode($contents);
		    $_SESSION['access_token'] = $contents->access_token;
		}
}

// log out of facebook delete access token
function remove_access_token(){
	session_destroy();
}

// get user id and name based on token
function get_user(){
	$url = 'https://graph.facebook.com/me?access_token=' . $_SESSION['access_token']; 

	$contents = file_get_contents($url);
	if($contents !== false){
		$contents = json_decode($contents);
	    $user['id'] = $contents->id;
	    $user['name'] = $contents->name;
	    return $user;
	}
	return false;
}

// get avatar based on user id
function get_avatar( $user_id ){
	$url = 'https://graph.facebook.com/' . $user_id . '/picture?redirect=false&type=large';
	$contents = file_get_contents($url);
	if( $contents !== false ){
		$contents= json_decode($contents);
		if ( isset( $contents->data->url ) ){
	    	return $contents->data->url;
		}
	}
	return '';	
}

// get posts from last week
function get_feed( $user_id ){
	$url = 'https://graph.facebook.com/' . $user_id . '/feed?access_token=' . $_SESSION['access_token'] .'&fields=picture,message,created_time';
	$contents = file_get_contents($url);
	if( $contents !== false ){
		$contents = json_decode($contents);
		if ( isset( $contents->data ) ){
			$posts = array();
			foreach ( $contents->data as $key => $post ){
				if ( is_within_interval( $post->created_time ) ) {
					// only keep posts from last week
					$posts[] = $post;
				}
			}
			return $posts;
		}
	}
	return false;	
}

// return true if date is less than a week from now
function is_within_interval( $created_date ){
	// default to interval from 7 days ago until present

	$to = time();
	// end of current day
	$to = strtotime("tomorrow", $to) - 1;
	// begining of day from 7 days ago
	$from = $to - ( 60 * 60 * 24 * 8 ) + 1 ;

	if ( isset( $_GET['from_interval']) && $_GET['from_interval'] != '' ){
		$from = strtotime( $_GET['from_interval'] );
	}
	if ( isset( $_GET['to_interval']) && $_GET['to_interval'] != '' ){
		$to = strtotime( $_GET['to_interval'] ) ;
		$to = strtotime("tomorrow", $to) - 1;
	}
	if ( $from > $to ){
		$from = $to - ( 60 * 60 * 24 ) + 1;
	}

	$date = strtotime($created_date);

	if ( $date >= $from && $date <= $to ){
		return true;
	}else{
		return false;
	}
}

require('index.html');

?>
