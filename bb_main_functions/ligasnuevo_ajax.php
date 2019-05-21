<?php
($parametros['Access']!=""?$Access=ltrim(rtrim($parametros['Access'])):0);
($parametros['PIN']!=""?$PIN=ltrim(rtrim($parametros['PIN'])):0);
($parametros['ConfirmPIN']!=""?$ConfirmPIN=ltrim(rtrim($parametros['ConfirmPIN'])):0);
($parametros['PassCode']!=""?$PassCode=ltrim(rtrim($parametros['PassCode'])):0);
($parametros['Tipo']!=""?$tipo=ltrim(rtrim($parametros['Tipo'])):0);
($parametros['Cliente']!=""?$cliente=ltrim(rtrim($parametros['Cliente'])):0);
($parametros['Cuenta']!=""?$cuenta=ltrim(rtrim($parametros['Cuenta'])):0);
($parametros['Sub']!=""?$sub=ltrim(rtrim($parametros['Sub'])):0);
($parametros['Clabe']!=""?$clabe=ltrim(rtrim($parametros['Clabe'])):0);

//echo "cuenta:" . $cuenta;
//echo "cliente:" . $cliente;


$host = $ini->read_var( "site", "RSAHost" );
$port = $ini->read_var( "site", "RSAPort" );

include("eztransaccion/user/include/tcpipnexions.php");
include("eztransaccion/user/include/xmlparser.inc");
include("eztransaccion/user/include/estilo.inc");
include("eztransaccion/user/include/estilo_divisas.inc");
//include("eztransaccion/user/include/ligas_functions.inc");
include("middleware/ligas/ligas_functions.inc");
include_once("eztransaccion/user/include/controles_javascript.inc");
include_once( "eztransaccion/classes/ezlog.php" );	// JAC NXN 09NOV 2012

?>


	<?php

	//echo "POST" . $_POST['Access'];

	if ( !isset( $Access ) && !isset( $_POST['Access'] ))
  {
		$_POST['Access'] = "FrAc";
  }

	$tr = new eZTransaccion( );

	$transaction_buffer = "";

	if($_POST['Access'] != "ValidaCta" && $_POST['Access'] != "CuentasCliente")
	{
	  echo "<h1>Alta de Cuentas BanBaj&iacute;o</h1>";
	  echo "<hr noshade='noshade' size='4' />";
	  echo "<br />";
  }
        
	switch ( $_POST['Access'] )
	{
		case "FrAc":
			//eZLog::writeNotice( ".DBA....en ligasNVO..1..invocandoligas2");  // NXN 09Nov2012		MAOS Oct2013 Quitar Notice
			//include("eztransaccion/user/include/ligas_frac.inc");
			//include("middleware/ligas/ligas_frac2.inc");
			include("middleware/ligas/ligas_frac.inc");
			break;
			//SEccion para seleccionar las cuentas de la operación
		case "Confirm":
			include("eztransaccion/user/include/ligas_confirm.inc");
			break;
		case "Process":

			//eZLog::writeNotice( ".DBA....Acces...*" . $_POST['Access'] . "*");	MAOS Oct2013 Quitar Notice
			//eZLog::writeNotice( ".DBA....Acces...*" . $_POST['producto'] . "*");	MAOS Oct2013 Quitar Notice
			//eZLog::writeNotice( ".DBA....Acces...*" . $_POST['sub'] . "*");		MAOS Oct2013 Quitar Notice
			include("eztransaccion/user/include/ligas_process.inc");
			break;
		case "ValidaCta":
			include("eztransaccion/user/include/valida_cuenta.inc");
			break;
		case "CuentasCliente":
			include("eztransaccion/user/include/cuentas_cliente.inc");
			exit();
			break;

	}

	$tr->blog( $qki, "LIGAS", "", "", "", "", "" );


?>

