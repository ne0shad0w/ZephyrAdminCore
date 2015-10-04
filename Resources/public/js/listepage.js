// JavaScript Document
function activate(id){
    $.ajax({ 
        url: "/admin/page/actif/" + id  , 
		cache: false ,
    }).done( function(html){Success(id,html);});
}



function Success(id,html) {
	if ( html == 0 ) {
		$( document.getElementById('page_'+id) ).addClass("danger") ;	
	} else {
		$( document.getElementById('page_'+id) ).removeClass("danger") ;
	}
}