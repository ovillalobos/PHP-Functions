<?php
//
//
// Created on: <1-Nov-2001 17:37:53 smb>
//
// This source file is part of IVA.
//
// Copyright (C) 1997-2004 Internet de Alta Calidad, S.A. de C.V.  Todos los derechos reservados. All rights reserved.
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, US
//
// DebugBreak();

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezhttptool.php" );
include_once( "eztransaccion/classes/eztransaccion.php" );
include_once( "ezsession/classes/ezsession.php" );
include_once("eztransaccion/user/include/edosCtas.inc");
//MAT -21Nov2012- Llave ASB para Circular X I
include_once("eztransaccion/user/include/controles_javascript.inc");
include_once("eztransaccion/user/eztransaccion_CallWebService.inc");
include_once("eztransaccion/user/include/xmlparser.inc");
//MAT -21Nov2012- Llave ASB para Circular X F

$session =& eZSession::globalSession();

if( !$session->fetch() )
    $session->store();


$ini =& $GLOBALS["GlobalSiteIni"];

$Language = $ini->read_var( "eZTransaccionMain", "Language" );
// $backend = $ini->read_var( "eZTransaccionMain", "Backend" );
$ServerNumber = $ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );

$backend = "gw" . $ServerNumber. $DomainPostfix;
$snabackend = $ini->read_var( "eZTransaccionMain", "SNABackend" );
$snabackendport = $ini->read_var( "eZTransaccionMain", "SNABackendPort" );
$snafs = $ini->read_var( "eZTransaccionMain", "SNAFS" );
$snaappid = $ini->read_var( "eZTransaccionMain", "SNAAppID" );


include_once( "ezuserbb/classes/ezuser.php" );

$user =& eZUserBB::currentUser();

$wsdl_Tdc = $wsdl_Tdc_base. "http://192.1.15.101:8090/bajioWS/services/ConsultaTarjeDebitoServiceImpl?wsdl";
$paramsRequest['trxn'] = "";
$paramsAdd   = array();
$paramsNodes = array();
$paramsNodes['NumCliente'] = $session->variable( "r_cno" );
$paramsNodes['Option'] = "credito";
$servWeb="getConsultaTarjeDebito";
//Call Web service
$result = callWebServiceTDC($servWeb, $paramsRequest, $paramsAdd, $paramsNodes, $wsdl_Tdc);
$res = $result['return']['response'];

$index = array();
$xml_struct = parse_xml(trim($res), $index);
if ( $xml_struct['vals'][1]["attributes"]["VALUE"] == "OK" )
{
	$cantRows = 0;
	$obj = array();
	foreach ($xml_struct["index"]["TARJETA"] as $key=>$val)
	{
		$obj[] = array('tarjeta'=> $xml_struct["vals"][$xml_struct["index"]["TARJETA"][$cantRows]]["attributes"]["NUMTC"],
		'nombreTC' => $xml_struct["vals"][$xml_struct["index"]["TARJETA"][$cantRows]]["attributes"]["NOMBRETC"],
		'tipo' => $xml_struct["vals"][$xml_struct["index"]["TARJETA"][$cantRows]]["attributes"]["TIPOTC"],
		'tipoCta' => $xml_struct["vals"][$xml_struct["index"]["TARJETA"][$cantRows]]["attributes"]["TIPOCTA"]);

		$tarjetaTC = $xml_struct["vals"][$xml_struct["index"]["TARJETA"][$cantRows]]["attributes"]["NUMTC"];
		$nombreTC = $xml_struct["vals"][$xml_struct["index"]["TARJETA"][$cantRows]]["attributes"]["NOMBRETC"];
		$tipo = $xml_struct["vals"][$xml_struct["index"]["TARJETA"][$cantRows]]["attributes"]["TIPOTC"];
		$tipoCta = $xml_struct["vals"][$xml_struct["index"]["TARJETA"][$cantRows]]["attributes"]["TIPOCTA"];
		$cantRows++;

	}
	//var_export($xml_struct);
    //var_export($obj);
    //var_export($obj);
	$ob=json_encode($obj);
	$obje=json_decode($ob);


		$_SESSION['numtc'] = $tarjetaTC;
		$_SESSION['nombreTC'] = $nombreTC;
		$_SESSION['tipo'] = $tipo;
		$_SESSION['tipoCta'] = $tipoCta;
}

//echo "TARJETA WS= ".$tarjetaTC;

$usr = $session->variable( "r_usr" );
$qki = $session->variable( "r_qki" );
$priv = $session->variable( "r_priv" );
//$tarjeta = $session->variable ("r_tdc");
$tarjeta = $tarjetaTC;
//echo " tarjeta1 con WS= ". $tarjeta;
// DebugBreak();

