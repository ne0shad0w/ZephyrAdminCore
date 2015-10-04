

$(document).ready(function() 
{  
 $("span").focus(function()  
  { 
   
  	var d = new Date();
	var n = d.getTime();
     console.log("click : " + n );

   if( $(this).attr("contenteditable") == "true") 
   {    
    // le"id" du td doit contenir l'id de la BDD 
    // le "name" doit contenir le nom du champ à modifier 
    var contenu_avant = $(this).text(); 
    var id_bdd = $(this).attr("id");
	 var type = $(this).attr("alt"); 
    var champ_bdd = $(this).attr("name"); 
	var envoyer = false ;
	if ( contenu_avant == "") contenu_avant = 'null'; 
				
    //console.log("avant =" + contenu_avant); 
                 
    	$(this).focusout(function() 
        { 
		
         var contenu_apres = $(this).text();
		 if ( contenu_apres == "") contenu_apres = 'null'; 
         console.log("envoyer = " + envoyer); 
         
         if ( (contenu_avant != contenu_apres) && envoyer === false ) 
         { 
		 	var contenu_apres = $(this).text();
		 	envoyer = true ;
           	parametre='id='+id_bdd+'&champ='+champ_bdd+'&contenu=' + contenu_apres ; 
         	console.log(parametre) ; 
          	$.ajax({ 
            	url: "/admin/page/"+type+"/update/"+champ_bdd+"/"+id_bdd , //+"/" + contenu_apres,
				method: "POST",
				data: { 'contenu' : contenu_apres },
				cache: false ,
				context: this
           	}).done( function(){Success(this);}).fail( function(){Error(this);});
			
          } 
          
        }); 
     
   };     
             
  }); 
  
  

});   
function Utf8ArrayToStr(array) {
    var out, i, len, c;
    var char2, char3;

    out = "";
    len = array.length;
    i = 0;
    while(i < len) {
    c = array[i++];
    switch(c >> 4)
    { 
      case 0: case 1: case 2: case 3: case 4: case 5: case 6: case 7:
        // 0xxxxxxx
        out += String.fromCharCode(c);
        break;
      case 12: case 13:
        // 110x xxxx   10xx xxxx
        char2 = array[i++];
        out += String.fromCharCode(((c & 0x1F) << 6) | (char2 & 0x3F));
        break;
      case 14:
        // 1110 xxxx  10xx xxxx  10xx xxxx
        char2 = array[i++];
        char3 = array[i++];
        out += String.fromCharCode(((c & 0x0F) << 12) |
                       ((char2 & 0x3F) << 6) |
                       ((char3 & 0x3F) << 0));
        break;
    }
    }

    return out;
}
function Success(cible){
	console.log("font");
	var color = $(cible).parent().css("background-color");
	$(cible).parent().stop().animate({ backgroundColor : '#0C6' }).animate({ backgroundColor : '' }).animate({backgroundColor : '#0C6' }).animate({ backgroundColor : '' }).animate({backgroundColor : '#0C6' }).animate({ backgroundColor : color },5000);
	$(cible).die();
	if ( $(cible).parent().attr("id") == "nomtable" ) {
		var full = location.protocol+'//'+location.hostname ;
		$(cible).parent().next('td').html("<a href='" + full + "/page/" + $(cible).parent().text().replace(/ /g,'') + "'>/page/" + $(cible).parent().text().replace(/ /g,'') + "</a>")  ;
	}
	
}
function Error(cible){
	var color = $(cible).parent().css("background-color");
	$(cible).parent().stop().animate({ backgroundColor : '#F00' }).animate({ backgroundColor : '#fff' }).animate({backgroundColor : '#F00' }).animate({ backgroundColor : '#fff' }).animate({backgroundColor : '#F00' }).animate({ backgroundColor : color },15000);
	$(cible).die();
}

function edit_champ(id,n){
	document.getElementsByName(id)[n].focus();	
}

function save_champ(id,n , sel , value ){
	console.log(sel);
	
    var id_bdd = $(sel).attr("id");
	var type = $(sel).attr("alt"); 
    var champ_bdd = $(sel).attr("name"); 
	var envoyer = false ;
    console.log("envoyer = " + envoyer);
	 		var contenu_apres = value;

            parametre='id='+id_bdd+'&champ='+champ_bdd+'&contenu=' + contenu_apres ; 
         	console.log(parametre) ; 
        
		 	envoyer = true ;
          	$.ajax({ 
            	url: "/admin/page/"+type+"/update/"+champ_bdd+"/"+id_bdd , 
				method: "POST",
				data: { 'contenu' : contenu_apres },
				cache: false ,
           	}).done( function(){Success(sel);}).fail( function(){Error(sel);});
  
}
// JavaScript Document