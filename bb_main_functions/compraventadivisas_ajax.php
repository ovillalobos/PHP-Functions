<?php
($parametros['Access']!=""?$Access=ltrim(rtrim($parametros['Access'])):0);
($parametros['PIN']!=""?$PIN=ltrim(rtrim($parametros['PIN'])):0);
($parametros['ConfirmPIN']!=""?$ConfirmPIN=ltrim(rtrim($parametros['ConfirmPIN'])):0);
($parametros['PassCode']!=""?$PassCode=ltrim(rtrim($parametros['PassCode'])):0);

$host = $ini->read_var( "site", "RSAHost" );
$port = $ini->read_var( "site", "RSAPort" );

include("eztransaccion/user/include/tcpipnexions.php");
include("eztransaccion/user/include/xmlparser.inc");
include("eztransaccion/user/include/estilo.inc");
include("eztransaccion/user/include/estilo_divisas.inc");
include("eztransaccion/user/include/compraventdiv_functions.inc");
include_once("eztransaccion/user/include/controles_javascript.inc");


	?>
	
	<script type="text/javascript">
						
					
						
						function isEnter(e)
						{
							var characterCode;

							if(e && e.which)
							{
								e = e;
								characterCode = e.which;
							}
							else
							{
								characterCode = e.keyCode;
							}

							if(characterCode == 13)
							{
								return true;
							}
							else
							{
								return false;
							}
						}
						
												
					</script>

	<?php
		
	//NEXAJAX - Instrucciones para consumir ProcesarAjax
		
	if ( !isset( $Access ) && !isset( $_POST['Access'] ))
		$_POST['Access'] = "FrAc";
		
	//NEXAJAX - Instrucciones para consumir ProcesarAjax
		
	$tr = new eZTransaccion( );
	
	$transaction_buffer = "";

	if($_POST['Access'] != "TipoCambio")
	{

		echo "<h1>Compra venta de divisas</h1>";
		echo "<hr noshade='noshade' size='4' />";
		echo "<br />";
	}		

	//NEXAJAX - Instrucciones para consumir ProcesarAjax
	switch ( $_POST['Access'] )
	{
	//NEXAJAX - Instrucciones para consumir ProcesarAjax
	
		case "FrAc":
			include("eztransaccion/user/include/compventdiv_frac.inc");
			break;
			//SEccion para seleccionar las cuentas de la operación
		case "FrAc2":
			include("eztransaccion/user/include/compventdiv_frac2.inc");
			break;
		case "Process":
			include("eztransaccion/user/include/compventdiv_process.inc");
			break;
		case "TipoCambio":
			include("eztransaccion/user/include/compventdiv_tipocambio.inc");
			break;

	}
	
	$tr->blog( $qki, "COMPVENTDIV", "", "", "", "", "" );
	

?>
