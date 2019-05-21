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
include("eztransaccion/user/include/estilo_vtm.inc");
include("eztransaccion/user/include/recargavtm_functions.inc");
include_once("eztransaccion/user/include/controles_javascript.inc");
include_once("eztransaccion/user/include/general_functions.inc");

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

function trim22(cadena)
{
	for(i=0; i<cadena.length; )
	{
		if(cadena.charAt(i)==" ")
			cadena=cadena.substring(i+1, cadena.length);
		else
			break;
	}

	for(i=cadena.length-1; i>=0; i=cadena.length-1)
	{
		if(cadena.charAt(i)==" ")
			cadena=cadena.substring(0,i);
		else
			break;
	}

	return cadena;
}


							</script>

	<?php

	//echo "POST" . $_POST['Access'];

	//NEXAJAX - Instrucciones para consumir ProcesarAjax

	if ( !isset( $Access ) && !isset( $_POST['Access'] ))
		$_POST['Access'] = "FrAc";

	//NEXAJAX - Instrucciones para consumir ProcesarAjax

	$tr = new eZTransaccion( );

	$transaction_buffer = "";

	if($_POST['Access'] != "datosBene")
        {

		echo "<h1>Recarga Visa Travel Money - Beneficiarios</h1>";
                echo "<hr noshade='noshade' size='4' />";
                echo "<br />";
        }

	//NEXAJAX - Instrucciones para consumir ProcesarAjax
	switch ( $_POST['Access'] )
	{
	//NEXAJAX - Instrucciones para consumir ProcesarAjax

		case "FrAc":
			include("eztransaccion/user/include/recargavtmbene_frac.inc");
			break;
			//SEccion para seleccionar las cuentas de la operación
		case "Confirm":
			include("eztransaccion/user/include/recargavtmbene_confirm.inc");
			break;
		case "Process":
			include("eztransaccion/user/include/recargavtmbene_process.inc");
			break;
		case "datosBene":
			include("eztransaccion/user/include/recargavtmbene_datos.inc");
			break;

	}

	$tr->blog( $qki, "RECARGAVTM", "", "", "", "", "" );


?>
