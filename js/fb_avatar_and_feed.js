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
	      	var avatar_container = document.getElementById('avatar_container');
	      	var image = document.createElement('img');
	      	image.setAttribute( 'id', 'user_avatar' );
	      	image.setAttribute( 'src', response.data.url );
	      	image.setAttribute( 'height', response.data.height );
	      	image.setAttribute( 'width', response.data.height );
			avatar_container.appendChild(image);                              
	      }
	      
	    },
	    { type: 'large'}
	);
}

function get_feed(user_id){

	if ( document.getElementById("feed_list") != null ){
		return;
	}
	FB.api(
	    "/"+ user_id + "/feed?fields=picture,message,created_time",
	    function (response) {
	    	if (response && !response.error) {
	    		if ( response.data.length > 0 ){
	  				var feed_container = document.getElementById('feed_container');
	  				var ul = document.createElement('ul');
	  				ul.setAttribute('id', 'feed_list');
	  				feed_container.appendChild(ul);
	  				for ( var i in response.data ){
	  					if ( less_than_a_week_ago( response.data[i].created_time ) ){
							var li = document.createElement('li');
							li.setAttribute('class', 'post');
							if (response.data[i].picture){
								var img = document.createElement('img');
								img.setAttribute('src', response.data[i].picture );
								img.setAttribute('class','float-left');
								li.appendChild( img );		
							}

							if ( response.data[i].message ){
								var paragraph = document.createElement('p');
								paragraph.innerHTML = response.data[i].message;
								paragraph.setAttribute('class', 'message');
								li.appendChild(paragraph);
							}
							var div = document.createElement('div');
							div.setAttribute('style','clear:both;')
							li.appendChild(div);
							ul.appendChild(li);
						}
					}
	    		}
	  		}
	    }
	);
}

function less_than_a_week_ago( created_date ){
	var date = new Date(created_date);
	var now = new Date();
	var diff = Math.abs(now.getTime() - date.getTime());
  	return diff / (1000 * 60 * 60 * 24) <= 7;
}