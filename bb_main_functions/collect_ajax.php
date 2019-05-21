<!-- SELECCION DE PROCESO RSA PARA REGISTRO DEL USUARIO-->
    
<?php 
	//NEXIONS Sep2012 210446- RSA AA Enrolar clientes, INICIO
	include_once("ezuserbb/include/adaptiveAuthenticationRSA.inc");
	include_once( "ezsession/classes/ezsession.php" );
	include_once( "classes/INIFile.php" );
	include_once( "classes/ezlog.php" );
	include_once( "classes/ezlogdb.php" );
	include_once( "eztransaccion/user/include/controles_javascript.inc");	

	$session =& eZSession::globalSession();
	if( !$session->fetch() )
		$session->store();

	$ini =& $GLOBALS["GlobalSiteIni"];
	//NEXIONS Sep2012 210446- RSA AA Enrolar clientes, FIN	

	$state_collect = $_POST["st_collect"];
    	$server_name = $GLOBALS["SERVER_NAME"];
	//$HTTP_HOST = $GLOBALS["HTTP_HOST"];
	$imagesPath = $ini->read_var("site", "aaRSA_pathImg");
	
	//NEXIONS Sep2012 210446- RSA AA Enrolar clientes, INICIO
	if( empty( $state_collect ))
	{
		//echo "<input type='hidden' name='state_collect' id='state_collect' value='collect_i' >";
		$state_collect = "ct_img_frac";
	}
	//NEXIONS Sep2012 210446- RSA AA Enrolar clientes, FIN
	
	switch( $state_collect )
		{
			case "ct_img_frac":
				include("eztransaccion/user/include/collect_ImgRSA_frac.inc");
			break;
			
			case "ct_img_confirm":
				include("eztransaccion/user/include/collect_ImgRSA_confirm.inc");
			break;
			
			case "ct_img_process":
				include("eztransaccion/user/include/collect_ImgRSA_process.inc");
			break;
			
			case "ct_qst_frac":
				include("eztransaccion/user/include/collect_QstRSA_frac.inc");
			break;
			
			case "ct_qst_confirm":
				include("eztransaccion/user/include/collect_QstRSA_confirm.inc");
			break;
			
			case "ct_qst_process":
				include("eztransaccion/user/include/collect_QstRSA_process.inc");
			break;
		
		}
	
?>