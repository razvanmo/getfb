/* make the API call */

function get_avatar(user_id){
	console.log(user_id);
	FB.api(
	    "/"+ user_id + "/picture",
	    function (response) {
	      if (response && !response.error) {
	      	var avatar = document.getElementById('avatar');
	      	var image = document.createElement('img');
	      	image.setAttribute( 'src', response.data.url );
	      	image.setAttribute( 'height', response.data.height );
	      	image.setAttribute( 'width', response.data.height );
			avatar.appendChild(image);                              
	      }
	      
	    },
	    { type: 'large'}
	);
}