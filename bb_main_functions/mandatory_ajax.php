<!-- SELECCION DE PROCESO RSA PARA REGISTRO DEL USUARIO-->
    
<?php 
    	$server_name = $GLOBALS["SERVER_NAME"];
	//$HTTP_HOST = $GLOBALS["HTTP_HOST"];
	$imagesPath = $ini->read_var("site", "aaRSA_pathImg");
	
	switch( $state_collect )
		{
			case "mt_img_frac":
				include("eztransaccion/user/include/mandatory_ImgRSA_frac.inc");
			break;
			
			case "mt_img_confirm":
				include("eztransaccion/user/include/mandatory_ImgRSA_confirm.inc");
			break;
			
			case "mt_img_process":
				include("eztransaccion/user/include/mandatory_ImgRSA_process.inc");
			break;
			
			case "mt_qst_frac":
				include("eztransaccion/user/include/mandatory_QstRSA_frac.inc");
			break;
			
			case "mt_qst_confirm":
				include("eztransaccion/user/include/mandatory_QstRSA_confirm.inc");
			break;
			
			case "mt_qst_process":
				include("eztransaccion/user/include/mandatory_QstRSA_process.inc");
			break;
		
		}
	
?>