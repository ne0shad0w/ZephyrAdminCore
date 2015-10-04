$(function() {
	var host = window.location.pathname ;
	var href_c;
	
	//console.log("start check");
	$( "li > a" ).each(function() {
		var a_href = $( this ).attr('href');
		href_c = a_href.replace("../../../../../../..","");
		if ( decodeURI(host) == decodeURI(href_c) ) {
			$( this ).parent().addClass( "Selected" );
			return false;	
		}
	});
});