($parametros['Reporte']!=""?$Reporte=$parametros['Reporte']:0);
($parametros['Accion']!=""?$Accion=$parametros['Accion']:0);
($parametros['Access']!=""?$Access=$parametros['Access']:0);
($parametros['Cadpriv']!=""?$Cadpriv=$parametros['Cadpriv']:0);
($parametros['CustID']!=""?$usr=$parametros['CustID']:0);
($parametros['Trxn']!=""?$Trxn=$parametros['Trxn']:0);
if($Access == "FrAc"){
($parametros['FrAccount']!=""? $FrAccount =$tarjetaTC:0);
}
if($Access == "Process"){
($parametros['FrAccount']!=""? $FrAccount =$parametros['FrAccount']:0);
}
//($parametros['FrAccount']!=""? $FrAccount =$parametros['FrAccount']:0);
($parametros['Formato']!=""? $Formato =$parametros['Formato']:0);

($parametros['Month']!=""?$Month=$parametros['Month']:0);
($parametros['Year']!=""?$Year=$parametros['Year']:0);
//MAT -21Nov2012- Llave ASB para Circular X I
($parametros['org_form']!=""?$org_form=$parametros['org_form']:0);
//MAT -21Nov2012- Llave ASB para Circular X F

$transaccion_buffer = "";

