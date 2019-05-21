<link rel='stylesheet' type='text/css' media='screen' href='/eztransaccion/user/include/js_calendar/calendar-blue.css' />
<script language="javascript" src="/eztransaccion/user/include/js_calendar/calendar.js" />
<script language="javascript" src="/eztransaccion/user/include/js_calendar/lang/calendar-es.js" />
<script language="javascript" src="/eztransaccion/user/include/jscalendar/calendar-setup.js" />
<?php
$session->setVariable("calendario",$session->variable("calendario")+1);
($parametros['Access']!=""?$Access=ltrim(rtrim($parametros['Access'])):0);
($parametros['PIN']!=""?$PIN=ltrim(rtrim($parametros['PIN'])):0);
($parametros['ConfirmPIN']!=""?$ConfirmPIN=ltrim(rtrim($parametros['ConfirmPIN'])):0);
($parametros['PassCode']!=""?$PassCode=ltrim(rtrim($parametros['PassCode'])):0);

$host = $ini->read_var( "site", "RSAHost" );
$port = $ini->read_var( "site", "RSAPort" );

include("eztransaccion/user/include/tcpipnexions.php");
include("eztransaccion/user/include/xmlparser.inc");
include("eztransaccion/user/include/estilo.inc");
include("eztransaccion/user/include/estilo_acladomi.inc");
include("eztransaccion/user/include/acladomi_functions.inc");

function ControlAcceso($menu,$usr)
{
        global $errors;

        //Mensaje - Nexions HM
        $trans = "<?xml version='1.0'?>";
        $trans .= "<mensaje><trxn value='adm'/><accion value='ValidaOpMenu'/><tipomsj value='rqs'/><CustID value='$usr'/>";
        $trans .= "<Opmenu value='" . $menu . "'/>";
        $trans .= "</mensaje>";
        //Mensaje

        //Conexion - Nexions HM
        $conn = new TCPIPNexions();

        if(!$conn->connect())
              echo "<script> alert('  4501 - " .  $errors[4501]  . " ') </script>";

        if(!$conn->send($trans))
              echo "<script> alert('  4501 - " .  $errors[4501]  . " ') </script>";

        $ans = $conn->receive();

        if(trim($ans)=="")
              echo "<script> alert('  4501 - " .  $errors[4501]  . " ') </script>";

        $xml_struct = parse_xml(trim($ans));

        if (  trim($xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"]) != "0000" )
                return  $xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"] ." - " .$xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["DESC"];

        $conn->close();
        //Conexion

        return "0000";

}


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

		if ($_POST['Access'] != "GetMovimientos" && $_POST['Access'] != "loadDetalleCargo")
		{
	?>
		<h1>Aclaraci&oacute;n de Domiciliaci&oacute;n</h1>
		<hr noshade="noshade" size="4" />
		<br />
	<?php
		}
	?>
	<?php

	//NEXAJAX - Instrucciones para consumir ProcesarAjax

	if ( !isset( $Access ) && !isset( $_POST['Access'] ))
	{
		$result = ControlAcceso("AclaraDomi",$usr);
		if ( $result!="0000")
		{
			$result = preg_replace( "/á/", "&aacute;", $result );     	/* HB AGL*/
		$result = preg_replace( "/é/", "&eacute;", $result );         	/* HB AGL*/
		$result = preg_replace( "/í/", "&iacute;", $result );			/* HB AGL*/
        	$result = preg_replace( "/ó/", "&oacute;", $result );    	/* HB AGL*/
		$result = preg_replace( "/ú/", "&uacute;", $result ); 			/* HB AGL*/
		$result = preg_replace( "/ñ/", "&ntilde;", $result );         	/* HB AGL*/
		$result = preg_replace( "/Ñ/", "&Ntilde;", $result );         	/* HB AGL*/
		$result = preg_replace( "/Á/", "&Aacute;", $result );         	/* HB AGL*/
		$result = preg_replace( "/É/", "&Eacute;", $result );         	/* HB AGL*/
		$result = preg_replace( "/Í/", "&Iacute;", $result );         	/* HB AGL*/
		$result = preg_replace( "/Ó/", "&Oacute;", $result );         	/* HB AGL*/
		$result = preg_replace( "/Ú/", "&Uacute;", $result );         	/* HB AGL*/
		$result = preg_replace( "/°/", "&deg;", $result );   		 	/* HB AGL*/
			echo $result;
			return;
		}

		$_POST['Access'] = "FrAc";
	}

	//NEXAJAX - Instrucciones para consumir ProcesarAjax

	$tr = new eZTransaccion( );

	$transaction_buffer = "";

	//NEXAJAX - Instrucciones para consumir ProcesarAjax
	switch ( $_POST['Access'] )
	{
	//NEXAJAX - Instrucciones para consumir ProcesarAjax

		case "FrAc":
			include("eztransaccion/user/include/acladomi_frac.inc");
			break;
		case "Confirm":
			include("eztransaccion/user/include/acladomi_confirm.inc");
			break;
		case "Process":
			include("eztransaccion/user/include/acladomi_process.inc");
			break;
		case "GetMovimientos":
			include("eztransaccion/user/include/acladomi_getmovimientos.inc");
			break;
		case "loadDetalleCargo":
			include("eztransaccion/user/include/acladomi_loaddetallecargo.inc");
			break;

	}

	$tr->blog( $qki, "ACLADOMI", "", "", "", "", "" );


?>
