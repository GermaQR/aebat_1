$(document).foundation();
$(document).ready(function(){

	$( "#radiobutton1" ).prop('checked', true);

	$( "#radiobutton1" ).on( "click", function() {
	 
	        //alert("Está activado ida y vuelta"); 
	        $(".viaje-vuelta").removeClass("no-visible");   
	 

	});	

	$( "#radiobutton2" ).on( "click", function() {
	 
	        //alert("Está activado solo ida");
	        $(".viaje-vuelta").addClass("no-visible");  
	 

	});	


});