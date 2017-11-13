/* make the API call */

function get_avatar(user_id){
	var user_avatar = document.getElementById("user_avatar");
	if ( user_avatar != null ){
		return;
	}
	FB.api(
	    "/"+ user_id + "/picture",
	    function (response) {
	      if (response && !response.error) {
	      	var avatar = document.getElementById('avatar_container');
	      	var image = document.createElement('img');
	      	image.setAttribute( 'id', 'user_avatar' );
	      	image.setAttribute( 'src', response.data.url );
	      	image.setAttribute( 'height', response.data.height );
	      	image.setAttribute( 'width', response.data.height );
			avatar.appendChild(image);                              
	      }
	      
	    },
	    { type: 'large'}
	);
}

function get_feed(user_id){
	FB.api(
	    "/"+ user_id + "/feed",
	    function (response) {
	  
	    }
	);
}