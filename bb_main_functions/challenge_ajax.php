<!-- SELECCION DE PROCESO RSA PARA REGISTRO DEL USUARIO-->
    
<?php 
    	$server_name = $GLOBALS["SERVER_NAME"];
	//$HTTP_HOST = $GLOBALS["HTTP_HOST"];
	$imagesPath = $ini->read_var("site", "aaRSA_pathImg");
	
	switch( $state_collect )
	{
		case "cg_qst_frac":
			include("eztransaccion/user/include/challenge_QstRSA_frac.inc");
		break;
		
		case "cg_qst_process":
			include("eztransaccion/user/include/challenge_QstRSA_process.inc");
		break;
	
	}
	
?>