if( !isset( $Reporte ) ) {
	$Reporte = "";
}
if( !isset( $Access ) || empty( $Access ) ) {
	$Access = "FrAc";
}
if( !isset( $Accion ) ) {
	$Accion = "";
}
if( !isset( $FrAccount ) ) {
	$FrAccount = "";
}
if( !isset( $Month ) ) {
	$Month = "";
}
if( !isset( $Year ) ) {
	$Year = "";
}
//MAT -21Nov2012- Llave ASB para Circular X I
if( !isset( $org_form ) ) {
	$org_form = "";
}
//MAT -21Nov2012- Llave ASB para Circular X F
if ( $user )
{
	if($Access != "FrAc"){
		$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
							"eztransaccion/user/intl/", $Language, "edosctaActualTC.php" );

		$t->setAllStrings();

		$t->set_file( array("edosctaActualTC_tpl" => "edosctaActualTC.tpl") );
	}else{
		$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "saldosTC.php" );

		$t->setAllStrings();

		$t->set_file( array("saldosTC_tpl" => "saldosTC.tpl") );
	}
    $session =& eZSession::globalSession();

    if ( !$session->fetch() )
    {
        $session->store();
    }
    $tr = new eZTransaccion( );
    $usr = $session->variable( "r_usr" );
    $qki = $session->variable( "r_qki" );
    $priv = $session->variable( "r_priv" );
	//$tarjeta = $session->variable ("r_tdc");
	//$tarjeta = $tarjetaTC;
	if($Access =="Process")
	{
		$tarjeta = $FrAccount;

	}else{
			$tarjeta = $tarjetaTC;
	}
    $transaccion_buffer = "";
    $ret_code = 0;
    $sna_ret_code = 0;

    $transaccion_buffer = "";
    $sna_transaccion_buffer = "";

	$rfc = $RFC;
    //$pan = $PAN;
    $pan = $tarjetaTC;
    $tipo = "";
    $nombre = "";
    $html_de_salida = "";
    $hubo_error = 0;

	// - Definir los datos a enviar

    //$particularFields = "&PAN=" . urlencode($tarjeta)."&Empresa=01";


      //PSM INI-DESPLEGAR POPUP CON INFO DE BANXICO 20110902  //PSM SE COMENTA EL 16042013 YA QUE BANXICO NO TIENE DISPONIBLE EL RECURSO

          //if ( $tarjeta >= 4201994000000000 && $tarjeta<= 4201995999999999  )
		  	//{
		  	//	$pagina="http://www.banxico.gob.mx/waTarjetas/TarjetasCreditoOro.pdf";
		  	//}
		  	//else
		  	//{
		  	//	if ($tarjeta >=4201998500000000 && $tarjeta <= 4201998699999999 )
		  	//	{
		  	//		$pagina="http://www.banxico.gob.mx/waTarjetas/TarjetasCreditoOro.pdf";
		  	//	}
		  	//	else
		  	//	if ($tarjeta >=4201996000000000 && $tarjeta <= 4201997999999999 )
			//		{
			//			$pagina="http://www.banxico.gob.mx/waTarjetas/TarjetasCreditoPlatino.pdf";
			//		}
		  	//	else
		  	//		if ($tarjeta >=4201998700000000 && $tarjeta <= 4201999000000000 )
			//			{
			//				 $pagina="http://www.banxico.gob.mx/waTarjetas/TarjetasCreditoPlatino.pdf";
			//			}
		  	//		else
		  	//			{
		  	//				$pagina="http://www.banxico.gob.mx/waTarjetas/TarjetasCreditoClasicas.pdf";
		  	//			}
			//}
			// $mes=date("m");
			//if ( $mes==10 || $mes==4 )
			//{
         // echo "
		 //       <script language=\"JavaScript\">
        //		var w=500, h=650, bw, bh, topPos, leftPos;
	     //   	bw = document.documentElement.clientWidth;
		  //      bh = document.documentElement.clientHeight;
		  //      leftPos = (Math.floor(bw/2)-2*w);
		  //      topPos = (Math.floor(bh/2)-h);
		  //      attributes = \"width=\" + w + \",height=\" + h + \",top=\" + topPos + \",left=\" + leftPos;
		  //      popUpWin = window.open ('".$pagina."', \"winName\", attributes,toolbar=0,menubar=0);
		  //      </script>";
		//	}
         //PSM FIN-DESPLEGAR POPUP CON INFO DE BANXICO 20110902

    if( ! $hubo_error )
	{
		//MAT -21Nov2012- Llave ASB para Circular X I
		$secure_site = $ini->read_var( "site", "SecureServer" ) . $ini->read_var( "site", "ServerNumber" ) . $ini->read_var( "site", "DomainPostfix" );

		$cliente_token="
				<form method='post'>
						<input type='hidden' name='CustID'>
						<input type='hidden' name='Cadpriv'>
						<input type='hidden' name='Trxn'>
						<input type='hidden' name='Access'>
						<table align='center' border='0' cellspacing='0' cellpadding='0' width='85%'>
							<tr>
								<td>Para obtener su <b>Estado de Cuenta Mensual</b>, ejecute los siguientes pasos:
								<ol>
									<LI>Es necesario capturar su <b>Clave ASB (Acceso Seguro Bajio)</b>para completar la transacci&oacute;n.</li>
									<li>Presione el bot&oacute;n <em>Aceptar</em> para continuar.</li>
								</ol>
								<hr size='1' noshade></td>
							</tr>
							<tr>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<td align='center' valign='middle'>
									<table align='center' border='0' cellspacing='0' cellpadding='0'>
										<tr>
										</tr>
									</table>
								</td>
							</tr>
							<tr><td>
							<form id=token_form method=post action='/procesarAjaxMenu.php' onsubmit= \"validaTknUser('edosctaActualTC'); return false;\">
							<input type='hidden' id='nomFunc' name='nomFunc' value='clienteasb'>
							<input type='hidden' id='org_form' name='org_form' value='tknForm'>
							<table width=98% align=center border=0 class=form_tbl>
								<tr>
									<td colspan=3 align=center>
										<table border=1 width=60% height=60px>
											<tr>
												<td colspan=2 border=1><b> Es necesario capturar su Clave ASB (Acceso Seguro Bajio)</b></td>
											</tr>
											<tr>
												<td colspan=2 align=center >
													<b>Clave ASB (Acceso Seguro Bajio):</b>
													<input type=password name=token id=token_value class=inputbox  size=15 maxlength=10 onKeyPress=\"if (token_valido(event,this) == true){ if (is_Enter(event) == true){validaTknUser('edosctaActualTC');}}else{return false}\" ></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td>
									</td>
									<td align=center>
										<input type=button name=btn_opcion id=btn_opcionA value=Aceptar class=button onmouseover=\"this.className='buttonON'\" onmouseout=\"this.className='button'\" onclick=\"if (validar_token('token_value','btn_opcionA')) { validaTknUser('edosctaActualTC');}\" >
										<input type=button name=btn_opcion id=btn_opcionC value=Cancelar class=button onmouseover=\"this.className='buttonON'\" onmouseout=\"this.className='button'\" onclick=\"document.location.href='https://$secure_site/article/articleview/196/1/7/'\" >
									</td>
									<td align=left></td>
									<td></td>
										</tr>
									</table>
								</form>
							</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</form>";
		//MAT -21Nov2012- Llave ASB para Circular X F
		switch($Access) {
			case "FrAc":
			{
				$Accion  = "EdoCta";
				$DiasPzo = "";   // Cadena con lista de tarjetas p actualizar en ovation char de 150
				$particularFields = "&Accion=".urlencode($Accion);
				$tr->blog($qki,"TarjetasCto", $FrAccount, $ToAccount, $DiasPzo, $Amount, $Accion);
				$transaccion_buffer = "";

				//MAT -21Nov2012- Llave ASB para Circular X I
				if ( $org_form == 'tknForm' || $session->variable( "r_tknOp" ) == "no" )
					//$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=tct&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer);
				//MAT -21Nov2012- Llave ASB para Circular X F

				$comboTC = $tarjetaTC ."-" . $nombreTC;
				$combo2TC= "<option value='".$comboTC."'>".$comboTC."</option>";
				foreach($obje as $o):
					$tarjetaTC =$o->tarjetaTC;
					$nombreTC=$o->nombreTC;
					  $combo2TC;
					// $combo2TC= "<option value='". $o->tarjetaTC . " - " $o->nombreTC"'>".$o->tarjetaTC . " - " $o->nombreTC."</option>";
				endforeach;

				$transaccion_buffer="<h1>
						<form method='post'>
									  <input type='HIDDEN' id='CustID' name='CustID' value='".$usr."'>
									  <input type='hidden' name='Cadpriv' value='".$priv."'>
									  <input type='hidden' name='Trxn' value='stm'>
									  <input type='hidden' name='Access' value='Process'>
									  <input type='hidden' name='Accion' value='EdoCta'>
									  <INPUT TYPE='HIDDEN' NAME='DiasPzo' VALUE='4201990000480039'>
									  <INPUT TYPE='HIDDEN' id='LadaB' VALUE= Month>
									  <INPUT TYPE='HIDDEN' NAME='Apocope' VALUE='2014'>

<table align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"85%\">
		<tr>
			<td>
				Para obtener su <b>Estado de Cuenta Mensual </b>, ejecute los siguientes pasos:
				<ol>

					<li>Seleccione el mes y año que requiera, el formato XML s&oacute;lo se encuentra disponible a partir de Enero 2013.</li>
					<li>Presione el bot&oacute;n <em>Aceptar</em> para continuar.</li>
				</ol>
				<hr size=\"1\" noshade>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align=\"center\">
				<table align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
					<tr>
						<td align=\"right\"><b>Tarjeta de Cr&eacute;dito:     </b></td>

						<td valign=\"TOP\">
									";
											$transaccion_buffer.="<select name='PAN' id='PAN'>";
																	foreach($obje as $o):
																		$tarjetaTC= $o->tarjeta;
																		$nombreTC=$o->nombreTC;
											$transaccion_buffer .=	"<option value='".$tarjetaTC." - ". $nombreTC."'>".$tarjetaTC ." - ". $nombreTC."</option>";
																	endforeach;

											$transaccion_buffer.= "</select>
						</td>
					</tr>
					<tr>
						<td>
									</br>
						</td>
					</tr>
					<tr>
						<td align=\"right\">
								<b>Mes:</b>
						</td>

						<td align=\"left\">
											<select name='Month' id='Month'>
													<option value='01'>Enero</option>
													<option value='02'>Febrero</option>
													<option value='03'>Marzo</option>
													<option value='04'>Abril</option>
													<option value='05'>Mayo</option>
													<option value='06'>Junio</option>
													<option value='07'>Julio</option>
													<option value='08'>Agosto</option>
													<option value='09' selected=''>Septiembre</option>
													<option value='10'>Octubre</option>
													<option value='11'>Noviembre</option>
													<option value='12'>Diciembre</option>
												</select>
						</td>
					</tr>
					<tr>
											<td>
												</br>
											</td>
					</tr>

					<tr>
						<td align=\"right\">
							<b>Año:</b>
						</td>
											<td align=\"left\">
											<select name='Year' id ='Year'>
												<option value='2013'>2013</option>
												<option value='2014' selected=''>2014</option>
											</select>

						</td>
					</tr>

					<tr>
						<td colspan=\"3\">&nbsp;</td>
					</tr>

					<tr>
						<td align=\"right\">
							<b>Formato:</b>
						</td>
							<td align=\"left\">
											<input checked id='Formato' name='Formato' type='radio' value='pdf' onclick='javascript:
											{ document.getElementById('Formato').checked = false; }'	 />PDF
										</td>
					</tr>
					<tr>
							<td>&nbsp;</td>
								<td align=\"left\">
											<input id='FormatoXml' name='Formato' type='radio' value='xml' onclick='javascript:
											{ document.getElementById('FormatoXml').checked = false; }' />XML
										</td>
					</tr>
					<tr>
					</tr>
					<tr>
							<td>
								</br>
							</td>
					</tr>
						<tr><td>&nbsp;</td></tr>
						<tr><td>&nbsp;</td></tr>

					<tr>
						<td align=\"center\" colspan=\"3\">

											<input id=\"OK\" type=\"button\" value=\"Aceptar\" onclick='javascript:procesarAjax();' name=\"OK\">
						</td>
				</tr>
			</table>
		</td>
</tr>



								</table>
								</form>
									</h1>


									";
				//$transaccion_buffer = $transaccion_buffer . $html_de_salida;
				$t->set_var( "transaccion_buffer", "" );
				$t->set_var( "intl-transaccion_operacion", "Tarjetas de Cr&eacute;dito" );
				$t->pparse( "output", "saldosTC_tpl" );

				if ($session->variable( "r_tknOp" ) == "no" )
				{
					$transaccion_buffer=str_replace("type=\"SUBMIT\"","type=\"BUTTON\"",$transaccion_buffer);
					$transaccion_buffer=str_replace("onclick=\"javascript:{ document.getElementById( 'OK' ).disabled = true; document.forms[ 0 ].submit(); }\"","onclick=\"javascript:procesarAjax();\"",$transaccion_buffer);
				}
				else
				{
				//MAT -21Nov2012- Llave ASB para Circular X I
					//echo "Transaccionbuffer[". $transaccion_buffer ."]";
					//$transaccion_buffer=str_replace("<input id=\"OK\" name=\"OK\" onclick=\"javascript:{ document.getElementById( 'OK' ).disabled = true; document.forms[ 0 ].submit(); }\" type=\"submit\" value=\"Aceptar\">",$cliente_token,$transaccion_buffer);
					if ( $org_form == 'tknForm')
					{
						//ya se solicito token
						$transaccion_buffer=str_replace("<input id=\"OK\" name=\"OK\" onclick=\"javascript:{ document.getElementById( 'OK' ).disabled = true; document.forms[ 0 ].submit(); }\" type=\"SUBMIT\" value=\"Aceptar\">",$cliente_token,$transaccion_buffer);
					}
					else{
						$transaccion_buffer=$cliente_token;
					}
				//MAT -21Nov2012- Llave ASB para Circular X F
				}

				break;
			}
			case "Process":
			//case "Confirm":
			{
				$FrAccount = trim( $FrAccount );
				$tarjeta = $FrAccount;
				$Formato   = trim( $Formato   );
				$particularFields = "&FrAccount=" . urlencode( $FrAccount ) . "&Month=".urlencode( $Month )."&Year=" . urlencode( $Year );
				$month = array( "", "enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre" );

				$particularFields .= "&PAN=" . urlencode($tarjeta)."&Empresa=01";
				$particularFields .= "&Accion=EdoCta";
				$transaccion_buffer = "";

				/*if(($Formato == "xml" && $Year == "2011") ||
					(( $Year . $Month ) > ( date( "Y", ( time() - ( 60 * 60 * 24 * 5 ) ) ) . date( "m", ( time() - ( 60 * 60 * 24 * 5 ) ) ) ) ))*/
				if(($Formato == "xml" && $Year == "2011") ||
					(( $Year . $Month ) > date("Y").date("m")))
					{
						$transaccion_buffer = "<p>No existe el Estado de Cuenta correspondiente al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>.</p>";
						$transaccion_buffer .= "Si desea descargar otro Estado de Cuenta haga clic <a href=\"/transaccion/edosctaActualTC/\">aqu&iacute;</a>.";

						$transaccion_buffer = $transaccion_buffer . $html_de_salida;
						$t->set_var( "intl-transaccion_operacion", "Estados de cuenta de tarjetas de cr&eacute;dito" );
						$t->set_var( "transaccion_buffer", "" );
						$t->pparse( "output", "edosctaActualTC_tpl" );
					}
				else{
					/*$Access = "Process";
					$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=tct&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // tarjetas de credito
					}*/

					$origen = $Formato == "pdf" ? "TC" : "TCXML";

					//::::::::::::::::::::::::::::LLAMADO AL WS::::::::::::::::::::::::::::::::::::::
					$numTC = substr($tarjeta, 0, 4);
					$numTC = $numTC."-".substr($tarjeta, 4, 4);
					$numTC = $numTC."-".substr($tarjeta, 8, 4);
					$numTC = $numTC."-".substr($tarjeta, 12, 4);

					$edosExistentes = countEstadosCta($origen, $numTC, $Year, $Month, $Formato);
					//$archivo_estado = getDocumento($numTC, $origen);

					//::::::::::::::::::::::::::::LLAMADO AL WS::::::::::::::::::::::::::::::::::::::

					$numEdos = 0;
					//Se valida del response si existen estados de cuenta para la fecha seleccionada
					for($x=0; $x < count($edosExistentes["vals"]); $x++){ //Se cambia strlen por count
						if( $edosExistentes["vals"][$x]["tag"] == "CUENTA"
							&& $edosExistentes["vals"][$x]["attributes"]["TIPO"] == "tc"
							&& $edosExistentes["vals"][$x]["attributes"]["NUMERO"] > 0){
							$numEdos = 1;
						}
					}

					if($numEdos != 0){
						$name_file = $Year.$Month.'_'.$numTC.".".$Formato;

						$t->set_file( array( "estadosdecuenta_tpl" => "estadosdecuentaxml_process.tpl" ) );

						//$size = filesize($fullname_file); //echo "SIZE => ".$size/1024;
						$t->set_var( "file_id", "1" );
						$t->set_var( "file_description", "Estado de Cuenta de " . $month[ intval( $Month ) ] ." de " . $Year );
						//$t->set_var( "file_size", round($size/1024) ); //Se divide entre 100 porque el valor lo devuelve en bytes
						$t->set_var( "file_size", "" ); //Se divide entre 100 porque el valor lo devuelve en bytes
						//$t->set_var( "file_unit", "KB" );
						$t->set_var( "file_unit", "" );
						$t->set_var( "original_file_name", $name_file );
						$t->set_var( "original_file_name_without_spaces", $name_file );
						$t->parse( "file", "file_tpl", true );
						$transaccion_buffer = "";
						$t->set_var( "transaccion_buffer", $transaccion_buffer );
						$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
					}
					else
					{
						//MAT -21Nov2012- Mensajes por fecha y tipo tarjeta X I
						//$transaccion_buffer = "<p>A&uacute;n no ha sido generado el Estado de Cuenta correspondiente al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>.</p><p>Favor de volverlo a intentar 5 d&iacute;as despu&eacute;s del d&iacute;a de corte.</p>";

						$int_numtc = (int)substr(str_replace( "-", "", $numTC ), 0, 8 );
						$i_currDate = (int)date("Ymd");

						if ( ((int)date("m")) == 1 )
							$i_prevMonth = 12;
						else
							$i_prevMonth = (((int)date("m"))-1);

						if ( ((int)date("m")) == 12 )
						{
							$s_nextMonth = "01";
							$s_Year = (((int)date("Y"))+1);
						}
						else
						{
							$s_nextMonth = (((int)date("m"))+1);
							$s_Year = $Year;
						}

						if ( strlen($s_nextMonth) <= 1 )
							$s_nextMonth = "0".$s_nextMonth;

						//echo "<br/> CurrentDate[". $i_currDate ."] s_Year[". $s_Year ."] s_nextMonth[". $s_nextMonth ."]  prevMonth [". $i_prevMonth ."]";
						switch( $int_numtc )
						{
							case 42019960:
							case 42019986:
								//echo "<br/>PLATINUM[". $int_numtc ."]";
								if( ((int)date("Y")) == ((int)$Year) && ((int)date("m")) == ((int)$Month) )
								{
									if( $i_currDate >= ( (int)($Year.$Month."05" ) ) &&  $i_currDate <= ( (int)($Year.$Month."10" ) )  )
										$transaccion_buffer = "<p>A&uacute;n no ha sido generado el Estado de Cuenta correspondiente al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>.</p><p>Favor de volverlo a intentar 5 d&iacute;as despu&eacute;s del d&iacute;a de corte.</p>";

									else if( $i_currDate > ( (int)($Year.$Month."10" ) ) &&  $i_currDate <= ( (int)($s_Year.$s_nextMonth."04" ) )  )
										$transaccion_buffer = "<p>Su tarjeta no tuvo movimientos correspondientes al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>.</p>";

									else if( $i_currDate < ( (int)($Year.$Month."05" ) ) )
										$transaccion_buffer = "<p>A&uacute;n no ha sido generado el Estado de Cuenta correspondiente al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>.</p><p>Favor de volverlo a intentar 5 d&iacute;as despu&eacute;s del d&iacute;a de corte.</p>";
								}
								else if ( ((int)date("Y")) == ((int)$Year) && $i_prevMonth == ((int)$Month) )
								{
									if( $i_currDate > ( (int)($Year.$Month."10" ) ) )
										$transaccion_buffer = "<p>Su tarjeta no tuvo movimientos correspondientes al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>.</p>";
								}
								else
								{
									$transaccion_buffer = "<p>Su tarjeta no tuvo movimientos correspondientes al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>.</p>";
								}
							break;
							case 42019900 :
							case 42019980 :
								//echo "<br/>CLASICA[". $int_numtc ."] date[". (date("m")+1) ."]";
								if( ((int)date("Y")) == ((int)$Year) && ((int)date("m")) == ((int)$Month) )
								{
									if( $i_currDate >= ( (int)($Year.$Month."25" ) ) &&  $i_currDate <= ( (int)($Year.$Month."30" ) )  )
										$transaccion_buffer = "<p>A&uacute;n no ha sido generado el Estado de Cuenta correspondiente al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>.</p><p>Favor de volverlo a intentar 5 d&iacute;as despu&eacute;s del d&iacute;a de corte.</p>";

									else if( $i_currDate > ( (int)($Year.$Month."30" ) )   &&  $i_currDate <= ( (int)($s_Year.$s_nextMonth."24" ) )  )
										$transaccion_buffer = "<p>Su tarjeta no tuvo movimientos correspondientes al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>. </p>";

									else if( $i_currDate < ( (int)($Year.$Month."25" ) ) )
										$transaccion_buffer = "<p>A&uacute;n no ha sido generado el Estado de Cuenta correspondiente al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>.</p><p>Favor de volverlo a intentar 5 d&iacute;as despu&eacute;s del d&iacute;a de corte.</p>";

								}
								else if ( ((int)date("Y")) == ((int)$Year) && $i_prevMonth == ((int)$Month) )
								{
									if( $i_currDate > ( (int)($Year.$Month."30" ) )   )
										$transaccion_buffer = "<p>Su tarjeta no tuvo movimientos correspondientes al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>.</p>";
								}
								else
								{
									$transaccion_buffer = "<p>Su tarjeta no tuvo movimientos correspondientes al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>.</p>";
								}
							break;
							case 42019940:
							case 42019985:
								//echo "ORO[". $int_numtc ."]";
								if( ((int)date("Y")) == ((int)$Year) && ((int)date("m")) == ((int)$Month) )
								{
									if( $i_currDate >= ( (int)($Year.$Month."15" ) ) &&  $i_currDate <= ( (int)($Year.$Month."20" ) )  )
										$transaccion_buffer = "<p>A&uacute;n no ha sido generado el Estado de Cuenta correspondiente al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>.</p><p>Favor de volverlo a intentar 5 d&iacute;as despu&eacute;s del d&iacute;a de corte.</p>";
									else if( $i_currDate > ( (int)($Year.$Month."20" ) ) &&  $i_currDate <= ( (int)($s_Year.$s_nextMonth."14" ) )  )
										$transaccion_buffer = "<p>Su tarjeta no tuvo movimientos correspondientes al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>. </p>";
									else if( $i_currDate < ( (int)($Year.$Month."15" ) ) )
										$transaccion_buffer = "<p>A&uacute;n no ha sido generado el Estado de Cuenta correspondiente al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>.</p><p>Favor de volverlo a intentar 5 d&iacute;as despu&eacute;s del d&iacute;a de corte.</p>";
								}
								else if ( ((int)date("Y")) == ((int)$Year) && $i_prevMonth == ((int)$Month) )
								{
									if( $i_currDate > ( (int)($Year.$Month."20" ) )  )
										$transaccion_buffer = "<p>Su tarjeta no tuvo movimientos correspondientes al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>.</p>";
								}
								else
								{
									$transaccion_buffer = "<p>Su tarjeta no tuvo movimientos correspondientes al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>.</p>";
								}
							break;
							default :
								if ( substr($int_numtc, 0,1) == 6 )
								{
									//echo "Inicia(6)[". $int_numtc ."]";
									if( ((int)date("Y")) == ((int)$Year) && ((int)date("m")) == ((int)$Month) )
									{
										if( $i_currDate >= ( (int)($Year.$Month."25" ) ) &&  $i_currDate <= ( (int)($Year.$Month."30" ) )  )
											$transaccion_buffer = "<p>A&uacute;n no ha sido generado el Estado de Cuenta correspondiente al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>.</p><p>Favor de volverlo a intentar 5 d&iacute;as despu&eacute;s del d&iacute;a de corte.</p>";
										else if( $i_currDate > ( (int)($Year.$Month."30" ) )   &&  $i_currDate <= ( (int)($s_Year.$s_nextMonth."24" ) )  )
											$transaccion_buffer = "<p>Su tarjeta no tuvo movimientos correspondientes al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>. </p>";
										else if( $i_currDate < ( (int)($Year.$Month."25" ) ) )
											$transaccion_buffer = "<p>A&uacute;n no ha sido generado el Estado de Cuenta correspondiente al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>.</p><p>Favor de volverlo a intentar 5 d&iacute;as despu&eacute;s del d&iacute;a de corte.</p>";
									}
									else if ( ((int)date("Y")) == ((int)$Year) && $i_prevMonth == ((int)$Month) )
									{
										if( $i_currDate > ( (int)($Year.$Month."30" ) ) &&  $i_currDate <= ( (int)($Year.date("m")."24" ) )  )
											$transaccion_buffer = "<p>Su tarjeta no tuvo movimientos correspondientes al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>.</p>";
									}
									else
									{
										$transaccion_buffer = "<p>Su tarjeta no tuvo movimientos correspondientes al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>.</p>";
									}
								}
								else
								{
									$transaccion_buffer = "<p>A&uacute;n no ha sido generado el Estado de Cuenta correspondiente al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>.</p><p>Favor de volverlo a intentar 5 d&iacute;as despu&eacute;s del d&iacute;a de corte.</p>";
								}
							break;
						}
						//MAT -21Nov2012- Mensajes por fecha y tipo tarjeta X F
						$transaccion_buffer .= "Si desea descargar otro Estado de Cuenta haga clic <a href=\"/transaccion/edosctaActualTC/\">aqu&iacute;</a>.";

						$t->set_var( "intl-transaccion_operacion", "Estados de cuenta de tarjetas de cr&eacute;dito" );
						$t->set_var( "transaccion_buffer", "" );
						$t->pparse( "output", "edosctaActualTC_tpl" );
					}
				}
				break;
			}
		}
    }

}
else {
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/edosctaActualTC/" );
    $t->pparse( "output", "user_login_tpl" );
}

	if($_POST['Otra'] == 'Download'){

		$nombreArchivo = $_POST['Year'].$_POST['Month'].'_'.$_POST['FrAccount'].".".$_POST['Formato'];
		getDocumento($_POST['Origen'], $_POST['FrAccount'], $_POST['Year'], $_POST['Month'], $_POST['Formato'], $nombreArchivo);
	}

	$transaccion_buffer=str_replace("onclick=\"javascript:{ document.getElementById( 'OK' ).disabled = true; document.forms[ 0 ].submit(); }\" type=\"submit\"","onclick=\"javascript:procesarAjax();\" type=\"button\""	,$transaccion_buffer);
	$transaccion_buffer=str_replace("href=\"/transaccion/edosctaActualTC/\"","HREF=\"#\" onclick=\"javascript:iniProcesarMenu('edosctaActualTC','')\" ",$transaccion_buffer);

	$transaccion_buffer="<script>
						function getArchivoEstadoCuenta()
						{
							$.ajax({
								type: 'POST',
								url: '/procesarAjaxMenu.php',
								data: 'nomFunc=edosctaActualTC&Access=Download&Otra=Download&Origen=$origen&FrAccount=$numTC&Year=$Year&Month=$Month&Formato=$Formato',
								dataTypedataType: 'html',
								success: function(datos)
										{
											window.location.href = '/filedownload/'+ $('input[name=filename]').val();
										}
								});
						}

									function procesarAjax()
									{
										var parametros=	'CustID:'+document.getElementsByName('CustID')[0].value+
														',Cadpriv:'+document.getElementsByName('Cadpriv')[0].value+
														',Trxn:'+document.getElementsByName('Trxn')[0].value+
														//',Accion:'+document.getElementsByName('Accion')[0].value+
														',Access:'+document.getElementsByName('Access')[0].value;

										if (document.getElementsByName('PAN')[0]!=null)
										{
											if (document.getElementsByName('PAN')[0].type!='HIDDEN' && document.getElementsByName('PAN')[0].type!='hidden')
											{
												parametros+=',FrAccount:'+document.getElementsByName('PAN')[0].options[document.getElementsByName('PAN')[0].selectedIndex].value;

											}
											else
											{
												parametros+=',FrAccount:'+document.getElementsByName('PAN')[0].value;
												//alert('pan :: '+document.getElementsByName('PAN')[0].value);
											}
										}

										if (document.getElementsByName('Day')[0]!=null)
										{
											parametros+=',Day:'+document.getElementsByName('Day')[0].options[document.getElementsByName('Day')[0].selectedIndex].value;
										}

										if (document.getElementsByName('Month')[0]!=null)
										{
											parametros+=',Month:'+document.getElementsByName('Month')[0].options[document.getElementsByName('Month')[0].selectedIndex].value;
										}

										if (document.getElementsByName('Year')[0]!=null)
										{
											parametros+=',Year:'+document.getElementsByName('Year')[0].options[document.getElementsByName('Year')[0].selectedIndex].value;
										}

										if (document.getElementsByName('DayF')[0]!=null)
										{
											parametros+=',DayF:'+document.getElementsByName('DayF')[0].options[document.getElementsByName('DayF')[0].selectedIndex].value;
										}

										if (document.getElementsByName('MonthF')[0]!=null)
										{
											parametros+=',MonthF:'+document.getElementsByName('MonthF')[0].options[document.getElementsByName('MonthF')[0].selectedIndex].value;
										}

										if (document.getElementsByName('YearF')[0]!=null)
										{
											parametros+=',YearF:'+document.getElementsByName('YearF')[0].options[document.getElementsByName('YearF')[0].selectedIndex].value;
										}

										if (document.getElementsByName('File')[0]!=null)
										{
											parametros+=',File:'+document.getElementsByName('File')[0].options[document.getElementsByName('File')[0].selectedIndex].value;
										}

										if (document.getElementsByName('Accion2')[0]!=null)
										{
											if (document.getElementsByName('Accion2')[0].type!='HIDDEN' && document.getElementsByName('Accion2')[0].type!='hidden')
											{
												parametros+=',Accion2:'+$('input:radio[name=Accion2]:checked').val();
											}
											else
											{
												parametros+=',Accion:'+document.getElementsByName('Accion2')[0].value;
											}
										}
										if (document.getElementsByName('Reporte')[0]!=null)
										{
											if (document.getElementsByName('Reporte')[0].type!='HIDDEN' && document.getElementsByName('Reporte')[0].type!='hidden')
											{
												parametros+=',Reporte:'+$('input:radio[name=Reporte]:checked').val();
											}
											else
											{
												parametros+=',Reporte:'+document.getElementsByName('Reporte')[0].value;
											}
										}
										//DBA 182332 CFD
										if (document.getElementsByName('Formato')[0]!=null)
										{
											if (document.getElementsByName('Formato')[0].type!='HIDDEN' && document.getElementsByName('Formato')[0].type!='hidden')
											{
												parametros+=',Formato:'+$('input:radio[name=Formato]:checked').val();
											}
											else
											{
												parametros+=',Formato:'+document.getElementsByName('Formato')[0].value;
											}
										}
										//DBA 182332 CFD
										//MAT -21Nov2012- Llave ASB para Circular X I
										if (document.getElementsByName('org_form')[0]!=null)
										{
											parametros+=',org_form:'+document.getElementsByName('org_form')[0].value;
										}
										//MAT -21Nov2012- Llave ASB para Circular X F

										iniProcesarMenu('edosctaActualTC', parametros);
									}


									$(document).ready(function (){
											$('#token_value').focus()
											$('form').bind('submit', function() {
												return false;
											}
										)
									});


								</script>".$transaccion_buffer;
?>
