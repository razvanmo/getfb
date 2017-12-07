<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

//used in HTML
global $user_name, $avatar_src, $posts;

$user_name = '';
$avatar_src = '';
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
				if ( less_than_a_week_ago( $post->created_time ) ) {
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
function less_than_a_week_ago( $created_date ){
	$date = strtotime($created_date);
	$now = time();
	$diff = abs($now - $date);

	// 60 seconds * 60 minutes * 24 hours = day
	$days = $diff / (60 * 60 * 24);
	if ( $days <= 7 ){
		return true;
	}else{
		return false;
	}
}

require('index.html');

?>
