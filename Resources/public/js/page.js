// JavaScript Document

function SaveHTML(id){
	
    console.log('save id : ' + id) ; 
    $.ajax({ 
        url: "/admin/page/html/update/"+id,
		type: "POST",
		data: { html : document.getElementById('html').value },
		cache: false ,
           	}).done( function(){
				console.log('correct') ; 
				$(document.getElementById('message')).css( { opacity : 1 , color : 'green'});
				document.getElementById('message').innerHTML = "Enregistrement réussi.";
				$(document.getElementById('message')).animate( { opacity : 0 } ,10000);
			}).fail(function(){
				$(document.getElementById('message')).css( { opacity : 1 , color : 'red'});
				document.getElementById('message').innerHTML = "Un problème lors de l'enregistrement est survenue.";
			});

	
}