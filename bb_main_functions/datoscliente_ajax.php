<?php
include("eztransaccion/user/include/controles_javascript.inc");
include_once("eztransaccion/user/include/general_functions.inc");
$Access = $_POST['Access'];
if(empty($Access) && empty($parametros["Access"]) )
{
	if ($dclrequerido == 1)
	{
		$Access = "FrAc";
	}
	else
	{
		$Access = "FrAcMenu";
	}
}
else
{
		$Access = $parametros["Access"];
}

function muestra_DatoCliente($user, $token, $hpharea, $hphone, $cpharea, $cphone, $email)
{

        $timeout = timeout_check();

        if($timeout != "")
                return $timeout;

		// tkn01
		$token	= asb_test($token, $user);
		// tkn01

        $trans = $trans . "<?xml version='1.0'?>";
        $trans .= "<mensaje><trxn value='pmp'/><accion value='UpdDatCli'/><tipomsj value='rqs'/><CustID value='" . $user . "'/>" .
        "<Token value='" . $token . "'/><Hpharea value='" . $hpharea . "'/>" .
        "<Hphone value='" . $hphone . "'/><Cpharea value='" . $cpharea . "'/>" .
        "<Cphone value='" . $cphone . "'/><Email value='" . $email . "'/></mensaje>";

		//Conexion - Nexions HM
		$conn = new TCPIPNexions();

		if(!$conn->connect())
			return "<p>" . $errors[4501]  . "<br><br>" .
					"Favor de intentar nuevamente.<br><br>" .
					"C&oacute;digo: (4501)";

		if(!$conn->send($trans))
			return "<p>" . $errors[4501]  . "<br><br>" .
					"Favor de intentar nuevamente.<br><br>" .
					"C&oacute;digo: (4501)";

		$ans = $conn->receive();

		$conn->close();

        if(trim($ans)=="")
			return "<p>" . $errors[4501]  . "<br><br>" .
					"Favor de intentar nuevamente.<br><br>" .
					"C&oacute;digo: (4501)";

        $xml_struct = parse_xml(trim($ans));

		if (trim($xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"]) != "0000")
		{
			return "<p>" . formatCaracter ($xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["DESC"] )  . "<br><br>" .
					"Favor de intentar nuevamente.<br><br>" .
					"C&oacute;digo: (" . $xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"] .")";
		}
		else
		{
			$hpharea = trim($xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["HPHAREA"]) ;
			$hphone  = trim($xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["HPHONE"]);

			$cpharea = trim($xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["CPHAREA"]);
			$cphone  = trim($xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["CPHONE"]);
			$email   = trim($xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["EMAIL"]);
			$teldom  = trim($xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["TELDOM"]);

			$text_table = "<TABLE ALIGN='LEFT' BORDER='0' CELLSPACING='2' CELLPADDING='2' WIDTH='700px'>
								<TR><TD>Nombre</TD><TD>" . 							trim($xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["NOMBRE"]) . "</TD>
								<TD>&nbsp;</TD><TD>&nbsp;</TD></TR>

								<TR><TD>RFC</TD><TD>" . 							trim($xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["SIN"]) . "</TD>
								<TD>&nbsp;</TD><TD>&nbsp;</TD></TR>

								<TR><TD>Domicilio</TD><TD>" .	 					trim($xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["ADDR1"]) . "</TD>
								<TD>Colonia</TD><TD>" . 							trim($xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["COLONIA"]) . "</TD></TR>

								<TR><TD>C&oacute;digo Postal</TD><TD>" . 			trim($xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["PC"]) . "</TD>
								<TD>&nbsp;</TD><TD>&nbsp;</TD></TR>

								<TR><TD>Estado</TD><TD>" . 							trim($xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["ADDR2"]) . "</TD>
								<TD>Ciudad</TD><TD>" .						 		trim($xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["ADDR3"]) . "</TD></TR>

								<TR><TD>Tel&eacute;fono de Domicilio</TD><TD>" . $hpharea . " " . $hphone . "</TD>

								<TD>Tel&eacute;fono Celular</TD><TD>" . $cpharea . " " . $cphone . "</TD></TR>

								<TR><TD>Correo Electr&oacute;nico</TD><TD colspan='2'>" . $email . "</TD>
								<TD>&nbsp;</TD></TR>";

			if ($teldom == "teldom")
			{
					$text_table = $text_table . "<TR><TD colspan='4'>Se ha actualizado correctamente el tel&eacute;fono de su domicilio.</TD></TR></TABLE>";
			}
			else
			{
				$text_table = $text_table . "<TR><TD colspan='4'><!--br><br><p><font color='red'>Estimado Usuario, para visualizar todas las opciones del men&uacute;, le solicitamos de favor salir y firmarse nuevamente. Adem&aacute;s recibir&aacute; en su correo electr&oacute;nico un c&oacute;digo de activaci&oacute;n para este correo y otro para su tel&eacute;fono celular, mismos que tendr&aacute; 24 horas para validar dentro de la secci&oacute;n Claves de Validaci&oacute;n de su nuevo men&uacute; Informaci&oacute;n del Usuario, en caso contrario se le volver&aacute;n a inhabilitar sus opciones del men&uacute;.</font></p>--></TD></TR></TABLE>
									<script>
										//Salida por redireccion.
										setTimeout(function(){window.location='/ingresaBB/FinSesionDatCli';},5000);
									</script>";
			}
		}
		return $text_table;
}

function consulta_DatoCliente($user, $token)
{

        $timeout = timeout_check();

        if($timeout != "")
                return $timeout;

		// tkn01
		$token	= asb_test($token, $user);
		// tkn01

        $trans = $trans . "<?xml version='1.0'?>";
        $trans .= "<mensaje><trxn value='pmp'/>
					<accion value='ConsDatCli'/>
					<tipomsj value='rqs'/>
					<Token value='" .$token . "'/>
					<CustID value='" . $user . "'/>
					<funcion value = 'consulta_DatoCliente'/>
					</mensaje>";

		//Conexion - Nexions HM
		$conn = new TCPIPNexions();

		if(!$conn->connect())
		  return "<p>Error.<br>4501 - ".$errors[4501]."</p>";

		if(!$conn->send($trans))
		  return "<p>Error.<br>4501 - ".$errors[4501]."</p>";

		$ans = $conn->receive();

		$conn->close();

        if(trim($ans)=="")
              return "<p>Error.<br>4501 - ".$errors[4501]."</p>";

        $xml_struct = parse_xml(trim($ans));

		if (trim($xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"]) != "0000")
		{
			return "<p>Error.<br>" .formatCaracter ($xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["DESC"])  . "<br><br>" .
					"Favor de intentar nuevamente.<br><br>" .
					"C&oacute;digo: (" . $xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"] .")";
		}
		else
		{
			$hpharea = trim($xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["HPHAREA"]);
			$hphone  = trim($xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["HPHONE"]);

			$cpharea = trim($xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["CPHAREA"]);
			$cphone  = trim($xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["CPHONE"]);
			$email   = trim($xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["EMAIL"]);

			$text_table = "	<input type='hidden' id='_hpharea' value='".$hpharea."'>
							<input type='hidden' id='_hphone' value='".$hphone."'>
							<input type='hidden' id='_cpharea' value='".$cpharea."'>
							<input type='hidden' id='_cphone' value='".$cphone."'>
							<input type='hidden' id='_email' value='".$email."'>
							<TABLE ALIGN='LEFT' BORDER='0' CELLSPACING='2' CELLPADDING='2' WIDTH='700px'>
								<TR><TD>Nombre</TD><TD>" . 							trim($xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["NOMBRE"]) . "</TD>
								<TD>&nbsp;</TD><TD>&nbsp;</TD></TR>

								<TR><TD>RFC</TD><TD>" . 							trim($xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["SIN"]) . "</TD>
								<TD>&nbsp;</TD><TD>&nbsp;</TD></TR>

								<TR><TD>Domicilio</TD><TD>" .	 					trim($xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["ADDR1"]) . "</TD>
								<TD>Colonia</TD><TD>" . 							trim($xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["COLONIA"]) . "</TD></TR>

								<TR><TD>C&oacute;digo Postal</TD><TD>" . 			trim($xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["PC"]) . "</TD>
								<TD>&nbsp;</TD><TD>&nbsp;</TD></TR>

								<TR><TD>Estado</TD><TD>" . 							trim($xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["ADDR2"]) . "</TD>
								<TD>Ciudad</TD><TD>" .						 		trim($xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["ADDR3"]) . "</TD></TR>

								<TR><TD>Tel&eacute;fono de Domicilio *</TD><TD>
								<input type='text' name='hpharea' id='hpharea' class='inputbox'  size='3'  maxlength='3'  value='" . $hpharea . "' onKeyPress=\"return esInteger(event,this)\">
								<input type='text' name='hphone'  id='hphone'  class='inputbox'  size='10' maxlength='10' value='" . $hphone . "' onKeyPress=\"return esInteger(event,this)\"></TD>

								<TD>Tel&eacute;fono Celular *</TD><TD>
								<input type='text' name='cpharea' id='cpharea' class='inputbox'  size='3'  maxlength='3'  value='" . $cpharea . "' onKeyPress=\"return esInteger(event,this)\">
								<input type='text' name='cphone'  id='cphone'  class='inputbox'  size='10' maxlength='10' value='" . $cphone . "' onKeyPress=\"return esInteger(event,this)\"></TD></TR>

								<TR><TD>Correo Electr&oacute;nico</TD><TD colspan='2'>
								<input type='text' name='email' id='email' class='inputbox'  size='30' maxlength='45' value='" . $email . "'></TD>
								<TD>&nbsp;</TD></TR></TABLE><br><br>";
		}
		return $text_table;

}

function consulta_ValidaToken($user, $token)
{
        $timeout = timeout_check();

        if($timeout != "")
                return $timeout;

		// tkn01
		$token	= asb_test($token, $user);
		// tkn01

        $trans = $trans . "<?xml version='1.0'?>";
        $trans .= "<mensaje><trxn value='pmp'/>
					<accion value='ConsDatCli'/>
					<tipomsj value='rqs'/>
					<Token value='" .$token . "'/>
					<CustID value='" . $user . "'/>
					<funcion value = 'consulta_ValidaToken'/>
					</mensaje>";

		//Conexion - Nexions HM
		$conn = new TCPIPNexions();

		if(!$conn->connect())
		  return "<p>Error.<br>4501 - ".$errors[4501]."</p>";

		if(!$conn->send($trans))
		  return "<p>Error.<br>4501 - ".$errors[4501]."</p>";

		$ans = $conn->receive();

		$conn->close();

        if(trim($ans)=="")
              return "<p>Error.<br>4501 - ".$errors[4501]."</p>";

        $xml_struct = parse_xml(trim($ans));

		if (trim($xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"]) != "0000")
		{
			return "<script> alert('" .  $xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"] . " - " .  formatCaracter ($xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["DESC"] )  . " ')</script>";
		}
		else
		{
			return "";
		}
}

//ACS 07Jun2010 DatCli Información del cliente Inicio
function consulta_DatoClienteInf ( $user )
{
	$timeout = timeout_check();

	if($timeout != "")
			return $timeout;

	$trans = $trans . "<?xml version='1.0'?>";
        $trans .= "<mensaje><trxn value='pmp'/><accion value='ConsDatCliInf'/><tipomsj value='rqs'/><CustID value='" . $user . "'/></mensaje>";

		//Conexion - Nexions HM
		$conn = new TCPIPNexions();

		if(!$conn->connect())
		  return "<script> alert(' 4501 - " .  $errors[4501]  . " ') </script>";

		if(!$conn->send($trans))
		  return "<script> alert(' 4501 - " .  $errors[4501]  . " ') </script>";

		$ans = $conn->receive();

		$conn->close();

        if(trim($ans)=="")
              return "<script> alert('  4501 - " .  $errors[4501]  . " ') </script>";

        $xml_struct = parse_xml(trim($ans));

		if (trim($xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"]) != "0000")
		{
			return "<p>" . $xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["DESC"]  . "<br><br>" .
					"Favor de intentar nuevamente.<br><br>" .
					"C&oacute;digo: (" . $xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"] .")";
		}
		else
		{
			return "<table>
						<tr>
							<td align = 'center' >".$xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["CUSTNO"]." - <b>".$xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["APEPATERNO"]." ".$xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["APEMATERNO"]." ".$xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["NOMBRE"]." ".$xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["NOMBRE2"]."</b></td>
						</tr>
						<tr>
							<td align = 'center'>R.F.C. ".$xml_struct["vals"][$xml_struct["index"]["CLIENTE"][0]]["attributes"]["RFC"]."</td>
						</tr>
					</table>";

		}
}
//ACS 07Jun2010 DatCli Información del cliente Fin


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

						function procesarAjaxCan(access)
						{
							var parametros='';
							switch(access)
							{
								case 'PiTk':

									break;
							}
							parametros = parametros + ',Access:'+ access;
							//alert(parametros);
							iniProcesarMenu('datoscliente', parametros);
						}

						function procesarAjax(access)
						{
							var parametros='';
							var token='';
							var hpharea = '';
							var hphone = '';
							var cpharea = '';
							var cphone = '';
							var email = '';

							if(access == '')
							{
								access = 'FrAc';
							}
							else
							{
								if (access == 'FrAc' || access == 'PiTk')
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
									if (access == 'Confirm')
									{
										try
										{
											hpharea = document.getElementById('hpharea').value;

											hphone 	= document.getElementById('hphone').value;

											cpharea = document.getElementById('cpharea').value;

											cphone 	= document.getElementById('cphone').value;

											email 	= document.getElementById('email').value;

											if ( hpharea == $(\"#_hpharea\").val() && hphone == $(\"#_hphone\").val() && cpharea == $(\"#_cpharea\").val() && cphone == $(\"#_cphone\").val() && email == $(\"#_email\").val() )
											{
												alert('No se ha modificado ningun dato.');
												return false;
											}

											if (!validaFormatoMail(email))
											{
												alert('LA INFORMACION DEL CORREO CAPTURADA ES INCORRECTA. POR FAVOR INGRESE NUEVAMENTE LOS DATOS, EL FORMATO ES EL SIGUINETE: CARACTERES +\"@\"+ CARACTERES+ \".\" + CARACTERES.');
												return false;
											}

											parametros = parametros +  'hpharea:' + hpharea;
											parametros = parametros + ',hphone:'  + hphone;
											parametros = parametros + ',cpharea:' + cpharea;
											parametros = parametros + ',cphone:'  + cphone;
											parametros = parametros + ',email:'	  + email;

											if (hpharea.length + hphone.length != 10 && hpharea.length + hphone.length != 0 )
											{
												alert('EL NUMERO TELEFONICO DEBE CONTENER 10 DIGITOS. EJ:  477 + 1143123');
												var focus_element;
												focus_element = document.getElementById('hpharea');
												focus_element.focus();
												return false;
											}
											if (cpharea.length + cphone.length != 10 && cpharea.length + cphone.length != 0 )
											{
												alert('EL NUMERO CELULAR DEBE CONTENER 10 DIGITOS. EJ:  477 + 2345651');
												var focus_element;
												focus_element = document.getElementById('cpharea');
												focus_element.focus();
												return false;
											}
											if(email.length==0)
											{
												alert('LA INFORMACION DE CORREO CAPTURADA ES INCORRECTA. POR FAVOR INGRESE NUEVAMENTE LOS DATOS, EL FORMATO ES EL SIGUINETE CARACTERES +\"@\"+ CARACTERES+ \".\" + CARACTERES.');
												var focus_element;
												focus_element = document.getElementById('email');
												focus_element.focus();
												return false;
											}
										}
										catch(err)
										{
											access = '';
										}
									}
									else
									{
										if (access == 'Process')
										{
											hpharea = document.getElementById('hpharea').value;
											hphone 	= document.getElementById('hphone').value;
											cpharea = document.getElementById('cpharea').value;
											cphone 	= document.getElementById('cphone').value;
											email 	= document.getElementById('email').value;
											token   = document.getElementById('token').value;
											parametros = parametros + 'token:'    + token;
											parametros = parametros + ',hpharea:' + hpharea;
											parametros = parametros + ',hphone:'  + hphone;
											parametros = parametros + ',cpharea:' + cpharea;
											parametros = parametros + ',cphone:'  + cphone;
											parametros = parametros + ',email:'	  + email;
											if(token.length != 10)
											{
													alert('Por favor ingrese una clave correcta');
													var focus_element;
													focus_element = document.getElementById('token_value');
													focus_element.focus();

													return false;
											}
											if (hpharea.length + hphone.length != 10 && hpharea.length + hphone.length != 0 )
											{
												alert('Por favor ingrese telefono de domicilio correcto');
												var focus_element;
												focus_element = document.getElementById('hpharea');
												focus_element.focus();
												return false;
											}
											if (cpharea.length + cphone.length != 10 && cpharea.length + cphone.length != 0 )
											{
												alert('Por favor ingrese telefono celular correcto');
												var focus_element;
												focus_element = document.getElementById('cpharea');
												focus_element.focus();
												return false;
											}
										}
									}
								}
							}

							parametros = parametros + ',Access:'+ access;
//							alert(parametros);
							iniProcesarMenu('datoscliente', parametros);

						}
					</script>";

include("eztransaccion/user/include/tcpipnexions.php");
include("eztransaccion/user/include/xmlparser.inc");

//REGA se incluye lectura de variables de INI para la conexión a la bd de campañas
include_once( "classes/INIFile.php" );
include_once( "classes/ezlog.php" );

$CampSrv=trim($ini->read_var("site", "CampSrv"));
$CampBD=trim($ini->read_var("site", "CampBD"));
$CampUsr=trim($ini->read_var("site", "CampUsr"));
$CampPwd=trim($ini->read_var("site", "CampPwd"));
$lnkCamp=trim($ini->read_var("site", "lnkCamp2"));

$cust = $session->variable( 'r_cno' );
//$cust = '9982141';
//REGA se incluye lectura de variables de INI para la conexión a la bd de campañas

    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "datoscliente.php" );
    $t->setAllStrings();
    $t->set_file( array( "datoscliente_tpl" => "datoscliente.tpl" ) );

    $transaccion_buffer = "";
	$usr = $session->variable( "r_usr" );

	$AccessBoton = "";
//	echo("=1=" . $Access . "--<br>");

    switch($Access) {
    case "FrAc":
		$AccessBoton = "PiTk";
        $particularFields = "";
        $tr = new eZTransaccion( );
		$usr = $session->variable( "r_usr" );
		$qki = $session->variable( "r_qki" );
		$priv = $session->variable( "r_priv" );
        $tr->blog($qki,"DatosCliente", $RFC );
		include("eztransaccion/user/datosclienteFrAc.php");
        break;
    case "FrAcMenu":
		$AccessBoton = "PiTk";
        $particularFields = "";
        $tr->blog($qki,"DatosCliente", $RFC );
		include("eztransaccion/user/datosclienteFrAcMenu.php");
        break;
    case "PiTk":
		$AccessBoton = "Confirm";
        $particularFields = "";
        $tr->blog($qki,"DatosCliente", $RFC );
		include("eztransaccion/user/datosclientePiTk.php");
		//REGA se hace la consulta de los boletos electrónicos para las campañas
		$conexion = mysqli_connect($CampSrv, $CampUsr, $CampPwd);
		/*echo "SERVER: ".$CampSrv."<br/>";
		echo "DATABASE: ".$CampBD."<br/>";
		echo "USER: ".$CampUsr."<br/>";
		echo "PASS: ".$CampPwd."<br/>";
		echo "CUST: ".$cust."<br/>";*/
		if ( $conexion)
		{
			if ( mysqli_select_db($conexion,$CampBD) )
			{
			
				$consulta  = "SELECT sum(numBoletos) as Boletos, nombreSorteo
							  FROM
									campanias
							  WHERE
									numCliente = ".$cust."
								AND
									campanaId = 4
								AND
									statusCamp = 'activa'
									
							  GROUP BY nombreSorteo;";
									
				//if(mysqli_query($conexion, $consulta)){
				$resultado = mysqli_query($conexion, $consulta);
				
				if(mysqli_num_rows($resultado))
				{
					$result= '<div align="center"><BR><font color="#5A419C"> <font size="3">¡Felicidades!,</font> tiene acumulados los siguientes boletos electrónicos:</font> <BR>
							<BR>
							<table width="40%" border="0" cellspacing="3">
								<tbody>
									<tr>
										<th bgcolor="#5A419C" align="CENTER">
										<font color="WHITE">Sorteo</font>
										</th>
										<th bgcolor="#5A419C" align="CENTER">
										<font color="WHITE">#Boletos<BR>Electrónicos</font>
										</th>
									</tr>';
					
					while ($row = mysqli_fetch_array($resultado)) {
					$sorteo="";
					   if($row{'nombreSorteo'}=="Cancun"){
						$sorteo = str_replace("u","ú",$row{'nombreSorteo'});
					   }
					   else{
						$sorteo = $row{'nombreSorteo'};
					   }
					   $result.= "<tr>
							<td bgcolor='#DDDDDD'>".$sorteo."</td>
							<td bgcolor='#DDDDDD'>".$row{'Boletos'}."</td>
							</tr>";
					}
					$result.="</tbody>
							</table>
							<BR>
							<font color='#5A419C'>
								Para consultar mayor información y bases del sorteo <b><em>\"Actualiza tus Datos\"</em></b> haga 
								<a href='".$lnkCamp."' target='_blank' onclick='window.open(this.href, this.target, 'scrollbars=no,directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no'); return false;'>
									clic aquí
								</a>
							</font>
							</div>
							<BR>";
				}
			}
		}
		echo $result;
		//REGA se hace la consulta de los boletos electrónicos para las campañas
        break;
    case "Confirm":
		$AccessBoton = "Process";
        $particularFields = "";
        $tr->blog($qki,"DatosCliente", $RFC );
		include("eztransaccion/user/datosclienteConfirm.php");
        break;
    case "Process":
		$AccessBoton = "";
        $particularFields = "";
        $tr->blog($qki,"DatosCliente", $RFC );
		include("eztransaccion/user/datosclienteProcess.php");
        break;
    }
?>
