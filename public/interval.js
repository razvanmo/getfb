function check_interval( changed_input_id ){
	var input = document.getElementById( changed_input_id );
	if ( input ){
		var value = input.value;

		if ( changed_input_id == 'from_interval' ){
			input = document.getElementById( 'to_interval' );
			input.setAttribute( 'min', value );
		}else if ( changed_input_id == 'to_interval' ) {
			input = document.getElementById( 'from_interval' );
			input.setAttribute( 'max', value );
		}
	}
}

window.onload = function (){
	check_interval('to_interval');
	check_interval('from_interval');
}