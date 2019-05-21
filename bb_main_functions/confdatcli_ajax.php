<?php
include("eztransaccion/user/include/controles_javascript.inc");
//include("eztransaccion/user/include/general_functions.inc");
$Access = $_POST['Access'];
if(empty($Access) && empty($parametros["Access"]) )
{
		$Access = "FrAc";
}
else
{
	if (!empty($parametros["Access"]))
	{
		$Access = $parametros["Access"];
	}
	else
	{
		$Access = "FrAc";
	}
}
//echo("Accesod :== " . $Access);

function Valida_ClaveActivacion($user, $cemail, $cphone)
{

        $timeout = timeout_check();

        if($timeout != "")
                return $timeout;

        $trans = $trans . "<?xml version='1.0'?>";
        $trans .= "<mensaje><trxn value='pmp'/><accion value='ValidaCveDCL'/><tipomsj value='rqs'/><CustID value='" . $user . "'/><CEmail value='" . $cemail . "'/><CPhone value='" . $cphone . "'/></mensaje>";

		//Conexion - Nexions HM
		$conn = new TCPIPNexions();

		if(!$conn->connect())
		  return "<p>  4501 - " .  $errors[4501]  . "  </p>";

		if(!$conn->send($trans))
		  return "<p>  4501 - " .  $errors[4501]  . "  </p>";

		$ans = $conn->receive();

		$conn->close();

        if(trim($ans)=="")
              return "<script> alert('  4501 - " .  $errors[4501]  . " ') </script>";

        $xml_struct = parse_xml(trim($ans));

		if (trim($xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"]) != "0000")
		{
			return "<p>" . formatCaracter ($xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["DESC"] )  . "<br><br>" .
					"Favor de intentar nuevamente.<br><br>" .
					"C&oacute;digo: (" . $xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"] .")";
		}
		else
		{
			return "<p>Datos enviados con exito.</p><p>Su sesi&oacute;n ser&aacute; cerrada.</p>
					<script>
										//Salida por redireccion.
										setTimeout(function(){window.location='/ingresaBB/';},5000);
									</script>";
		}
		return $text_table;

}

function consulta_ValidaToken($user, $token)
{
        $timeout = timeout_check();

		$token	= asb_test($token, $user);

        if($timeout != "")
                return $timeout;

        $trans = $trans . "<?xml version='1.0'?>";
        $trans .= "<mensaje><trxn value='pmp'/><accion value='ConsDatCli'/><tipomsj value='rqs'/><Token value='" .$token . "'/><CustID value='" . $user . "'/></mensaje>";

		//Conexion - Nexions HM
		$conn = new TCPIPNexions();

		if(!$conn->connect())
		  return "<p>  4501 - " .  $errors[4501]  . "  </p>";

		if(!$conn->send($trans))
		  return "<p>  4501 - " .  $errors[4501]  . "  </p>";

		$ans = $conn->receive();

		$conn->close();

        if(trim($ans)=="")
              return "<p>  4501 - " .  $errors[4501]  . "  </p>";

        $xml_struct = parse_xml(trim($ans));

		if (trim($xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"]) != "0000")
		{
			return "<p> " .  $xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"] . " - " .  formatCaracter ($xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["DESC"] )  . " </p>";
		}
		else
		{
			return "";
		}
}


	$procesarAjax="
					<script>
						function isEnter(e)
						{
							var characterCode;
							if(e && e.which) { e = e; characterCode = e.which;
							} else { characterCode = e.keyCode; }
							if(characterCode == 13) { return true; }
							else { return false; }
						}

						function procesarAjax(access)
						{
							var parametros='';
							var token='';
							var cphone = '';
							var cemail = '';

							if(access == '')
							{
								access = 'FrAc';
							}
							else
							{
								if (access == 'FrAc')
								{
								}
								else
								{
									if (access == 'Confirm')
									{
										token = document.getElementById('token').value;
										parametros = parametros + 'token:' + token;
										if(token.length != 10)
										{
												alert('Por favor ingrese una clave correcta');
												var focus_element;
												focus_element = document.getElementById('token');
												focus_element.focus();

												return false;
										}
									}
									else
									{
										if (access == 'Process')
										{
											cphone 	= document.getElementById('cphone').value;
											cemail 	= document.getElementById('cemail').value;
											parametros = parametros + 'cphone:'  + cphone;
											parametros = parametros + ',cemail:'  + cemail;
											if(cphone.length == 0 && cemail.length == 0)
											{
													alert('Por favor ingrese alguna clave ');
													return false;
											}
											// if(cemail.length == 0 )
											// {
													// alert('Por favor ingrese un correo electr".chr(162)."nico valido ');
													// return false;
											// }
										}
									}
								}
							}

							parametros = parametros + ',Access:'+ access;
//							alert(parametros);
							iniProcesarMenu('confdatcli', parametros);

						}
					</script>";

include("eztransaccion/user/include/tcpipnexions.php");
include("eztransaccion/user/include/xmlparser.inc");
include("eztransaccion/user/include/controles_javascript.inc");

    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "confdatcli.php" );
    $t->setAllStrings();
    $t->set_file( array( "confdatcli_tpl" => "confdatcli.tpl" ) );

    $transaccion_buffer = "";
	$usr = $session->variable( "r_usr" );

	$AccessBoton = "";
//	echo("=1=" . $Access . "--<br>");

    switch($Access) {
    case "FrAc":
		$AccessBoton = "Confirm";
        $particularFields = "";
        $tr = new eZTransaccion( );
		$usr = $session->variable( "r_usr" );
		$qki = $session->variable( "r_qki" );
		$priv = $session->variable( "r_priv" );
        $tr->blog($qki,"ConfDatCli", $RFC );
		include("eztransaccion/user/confdatcliFrAc.php");
        break;
    case "Confirm":
		$AccessBoton = "Process";
        $particularFields = "";
        $tr->blog($qki,"ConfDatCli", $RFC );
		include("eztransaccion/user/confdatcliConfirm.php");
        break;
    case "Process":
		$AccessBoton = "";
        $particularFields = "";
        $tr->blog($qki,"ConfDatCli", $RFC );
		include("eztransaccion/user/confdatcliProcess.php");
        break;
    }
?>
