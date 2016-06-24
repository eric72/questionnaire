
/* ========================================================
*
* Eric Dos Santos
*
* ========================================================
*
* File: registerpage_js.js;
* Description:Load des scripts de la page inscription register.php.
* 
*
* ======================================================== */



$(function() {


	$.jGrowl.defaults.closer = false;
	$.jGrowl.defaults.easing = 'easeInOutCirc';
	$.jGrowl.defaults.position = 'bottom-right';
	
    
	$("button").on("click", function(){

	 	var testError=$('#phpVar').attr('value');
	 	console.log(testError);

		if (parseInt(testError)>0) {	
			$.jGrowl("Veuillez remplir correctement le formulaire svp ! ",{header:'--ERREURS--',theme:'growl-warning'});		
		}
    });

});