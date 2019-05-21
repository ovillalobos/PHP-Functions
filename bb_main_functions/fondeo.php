<?php

include("eztransaccion/user/include/PHPLiveX-2.2.php");
include_once("eztransaccion/classes/encrypt.php");
include("httplib.php");	
include_once("classes/ezlog.php"); 
include("include/estilo.inc");

function ControlAcceso($menu,$usr)
{
    global $errors;

    //Mensaje - Nexions HM
    $trans = "<?xml version='1.0'?>";
    $trans .= "<mensaje><trxn value='fon'/><accion value='ValidaOpMenu'/><tipomsj value='rqs'/><CustID value='$usr'/>";
    $trans .= "<Opmenu value='" . $menu . "'/>";
	$trans .= "<IP value='" . $_SERVER['REMOTE_ADDR'] . "'/>";
    $trans .= "</mensaje>";
    //Mensaje

    //Conexion - Nexions HM
    $conn = new TCPIPNexions();

    if(!$conn->connect())
          echo "<script> alert('  4501 - " .  $errors[4501]  . " ') </script>";

    if(!$conn->send($trans))
          echo "<script> alert('  4501 - " .  $errors[4501]  . " ') </script>";

    $ans = $conn->receive();

    if(trim($ans) == "")
          echo "<script> alert('  4501 - " .  $errors[4501]  . " ') </script>";

    $xml_struct = parse_xml(trim($ans));

    if (trim($xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"]) != "0000")
	{
		return $xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"] . " - " . $xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["DESC"];
	}
    $conn->close();
    //Conexion

    return "0000";
}

function cambiaCuenta($str)
{
	$Cuenta = "";
	if(trim($str) != "")
	{
		switch(trim($str))
		{
			case "ahorro":
				$Cuenta = "NominaBasica";
				break;
			case "ahnom":
				$Cuenta = "Nomina";
				break;
			case "cheqsi":
				$Cuenta = "Clasica";
				break;
			case "maestra":
				$Cuenta = "Maestra";
				break;
			case "cheqsc":
				$Cuenta = "Cheqsc";
				break;
			case "basica":
				$Cuenta = "Basica";
				break;
			case "brillan":
				$Cuenta = "Brillante";
				break;
			case "precisa":
				$Cuenta = "Precisa";
				break;	
			case "puntos":
				$Cuenta = "Puntos";
				break;
			default:
				$Cuenta = trim($str);
				break;
		}
	}
	return $Cuenta;
}

function consultaCuentasEnlace($usuario)
{
	require_once("nusoap-0.7.3/lib/nusoap.php");
	
	$Tipo = "enlace";
	$Trn = "fef";
	$Cte = "0";
	$servicio = "cuentas";
	$ini =& $GLOBALS["GlobalSiteIni"];
	$wsdl = $ini->read_var( "site", "wsdlInversion" );
	
	$consultaXML = "<consulta><tipo>$Tipo</tipo><transaccion>$Trn</transaccion><origen></origen><cliente>$Cte</cliente><userid>$usuario</userid><extra>0-0</extra></consulta>";
	
	$param = array('xmlRequest' => $consultaXML);
	
	$client = new nusoap_client($wsdl,'wsdl');

	$response = $client->call($servicio, $param);

	if ($client->fault)	{
		echo "No se pudo completar la operacion";
		return "Error al conectarse con el Web Service.";
	} else {
		$error = $client->getError();
		if ($error){
			echo "==========> Error al llamar al webservice:" . $error . " wsdl[$wsdl]<BR><BR>";
			return "Error " . $error;
		}
	}
	
	$index = array();
	$xml_struct = parse_xml(trim($response["cuentasReturn"]),$index);
	
	$cuentas = array();
	
	for ($i = 0; $i < count($xml_struct["index"]["CUENTA"]); $i += 2)
	{
		$atributos = array();
		for ($j = $xml_struct["index"]["CUENTA"][$i] + 1; $j < $xml_struct["index"]["CUENTA"][$i + 1]; $j++)
		{
			$atributos[] = array($xml_struct["vals"][$j]["tag"] => $xml_struct["vals"][$j]["value"]);
		}		
		$cuentas[] = $atributos;
	}

	return $cuentas;
}

function llena_select($cuentasEnlace)
{
	$optionsResult = "";
	if(count($cuentasEnlace) > 0)
	{
		foreach($cuentasEnlace as $key => $val)
		{
			$xmlNodeIbpwRowno 	= $cuentasEnlace[$key][0]["IBPWROWNO"];
			$xmlNodeCliente 	= $cuentasEnlace[$key][1]["CLIENTE"];
			$xmlNodeTipoCuenta 	= $cuentasEnlace[$key][2]["TIPO"];
			$xmlNodeSubCuenta 	= $cuentasEnlace[$key][3]["SUB"];
			$xmlNodeCuentaEF 	= $cuentasEnlace[$key][4]["CTA_EF"];
			$xmlNodeEmpresa 	= $cuentasEnlace[$key][5]["EMPRESA"];
			$xmlNodeNombre 		= $cuentasEnlace[$key][6]["NOMBRE"];
			$xmlNodePerfil 		= $cuentasEnlace[$key][7]["PERFIL"];
			
			$xmlNodePerfil = trim($xmlNodePerfil) == "Agrupado" ? " - gpo - " : " - ";
			
			$optionsResult = $optionsResult .
			 "<OPTION VALUE='" .
				$xmlNodeIbpwRowno .  "|".
				$xmlNodeCliente .    "|".
				$xmlNodeTipoCuenta . "|".
				$xmlNodeSubCuenta .  "|".
				$xmlNodeCuentaEF .   "|".
				$xmlNodeEmpresa .    "'>".
				$xmlNodeCliente .    " - ".
				CambiaCuenta($xmlNodeTipoCuenta) . " - ".
				$xmlNodeSubCuenta .
				$xmlNodePerfil .
				$xmlNodeNombre .
			 "</OPTION>";
		}
	}
	else
	{
		$optionsResult = $optionsResult .
			 "<OPTION VALUE=\"error\">No se encontraron cuentas</OPTION>";
	}
	
	return $optionsResult;
}

function llena_info($cuentas)
{
	$text = "";
	$text = "
	<input type=\"HIDDEN\" id=\"ibpwrowno\" name=\"ibpwrowno\" value=\"error\">
	<input type=\"HIDDEN\" id=\"ctaorigen\" name=\"ctaorigen\" value=\"error\">
	<input type=\"HIDDEN\" id=\"ctaorigen2show\" name=\"ctaorigen2show\" value=\"error\">
	<input type=\"HIDDEN\" id=\"ctadestino\" name=\"ctadestino\" value=\"error\">
	<input type=\"HIDDEN\" id=\"empresaef\" name=\"empresaef\" value=\"error\">
	
	<div id=\"principal\" class=\"panelDiv\"> 
		<table align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"85%\">
			<tr>
				<ol>
					<li>Seleccione la cuenta origen.</li>
					<li>Capture el monto para fondear las operaciones de SPEI.</li>
					<li>Presione el bot&oacute;n <em>Aceptar</em> para continuar.</li>
				</ol>
				<hr size=\"1\" noshade></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td align=\"center\" valign=\"middle\">
					<table align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
						<tr>
							<td align=\"left\"> <b><font size=\"2\">Cuenta Origen:</font></b></td>
						</tr>
						<tr>
							<td align=\"left\">
								<select class='inputbox_nom' name=\"CtasOrigen\" id=\"CtasOrigen\" onchange=\"optionSelected();\">
									<option value='error'></option>
									$cuentas
								</select>
							</td>
						</tr>
						<tr>
							<td align=\"left\">&nbsp</font></b></td>
						</tr>
						<tr>
							<td align=\"left\"> <b><font size=\"2\">Monto:</font></b></td>
						</tr>
						<tr>
							<td align=\"left\"><input type=\"text\" class='inputbox_nom' name=\"Amount\" id=\"Amount\" size=\"15\" maxlength=\"15\" onKeyUp=\"javascript:{currencya( this,event );}\" onblur=\"javascript:{ this.value=FormatAmount( this.value ); }\"></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td><center><input type=\"Button\" name=\"Button\" id=\"Aceptar\" value=\"Aceptar\" class='button' onmouseover=\"this.className='buttonON'\" onmouseout=\"this.className='button'\" onclick=\"javascript:mostrarModal();\"></center></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
	";
	$text .= "
	
	<div id=\"modalFinalizar\" style='display:none;'>
		<div style=\"width:750; height:100px;\">
			<div class='ui-jqgrid-titlebar ui-widget-header ui-corner-top ui-helper-clearfix' style='width:99.7%; height:20px;'><span class='ui-jqgrid-title' style='font-size:12px;'>&nbsp;Detalle Transacci&oacute;n</span></div>
			<label style='margin:auto;position:absolute;font-weight:bold; color:black; font-family: 12 px Verdana,Arial,Helvetica,Swiss,Futura,sans-serif; width:100%; text-align:center;'>
				Usted est&aacute; autorizando la siguiente operaci&oacute;n de fondeo
			</label>
			<br/>
			<br/>
			<table style='font-family: 12 px Verdana,Arial,Helvetica,Swiss,Futura,sans-serif; width:70%; border-collapse:collapse; padding:5px; border: 1px solid #000;' align='center' cellspacing='1'>
				<tr>
					<td style='text-align:left; border: 1px solid #000;'><b>Cuenta Origen:</b> <label id='cuentaOrigen' style=\"font-weight:normal;\"></label></td>
				</tr>
				<tr>
					<td style='text-align:left; border: 1px solid #000;'><b>Cuenta Destino en EF:</b> <label id='cuentaEF' style=\"font-weight:normal;\"></label></td>
				</tr>
				<tr>
					<td style='text-align:left; border: 1px solid #000;'><b>Empresa Destino en EF:</b> <label id='empresa' style=\"font-weight:normal;\"></label></td>
				</tr>
				<tr>
					<td style='text-align:left; border: 1px solid #000;'><b>Monto:</b> <label id='monto' style=\"font-weight:normal;\"></label></td>
				</tr>
			</table>
			<br/>
			<table style='font-weight:bold;font-family: 12px Verdana,Arial,Helvetica,Swiss,Futura,sans-serif; width:80%; padding:5px;' align='center'>
				<tr>
					<td align=\"center\">
						<br/>
						<table border=\"0\" width=\"100%\">
							<tr><td colspan='2' align='center'><div id='auxErrTK' name='auxErrTK' style='color:red;'></div></td></tr>
							<tr><td align=\"center\">Autorice la Transacci&oacute;n Capturando su Clave ASB ( Acceso Seguro Bajio )</td></tr>
							<tr>
								<td align=\"center\" style=\"font-weight:normal;\">
									Clave ASB (Acceso Seguro Bajio): 
									<input onKeyPress=\"javascript:if (isEnterTk(event) == true){ if(isNumberTk($('#codeTkASB').val())) { continuaFondeo(); } else { $('#codeTkASB').val(''); $('#auxErrTK').html('El n&uacute;mero de Clave ASB que ha ingresado es inv&aacute;lido, int&eacute;ntelo de nuevo.'); } } else { return isNum(event, codeTkASB);}\" name='codeTkASB' id='codeTkASB' size='12' maxlength='10' type='password' align='left'>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<br/>
			<table style='font-weight:bold;font-family: 12px Verdana,Arial,Helvetica,Swiss,Futura,sans-serif; width:80%; padding:5px;' align='center'>				
				<tr align=\"center\">
					<td align=\"center\">								
						<input class='button' onmouseover=\"this.className='buttonON'\" onmouseout=\"this.className='button'\" type=\"button\"  id=\"btnModalContinua\" name=\"btnModalContinua\" value =\"Aceptar\" text=\"Aceptar\" onclick=\"javascript:continuaFondeo();\" />&nbsp;
						<input class='button' onmouseover=\"this.className='buttonON'\" onmouseout=\"this.className='button'\" type=\"button\"  id=\"btnModalCierra\"   name=\"btnModalCierra\"   value =\"Cancelar\" text=\"Cancelar\" onclick=\"javascript:cancelaFondeo();\"/>
					</td>
				</tr>
			</table>
		</div>
	</div>
	</FORM>
	";
	$text = "
	<FORM METHOD=post>
	<script language=\"javascript\">
		
		function isNumberTk(code)
		{
			var valor = code;
			var patternx = \"^\\\\d{10}$\";

			if (valor.match(patternx) && valor != \"\" )
			{ return true; }
			else
			{ return false;  }
			return false;
		}
		
		function isEnterTk(e)
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
		
		function isNum(e, textbox)
		{
			var charCode;
			if (navigator.appName == \"Netscape\")
			{
				charCode = e.which;
			}
			else
			{
				charCode = e.keyCode;
			}
			
			if (charCode > 31 && (charCode < 48 || charCode > 57))
			{
				return false;
			}
			return true;
		}
		
		function optionSelected()
		{
			var selectedText = $(\"#CtasOrigen option:selected\").text();
			var selectedValue = $(\"#CtasOrigen\").val();
			
			if(selectedValue != 'error')
			{
				var valores = selectedValue.split(\"|\");
				$(\"#ctaorigen2show\").val(selectedText);
				$(\"#ibpwrowno\").val(valores[0]);
				$(\"#ctaorigen\").val(valores[1] + ' - ' + valores[2] + ' - ' + valores[3]);
				$(\"#ctadestino\").val(valores[4]);
				$(\"#empresaef\").val(valores[5]);
			}
			else
			{
				$(\"#ctaorigen2show\").val('error');
				$(\"#ibpwrowno\").val('error');
				$(\"#ctaorigen\").val('error');
				$(\"#ctadestino\").val('error');
				$(\"#empresaef\").val('error');
			}
		}
		
		function mostrarModal(){
			if($(\"#ctaorigen\").val() == 'error' || $(\"#ctadestino\").val() == 'error' || $(\"#empresaef\").val() == 'error')
			{
				alert('Debe seleccionar una cuenta.');
			}
			else
			{
				if(validaMonto())
				{
					if(hasDecimals())
					{
						alert('Por ahora no se permiten montos con centavos, favor de ingresar un monto exacto.');
					}
					else
					{
						$(\"#monto\").html(\"$ \" + $(\"#Amount\").val());
						$(\"#cuentaOrigen\").html($(\"#ctaorigen2show\").val());
						$(\"#cuentaEF\").html($(\"#ctadestino\").val());
						$(\"#empresa\").html($(\"#empresaef\").val());
						$(\"#modalFinalizar\").modal({close:false,
							  containerCss: {
									height: 270,
									width: 760
									},
							onClose: cancelaFondeo
						});
					}
				}
				else
				{
					alert('Debe ingresar un monto válido');
				}
			}
		}
		
		function cancelaFondeo(){
			$.modal.close();
		}
		
		function validaMonto()
		{
			var montoFon = $('#Amount').val();
			if(montoFon <= 0)
			{
				return false;
			}
			return true;
		}
		
		function cleanAmount()
		{
			var montoFon = $('#Amount').val();
			while (montoFon.indexOf(\",\") >= 0){
				montoFon = montoFon.replace(\",\",\"\");
			}
			return montoFon;
		}
		
		function hasDecimals()
		{
			var montoFon = cleanAmount();
			var tmpMonto = montoFon % 1;
			if(tmpMonto != 0)
			{
				return true;
			}
			return false;
		}
		
		function continuaFondeo()
		{
			if(isNumberTk($('#codeTkASB').val()))
			{
				var montoFon = cleanAmount();
				var tkn = $('#codeTkASB').val();
				var cuentaorigen = $('#ctaorigen').val();
				var cuentadestino = $('#ctadestino').val();
				var rowno = $('#ibpwrowno').val();

				$.ajax(
				{
					type: 'POST',
					beforeSend: function(obj)
						{
							$.modal.close();
							$(\"#principal\").html('<br><br><br><br><br><br><br><br><br><div align=center style=\"width:85%;\"><img src=\"https://images.bb.com.mx/sitedesign/bajionet/images/loading.gif\"	style=\"align: center;cursor: pointer; border: 0px solid red;\" title=\"cargando\" alt=\"Cargando...\" align=\"center\" /><br>Cargando...</div>');
						},
					url: '/procesarAjaxMenu.php',
					data: 'nomFunc=fondeo&Access=AltaFondeo&Token=' + tkn + '&MontoFondeo=' + montoFon + '&CtaOrigen=' + cuentaorigen + '&CtaDestino=' + cuentadestino + '&RownoIbpw=' + rowno + '',
					dataTypedataType: 'html',
					async: true,
					success: function(datos)
							{
								$(\"#principal\").html(\"\");
								$(\"#principal\").html($.trim(datos));
							}
							
				});
				$.modal.close();
			}
			else
			{
				$('#codeTkASB').val(''); $('#auxErrTK').html('El n&uacute;mero de Clave ASB que ha ingresado es inv&aacute;lido, int&eacute;ntelo de nuevo.');
			}	
		}
		</script>".$text;
	return $text;
}

function consulta_datos_EF($user)
{
	global $errors;

    $timeout = timeout_check();

    if($timeout != "")
		return $timeout;

	$text = "";
	
    $trans  = "<?xml version='1.0'?>";
	$trans .= "<mensaje><trxn value='fon'/><accion value='ConsDatEF'/><tipomsj value='rqs'/><CustID value='$user'/>";
	$trans .= "<IP value='" . $_SERVER['REMOTE_ADDR'] . "'/>";
	$trans .= "</mensaje>";
    $conn = new TCPIPNexions();

	if(!$conn->connect())
	{
		return "<script> alert('  4501 - " .  $errors[4501]  . " ') </script>";
	}
	if(!$conn->send($trans))
	{
		return "<script> alert('  4501 - " .  $errors[4501]  . " ') </script>";
	}
	$ans = $conn->receive();
	if(trim($ans) == "")
	{
		return "<script> alert('  4501 - " .  $errors[4501]  . " ') </script>";
	}
	$xml_struct = parse_xml(trim($ans));

	if(($value = check_user($user, $xml_struct["vals"][$xml_struct["index"]["CUSTID"][0]]["attributes"]["VALUE"])) != "1")
	{
		eZLog::writeNotice( "...............XML REQUEST  CONSULTA PAGOS.........requestJboss[".print_r($trans,true)."].."); 
		eZLog::writeNotice( "...............XML RESPONSE CONSULTA PAGOS.........responseJboss[".print_r($ans,true)."].."); 							
		eZLog::writeNotice( "::::::::::::::ERROR AL CHECKAR DATOS DE USUARIO $user xmlCustID: ".$xml_struct["vals"][$xml_struct["index"]["CUSTID"][0]]["attributes"]["VALUE"]." ::::::::::::::::::::");
	}

    if(trim($xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"]) != "0000")
	{
		return $xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"] . " - " .  $xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["DESC"];
	}
	$conn->close();
    //Conexion

	//$val = $xml_struct["index"]["DATOSEF"][0];
	//$ctaorigen = trim($xml_struct["vals"][$val]["attributes"]["CTAORIGEN"]);
	//$ctadestino = trim($xml_struct["vals"][$val]["attributes"]["CTADESTINO"]);
	
	$cuentas = consultaCuentasEnlace($user);
	$opciones = llena_select($cuentas);
	$text = llena_info($opciones);

	return $text;
}

if (!headers_sent())
{
	header("ETag: PUB" . time());
	header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
	header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
	header( 'Cache-Control: no-store, no-cache, must-revalidate' );
	header( 'Cache-Control: post-check=0, pre-check=0', false );
	header( 'Pragma: no-cache' );
}

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezhttptool.php" );
include_once( "eztransaccion/classes/eztransaccion.php" );
include_once( "ezsession/classes/ezsession.php" );
include("eztransaccion/user/include/tcpipnexions.php");
include("eztransaccion/user/include/xmlparser.inc");
include("eztransaccion/user/include/general_functions.inc");

$session =& eZSession::globalSession();

if( !$session->fetch() )
    $session->store();

$ini =& $GLOBALS["GlobalSiteIni"];

$Language = $ini->read_var( "eZTransaccionMain", "Language" );
$ServerNumber = $ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );

$backend = "gw" . $ServerNumber. $DomainPostfix;

include_once( "ezuserbb/classes/ezuser.php" );

$user =& eZUserBB::currentUser();

if ($user)
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "fondeo.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "fondeo_tpl" => "fondeo.tpl"
        ) );

    $session =& eZSession::globalSession();

    if ( !$session->fetch() )
    {
        $session->store();
    }

    $tr = new eZTransaccion( );
    $usr = $session->variable( "r_usr" );
    $qki = $session->variable( "r_qki" );
    $priv = $session->variable( "r_priv" );
    $transaccion_buffer = "";
    
    $t->set_var( "transaccion_buffer", $transaccion_buffer );

    //Escritura del template
    $t->pparse( "output", "fondeo_tpl" );

    $usr = strtolower($usr);
    $result = ControlAcceso("fondeo",$usr);
	
	if($result == "0000")
	{
		echo consulta_datos_EF($usr);
	}
	else
	{
        echo $result;
	}
}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/fondeo/" );

    $t->pparse( "output", "user_login_tpl" );
}
?>

