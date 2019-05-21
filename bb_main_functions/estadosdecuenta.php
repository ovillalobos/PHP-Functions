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
include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezhttptool.php" );
include_once( "classes/ezdate.php" );
include_once( "classes/ezlog.php" );
include_once( "classes/ezfile.php" );

include_once( "ezuserbb/classes/ezuser.php" );
include_once( "ezfilemanager/classes/ezvirtualfile.php" );
include_once( "ezfilemanager/classes/ezvirtualfolder.php" );

include_once( "eztransaccion/classes/eztransaccion.php" );
include_once( "ezsession/classes/ezsession.php" );
//06May2010   ACS  Llave ASB para Circular X I
	include("eztransaccion/user/include/controles_javascript.inc");
//06May2010   ACS  Llave ASB para Circular X F
include("eztransaccion/user/include/httplib.php");		// JAC 05NOV2010
include("eztransaccion/user/include/tcpipnexions.php"); // JAC 05NOV2010
include("xmlparser.inc");		// JAC 05NOV2010
$session =& eZSession::globalSession();

if( !$session->fetch() )
    $session->store();

$ini =& $GLOBALS["GlobalSiteIni"];

$Language = $ini->read_var( "eZTransaccionMain", "Language" );
// $backend = $ini->read_var( "eZTransaccionMain", "Backend" );
$ServerNumber = $ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );

$backend = "gw" . $ServerNumber. $DomainPostfix;
$ImageDir = $ini->read_var( "eZFileManagerMain", "ImageDir" );
$Limit = $ini->read_var( "eZFileManagerMain", "Limit" );
$ShowUpFolder = $ini->read_var( "eZFileManagerMain", "ShowUpFolder" ) == "enabled";

$user =& eZUserBB::currentUser();

// ********************************************************************************************
// DGM I

function ultimoDia( $mes, $anio )
{
	//echo ("Recibi: ($mes)($anio) ");
	$ultimo_dia = 28;
	while (checkdate ($mes, $ultimo_dia, $anio) )
	{
		//echo ("en while.. $ultimo_dia");
		$ultimo_dia ++;
	}
	return ($ultimo_dia-1);
}

//ACS-28Ago2008 Inicio Fecha final de estado de cuenta
// function parserHTMLtoXLS ( $HTML, $fechaEDO, $FrAccount )
// {
function parserHTMLtoXLS ( $HTML, $fechaEDO, $fechaEDOFin, $FrAccount )
{
//ACS-28Ago2008 Fin Fecha final de estado de cuenta
	//echo "estadosdecuenta.php parserHTMLtoXLS() FrAccount[" . $FrAccount . "]";

	$posFIN = strpos($HTML,"<TABLE BORDER=");
	if ($posFIN > 0)
	{
		$HTML = substr($HTML, $posFIN);  //regresa todos los caraceres despues de la posicion indicada
		$posFIN				= strpos($HTML,"<BR/><INPUT NAME=\"Dias\"");

		if ($posFIN > 0)
		{
			$HTML = substr($HTML,0, $posFIN);
		}
		// Se le agrega el encabezado.
		//ACS-28Ago2008 Inicio Fecha final de estado de cuenta
		//$HTML = "Banco del Bajío, S. A.\r\nEstado de Cuenta Informativo.\r\n \r\nCuenta: ".$FrAccount."\r\nMovimientos del ".$fechaEDO." al ".$fechaEDO." \r\n".$HTML;
		$HTML = "Banco del Bajío, S. A.\r\nEstado de Cuenta Informativo.\r\n \r\nCuenta: ".$FrAccount."\r\nMovimientos del ".$fechaEDO." al ".$fechaEDOFin." \r\n".$HTML;
		//ACS-28Ago2008 Fin Fecha final de estado de cuenta
		$HTML = preg_replace("#<TABLE ([^>]+)#","",	$HTML );  //(buscar, remplazo, en_donde)     /*HB AGL*/
		$HTML = preg_replace("#<TR ([^>]+)#",	"",		$HTML );  //(buscar, remplazo, en_donde)	 /*HB AGL*/
		$HTML = preg_replace("#<TD ([^>]+)#",	"",		$HTML );  //(buscar, remplazo, en_donde)	 /*HB AGL*/
		$HTML = preg_replace("#<BR ([^>]+)#",	"",		$HTML );  //(buscar, remplazo, en_donde)	 /*HB AGL*/
		$HTML = preg_replace("#<TH ([^>]+)#",	"",		$HTML );  //(buscar, remplazo, en_donde)	 /*HB AGL*/
		$HTML = preg_replace("#<FONT ([^>]+)#",	"",	$HTML );  //(buscar, remplazo, en_donde)	 /*HB AGL*/

		$HTML = preg_replace("#<TABLE>#",	"",	$HTML );  //(buscar, remplazo, en_donde)			 /*HB AGL*/
		$HTML = preg_replace("#<TR>#",	"",	$HTML );  //(buscar, remplazo, en_donde)			 /*HB AGL*/
		$HTML = preg_replace("#<TD>#",	"",	$HTML );  //(buscar, remplazo, en_donde)			 /*HB AGL*/
		$HTML = preg_replace("#<BR>#",	"",	$HTML );  //(buscar, remplazo, en_donde)			 /*HB AGL*/
		$HTML = preg_replace("#<TH>#",	"",	$HTML );  //(buscar, remplazo, en_donde)			 /*HB AGL*/
		$HTML = preg_replace("#<FONT>#",	"",	$HTML );  //(buscar, remplazo, en_donde)			 /*HB AGL*/
		$HTML = preg_replace("#<EM>#",	"",	$HTML );  //(buscar, remplazo, en_donde)			 /*HB AGL*/

		$HTML = preg_replace("#</TABLE>#",	"",		$HTML );  //(buscar, remplazo, en_donde)	 /*HB AGL*/
		$HTML = preg_replace("#</TR>#",		"\r\n",	$HTML );  //(buscar, remplazo, en_donde)	 /*HB AGL*/
		$HTML = preg_replace("#</TD>#",		"\t",	$HTML );  //(buscar, remplazo, en_donde)	 /*HB AGL*/
		$HTML = preg_replace("#</FONT>#",		"",		$HTML );  //(buscar, remplazo, en_donde)	 /*HB AGL*/
		$HTML = preg_replace("#</TH>#",		"\t",	$HTML );  //(buscar, remplazo, en_donde)	 /*HB AGL*/
		$HTML = preg_replace("#</EM>#",		"",		$HTML );  //(buscar, remplazo, en_donde)	 /*HB AGL*/
		$HTML = preg_replace("#<BR/#",		"",		$HTML );  //(buscar, remplazo, en_donde)	 /*HB AGL*/
		$HTML = preg_replace("#>#",			"",		$HTML );  //(buscar, remplazo, en_donde)	 /*HB AGL*/
		$HTML .= "*La información contenida en este archivo es exclusivamente de carácter informativo.\r\n";
		$HTML .= "*El Estado de Cuenta Oficial lo puede obtener en su sucursal o se le hará llegar a su domicilio, dependiendo de su indicación al momento de aperturar la cuenta.\r\n";

		// Se separa todo linea x linea buscando las transaciones o movimientos para sacar la palabra recibo(xx) de la columna "descripcion"

		$archivoXLS = "";
		$posINI		= 0;

//REF WMA-05sep2008, Inicio
		//$HTML	= str_replace( "FECHA\t",			  "\r\nFECHA\tRECIBO\t",	$HTML );
		$HTML	= str_replace( "FECHA\t",			  "\r\nFECHA\tRECIBO/DOCTO\t",	$HTML );
//REF WMA-05sep2008, Fin
		$HTML	= str_replace( "MONTO NETO\t",		  "",						$HTML );
		$HTML	= str_replace( "MONTO\tCOMISION\tIVA","COMISION\tIVA\tMONTO",	$HTML );
		$HTML	= str_replace( "SALDO\t",				  "SALDO \r\n|",			$HTML );


		$posFIN	= strpos($HTML,"\r");

		while ( $posFIN > 0 )
		{
			// el renglon original viene asi "18Feb2008	Pago de Servicios a su  507111 Cheqsi-1 Suc. matrizGobierno-colimita Titular:  adf REF. 1234567890 REcibo 445480002 	(15.00)	NA*	NA*	(15.00)	229,168.28	"
			// y se pretende que la palabra "reciboxxx" se separe en un campo despues de la fecha.

		//REF WMA-05sep2008, Inicio. Debemos separar el número de cheque y colocarlo en la columna de Recibo. Comento el código anterior y hago lo nuevo
//			$renglon		= substr		($HTML, $posINI, $posFIN-$posINI-1	);
//			$renglon		= str_replace	("NA*","0",$renglon					);
//			$posBuscando	= strpos		($renglon,"Recibo #"				);
//
//			if ( $posBuscando > 0 ) //ya encontre una cadena "Recibo #" ahora buscamos la posicion del siguiente tabulador
//			{
//				$linea  = substr($renglon,	$posBuscando	);	// Toda la linea empezando desde Recibo
//				$posTab = strpos($linea,	"\t"			);	// Posicion del 1er tab despues de Recibo
//				$recibo = substr($linea,	0, $posTab		);	//aqui traemos "Recibo # 1321321\t"
//
//				$renglon		= str_replace($recibo, " ", $renglon);	//eliminamos la palabra recibo xxxx del renglon y le ponemos un tab
//				$recibo			= substr($recibo, 9		);				//le quitamos la palabra "Recibo #"
//				$posBuscando	= strpos($renglon,"\t"	);				// insertamos la palabra recibo despues de la fecha (despues del 1er tab)
//				$renglon		= substr($renglon,0, $posBuscando+1).$recibo."\t".substr($renglon,$posBuscando+1);
//
//				/* tenemos en el renglon
//				  0     1        2         3      4      5   6    7
//				fecha recibo descripcion monto comision iva neto saldo
//
//				se pretende dejar asi
//				  0     1        2           4      5     3   7
//				fecha recibo descripcion  comision iva  monto saldo
//
//				*/
//
//				$renglon = substr($renglon,1);
//
//				$campos	 = explode("\t", $renglon);
//
//
//				if ( strlen( $campos[0] ) == 8  ) // estaba cambiado esto
//				{
//					$campos[0] = "0" . $campos[0];
//				}
//
//				$mesLetra = strtolower(substr($campos[0],2,3)); //feb, mar, abr.
//
//				switch ( $mesLetra )
//				{
//					case "ene":
//					case "jan":
//								$mesLetra = "01";
//								break;
//					case "feb":
//								$mesLetra = "02";
//								break;
//					case "mar":
//								$mesLetra = "03";
//								break;
//					case "abr":
//					case "apr":
//								$mesLetra = "04";
//								break;
//					case "may":
//								$mesLetra = "05";
//								break;
//					case "jun":
//								$mesLetra = "06";
//								break;
//					case "jul":
//								$mesLetra = "07";
//								break;
//					case "ago":
//					case "aug":
//								$mesLetra = "08";
//								break;
//					case "sep":
//								$mesLetra = "09";
//								break;
//					case "oct":
//								$mesLetra = "10";
//								break;
//					case "nov":
//								$mesLetra = "11";
//								break;
//					case "dec":
//					case "dic":
//								$mesLetra = "12";
//								break;
//				}
//
//				$campos[3]	= str_replace( ",",	"",	$campos[3] );
//				$campos[7]	= str_replace( ",",	"",	$campos[7] );
//				$campos[0] = substr($campos[0],5).$mesLetra.substr($campos[0],0,2); // formato de dd-mmm-yy aaaammdd
//				$renglon = $campos[0]."\t".$campos[1]."\t".$campos[2]."\t".$campos[4]."\t".$campos[5]."\t".$campos[3]."\t".$campos[7]."\r\n";
//			}

			$renglon		= substr		($HTML, $posINI, $posFIN-$posINI-1	);
			$renglon		= str_replace	("NA*","0",$renglon					);
			$posTracer		= strpos		($renglon,"número de cheque"		);
			$posBuscando	= strpos		($renglon,"Recibo #"				);
			$ingreso		= "no";

			if ($posTracer > 0) //encontre una cadena con el número de cheque
			{
				//cuando la descripción viene con número de cheque xxx, el excel debe mantener la descripción que llegó pero sacar el número
				//de cheque en la columna del recibo

				$linea  = substr($renglon,	$posTracer	    );	// Toda la linea empezando desde número de cheque
				$posTab = strpos($linea,	"\t"			);	// Posicion del 1er tab despues de número de cheque
				$posRec	= strpos ($linea,   "Recibo #"		);  // Posicion donde comienza la palabra Recibo #
				$recibo = substr($linea,	0, $posRec );	    //aqui traemos "número de cheque 1321321\t"

				$recibo			= str_replace("número de cheque"," ", $recibo); //elimino la palabra número de cheque
				$posBuscando	= strpos($renglon,"\t"	);				// insertamos la palabra recibo despues de la fecha (despues del 1er tab)
				$renglon		= substr($renglon,0, $posBuscando+1).$recibo."\t".substr($renglon,$posBuscando+1);

				$ingreso = "si";

			}
			else
			{
				if ( $posBuscando > 0 ) //ya encontre una cadena "Recibo #" ahora buscamos la posicion del siguiente tabulador
				{
					$linea  = substr($renglon,	$posBuscando	);	// Toda la linea empezando desde Recibo
					$posTab = strpos($linea,	"\t"			);	// Posicion del 1er tab despues de Recibo
					$recibo = substr($linea,	0, $posTab		);	//aqui traemos "Recibo # 1321321\t"

					$renglon		= str_replace($recibo, " ", $renglon);	//eliminamos la palabra recibo xxxx del renglon y le ponemos un tab
					$recibo			= substr($recibo, 9		);				//le quitamos la palabra "Recibo #"
					$posBuscando	= strpos($renglon,"\t"	);				// insertamos la palabra recibo despues de la fecha (despues del 1er tab)
					$renglon		= substr($renglon,0, $posBuscando+1).$recibo."\t".substr($renglon,$posBuscando+1);

					$ingreso = "si";
				}
			}

			if ($ingreso == "si")
			{
				/* tenemos en el renglon
				  0     1        2         3      4      5   6    7
				fecha recibo descripcion monto comision iva neto saldo

				se pretende dejar asi
				  0     1        2           4      5     3   7
				fecha recibo descripcion  comision iva  monto saldo

				*/

				$renglon = substr($renglon,1);

				$campos	 = explode("\t", $renglon);


				if ( strlen( $campos[0] ) == 8  ) // estaba cambiado esto
				{
					$campos[0] = "0" . $campos[0];
				}

				$mesLetra = strtolower(substr($campos[0],2,3)); //feb, mar, abr.

				switch ( $mesLetra )
				{
					case "ene":
					case "jan":
								$mesLetra = "01";
								break;
					case "feb":
								$mesLetra = "02";
								break;
					case "mar":
								$mesLetra = "03";
								break;
					case "abr":
					case "apr":
								$mesLetra = "04";
								break;
					case "may":
								$mesLetra = "05";
								break;
					case "jun":
								$mesLetra = "06";
								break;
					case "jul":
								$mesLetra = "07";
								break;
					case "ago":
					case "aug":
								$mesLetra = "08";
								break;
					case "sep":
								$mesLetra = "09";
								break;
					case "oct":
								$mesLetra = "10";
								break;
					case "nov":
								$mesLetra = "11";
								break;
					case "dec":
					case "dic":
								$mesLetra = "12";
								break;
				}

				$campos[3]	= str_replace( ",",	"",	$campos[3] );
				$campos[7]	= str_replace( ",",	"",	$campos[7] );
				$campos[0] = substr($campos[0],5).$mesLetra.substr($campos[0],0,2); // formato de dd-mmm-yy aaaammdd
				$renglon = $campos[0]."\t".$campos[1]."\t".$campos[2]."\t".$campos[4]."\t".$campos[5]."\t".$campos[3]."\t".$campos[7]."\r\n";
			}

//REF WMA-05sep2008, Fin.

			$archivoXLS .= $renglon;
			$posINI		 = $posFIN+1;
			$posFIN		 = strpos($HTML,"\r", $posINI);

		}

		$HTML = $archivoXLS;
	}

	return ( $HTML );
}

// **************


// DebugBreak();
if ( $user )
{
	$session =& eZSession::globalSession();

	if ( !$session->fetch() )
	{
		$session->store();
	}

	$tr = new eZTransaccion( );
	$usr = $session->variable( "r_usr" );
	$qki = $session->variable( "r_qki" );
	$priv = $session->variable( "r_priv" );
	//JAC OCT2010 INI
	$perfil = $session->variable("r_perfil");
	//JAC OCT2010 FIN
	//echo "valor del perfil...[$perfil].....";
	$transaccion_buffer = "";
//HB
	if(!empty($_POST['Reporte']))
        $Reporte = $_POST['Reporte'];
        
   if(!empty($_POST['Access']))
        $Access = $_POST['Access'];
        
   if(!empty($_POST['Accion']))
        $Accion = $_POST['Accion'];
        
   if(!empty($_POST['i']))
        $i = $_POST['i'];
        
   if(!empty($_POST['FrAccount']))
        $FrAccount = $_POST['FrAccount'];
        
   if(!empty($_POST['Day']))
        $Day = $_POST['Day'];
        
   if(!empty($_POST['Month']))
        $Month = $_POST['Month'];
        
  	if(!empty($_POST['Year']))
        $Year = $_POST['Year'];
        
   if(!empty($_POST['DayF']))
        $DayF = $_POST['DayF'];
        
   if(!empty($_POST['MonthF']))
        $MonthF = $_POST['MonthF'];
      
   if(!empty($_POST['YearF']))
        $YearF = $_POST['YearF'];
//HB
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $Reporte ) ) {
		$Reporte = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $Access ) || empty( $Access ) ) {
		$Access = "FrAc";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $Accion ) ) {
		$Accion = "";
	}
	// $Accion = "estadosdecuenta";
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $i ) ) {
		$i = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $FrAccount ) ) {
		$FrAccount = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $Day ) ) {
		$Day = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $Month ) ) {
		$Month = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $Year ) ) {
		$Year = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $DayF ) ) {
		$DayF = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $MonthF ) ) {
		$MonthF = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $YearF ) ) {
		$YearF = "";
	}
	switch( $Access )
	{
		case "FrAc":
			$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "estadosdecuenta.php" );
			$t->setAllStrings();
			$t->set_file( array( "estadosdecuenta_tpl" => "estadosdecuenta.tpl" ) );
			$particularFields = "";
			$transaccion_buffer = "";
			$ret_code = $tr->PostToHost( $backend, "/IBnkIIS.dll", "Trxn=stm&Access=FrAc&CustID=" . urlencode( $usr ) . "&Cadpriv=" . urlencode( $priv ) . $particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer );
//06May2010   ACS  Llave ASB para Circular X I
			if ($session->variable( "r_tknOp" ) == "si" )
			{?>
				<script>

					$(document).ready(function (){
																$('#token_value').focus()
																$('form').bind('submit', function() {
																	try
																	{
																		if ($("#bandera").val() != "envia")
																		{
																			return false;
																		}

																	}
																	catch (err)
																	{
																		return false;
																	}

																}
															)
														});


				</script>
<?php

				$secure_site = $ini->read_var( "site", "SecureServer" ) . $ini->read_var( "site", "ServerNumber" ) . $ini->read_var( "site", "DomainPostfix" );

				$cliente_token="
									</tr>
								</table>
							</td>
						</tr>
						<tr><td>
						<input type='hidden' id='nomFunc' name='nomFunc' value='clienteasb'>
						<input type='hidden' id='btn_opcion' name='btn_opcion'>
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
												<input type=password name=token id=token_value class=inputbox  size=15 maxlength=10 onKeyPress=\"if (token_valido(event,this) == true){ if (is_Enter(event) == true){validaTknUser('estadosdecuenta')}}else{return false}\" ></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
								</td>
								<td align=center>
									<input type=submit name=btn_opcion id=btn_opcionA value=Aceptar class=button onmouseover=\"this.className='buttonON'\" onmouseout=\"this.className='button'\" onclick=\"if (validar_token('token_value','btn_opcionA')) { validaTknUser('estadosdecuenta');}else{return false}\" >
									<input type=button name=btn_opcion id=btn_opcionC value=Cancelar class=button onmouseover=\"this.className='buttonON'\" onmouseout=\"this.className='button'\" onclick=\"document.location.href='https://$secure_site/article/articleview/196/1/7/'\" >
								</td>
								<td align=left></td>
								<td></td>
							</tr>
						</table>
				";

					$transaccion_buffer=str_replace("<input id=\"OK\" name=\"OK\" onclick=\"javascript:{ document.getElementById( 'OK' ).disabled = true; document.forms[ 0 ].submit(); }\" type=\"SUBMIT\" value=\"Aceptar\">",$cliente_token,$transaccion_buffer);

					//Se Agrega instricción para el token
					/*
					$instruccion="<LI>Es necesario capturar su <b>Clave ASB (Acceso Seguro Bajio)</b>, la cual est&aacute; formada por 10 d&iacute;gitos (4 d&iacute;gitos de su <b>NIP</b> actual m&aacute;s 6 d&iacute;gitos de su <b>Clave Din&aacute;mica</b>), para completar la transacci&oacute;n.<LI>Presione bot&oacute;n <EM>Aceptar</EM>";
					$transaccion_buffer=str_replace("<LI>Presione bot&oacute;n <EM>Aceptar</EM>",$instruccion, $transaccion_buffer);
					$transaccion_buffer=str_replace("<li>Presione el bot&oacute;n <em>Aceptar</em>",$instruccion, $transaccion_buffer);*/
			}
//06May2010   ACS  Llave ASB para Circular X F
			$t->set_var( "transaccion_buffer", $transaccion_buffer );
			$t->pparse( "output", "estadosdecuenta_tpl" );
			$tr->blog( $qki, "EstadosDeCuenta", $particularFields, "", "", "", "" );

			break;
		case "Confirm":
			$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "estadosdecuenta.php" );
			$t->setAllStrings();
			$t->set_file( array( "estadosdecuenta_tpl" => "estadosdecuenta.tpl" ) );
			$particularFields = "&Accion=" . $Accion;
			$transaccion_buffer = "";
			$ret_code = $tr->PostToHost( $backend, "/IBnkIIS.dll", "Trxn=stm&Access=Confirm&CustID=" . urlencode( $usr ) . "&Cadpriv=" . urlencode( $priv ) . $particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer );
			//JC
			if ( $Accion != "Mensual" )
			{
			if ( $perfil == "permor" )
			{
				//$transaccion_buffer = str_replace("<rpl>","",$transaccion_buffer);
			}
			else
			{

				$postagi = strpos ($transaccion_buffer,"<!--rpl-->");
				$sub1 = substr($transaccion_buffer,0,$postagi);
				$postagf = strpos ($transaccion_buffer,"<!--rpl-->",$postagi+10);
				$sub2 = substr($transaccion_buffer,$postagf+10,strlen($transaccion_buffer));
				$transaccion_buffer = $sub1 . $sub2;
				//Buscando 2da cadena

				$postagi = strpos ($transaccion_buffer,"<!--rpl2-->");
				$sub1 = substr($transaccion_buffer,0,$postagi);
				$postagf = strpos ($transaccion_buffer,"<!--rpl2-->",$postagi+11);
				$sub2 = substr($transaccion_buffer,$postagf+11,strlen($transaccion_buffer));
				$transaccion_buffer = $sub1 . $sub2;
				$transaccion_buffer = str_replace("Formato 1","",$transaccion_buffer);
				}
			}
			//JC
			$t->set_var( "transaccion_buffer", $transaccion_buffer );
			$t->pparse( "output", "estadosdecuenta_tpl" );
			$tr->blog( $qki, "EstadosDeCuenta", $particularFields, "", "", "", "" );
			break;
		case "Process":
// JAC 03AGO2010 INI
			$eliminaServices = $ini->read_var( "site", "EliminaServices" );	// Si o No.

			if ( strtoupper( trim( $eliminaService ) ) == "SI" ) {	// La variable se encuentra definida en site.ini
				// passthru( "/bin/bash /var/www/html/bajio/mountnfs_ecc.sh", $ret_code );
				passthru( "/bin/bash /usr/bin/mountnfs_ecc.sh", $ret_code );
			if ( $ret_code != 0 )
			{
					//eZLog::writeNotice( "At estadosdecuenta: images.bb.com.mx:/var/www/images/ezfilemanager/files/ and/or services.bb.com.mx:/var/www/ecc/ are not mounted.|" );	MAOS Oct2013 Quitar Notice
					sendmail( $ini->read_var( "site", "ErrEMail1" ), "BajíoNET. Error Servidor " . $ini->read_var( "site", "ServerNumber" ), "Las Monturas en el Servidor www". $ini->read_var( "site", "ServerNumber" ) . ".bb.com.mx, No están Disponibles.\r\n\r\nFavor de volverlas a montar ejecutando \"umount -a\" y enseguida \"mount -a\"." );
					sendmail( $ini->read_var( "site", "ErrEMail2" ), "BajíoNET. Error Servidor " . $ini->read_var( "site", "ServerNumber" ), "Las Monturas en el Servidor www". $ini->read_var( "site", "ServerNumber" ) . ".bb.com.mx, No están Disponibles.\r\n\r\nFavor de volverlas a montar ejecutando \"umount -a\" y enseguida \"mount -a\"." );
					$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "estadosdecuenta.php" );
					$t->setAllStrings();
					$t->set_file( array( "estadosdecuenta_tpl" => "estadosdecuenta.tpl" ) );
					$transaccion_buffer = "<br />Su transacción no puede ser atendida por el momento.<br /><br />Favor de volver a intentar más tarde.<br /><br />";
					$transaccion_buffer .= "Si desea descargar otro Estado de Cuenta haga clic <a href=\"/transaccion/estadosdecuenta/\">aqu&iacute;</a>.";
					$t->set_var( "transaccion_buffer", $transaccion_buffer );
					$t->pparse( "output", "estadosdecuenta_tpl" );
					$tr->blog( $qki, "EstadosDeCuenta", $particularFields, "", "", "", "Mensual" );
					unset( $ret_code );
					break;
				}
				unset( $ret_code );
			} else {
				// passthru( "/bin/bash /var/www/html/bajio/mountnfs.sh", $ret_code );
				passthru( "/bin/bash /usr/bin/mountnfs.sh", $ret_code );
				if ( $ret_code != 0 )
				{
					// passthru( "/bin/bash /var/www/html/bajio/mountnfs.sh", $ret_code );
					passthru( "/bin/bash /usr/bin/mountnfs.sh", $ret_code );
					if ( $ret_code != 0 )
					{
						// passthru( "/bin/bash /var/www/html/bajio/mountnfs.sh", $ret_code );
						passthru( "/bin/bash /usr/bin/mountnfs.sh", $ret_code );
						if ( $ret_code != 0 )
						{
							//eZLog::writeNotice( "At estadosdecuenta: images.bb.com.mx:/var/www/images/ezfilemanager/files/ and/or services.bb.com.mx:/var/www/data/ are not mounted.|" );		MAOS Oct2013 Quitar Notice
							sendmail( $ini->read_var( "site", "ErrEMail1" ), "BajíoNET. Error Servidor " . $ini->read_var( "site", "ServerNumber" ), "Las Monturas en el Servidor www". $ini->read_var( "site", "ServerNumber" ) . ".bb.com.mx, No están Disponibles.\r\n\r\nFavor de volverlas a montar ejecutando \"umount -a\" y enseguida \"mount -a\"." );
							sendmail( $ini->read_var( "site", "ErrEMail2" ), "BajíoNET. Error Servidor " . $ini->read_var( "site", "ServerNumber" ), "Las Monturas en el Servidor www". $ini->read_var( "site", "ServerNumber" ) . ".bb.com.mx, No están Disponibles.\r\n\r\nFavor de volverlas a montar ejecutando \"umount -a\" y enseguida \"mount -a\"." );
							$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "estadosdecuenta.php" );
							$t->setAllStrings();
							$t->set_file( array( "estadosdecuenta_tpl" => "estadosdecuenta.tpl" ) );
							$transaccion_buffer = "<br />Su transacción no puede ser atendida por el momento.<br /><br />Favor de volver a intentar más tarde.<br /><br />";
							$transaccion_buffer .= "Si desea descargar otro Estado de Cuenta haga clic <a href=\"/transaccion/estadosdecuenta/\">aqu&iacute;</a>.";
							$t->set_var( "transaccion_buffer", $transaccion_buffer );
							$t->pparse( "output", "estadosdecuenta_tpl" );
							$tr->blog( $qki, "EstadosDeCuenta", $particularFields, "", "", "", "Mensual" );
							unset( $ret_code );
							break;
						}
					}
				}
				unset( $ret_code );
			}

			switch( $Accion )
			{
				case "Corte":
					$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "estadosdecuenta.php" );
					$t->setAllStrings();
					// JAC OCT2010 INI
					$canBreak = false;
					switch( $Reporte )
					{
						case "txt":
						// echo "voy pro el pinche reporte";
							$canBreak = true;
							$DayHoy		= date("d");	// 28 12 2008  (28 de diciembre del 2008)
							$DayLetra	= strtolower(date("D"));
							$MonthHoy	= date("m");
							$YearHoy	= date("Y");
							$FrAccount = str_replace( "brillante", "brillan", $FrAccount );
							if ( $Accion2 == "periododia" ) // Lo debe resuelve un sam exclusivo para estados de cuenta.
							{
								$Day	= $DayHoy;
								$Month	= $MonthHoy;
								$Year	= $YearHoy;

								$DayF   = $DayHoy;
								$MonthF = $MonthHoy;
								$YearF  = $YearHoy;
							}
							else
							{
								if ( $Month == "  " or $Year == "    ")	// error.. debe seleccionar al menos mes anio inicial
								{
									$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>Favor de capturar el mes y año iniciales</b>";
									$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
									$t->set_var( "transaccion_buffer", $transaccion_buffer );
									$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
									break;
								}
								else
								{
									if (
											( $DayF == "  " or $MonthF == "  " or $YearF == "    " ) and
											( $DayF.$MonthF.$YearF != "        " )
										)
									{
										$transaccion_buffer = "Transacción no realizada.<br><br><b>Favor de capturar la fecha final.</b>";
										$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
										$t->set_var( "transaccion_buffer", $transaccion_buffer );
										$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
										break;
									}

									if ($Day == "  ")
										$Day = "01";

									if ($DayF == "  ")
									{
										$DayF = ultimoDia($Month, $Year);
									}

									if ($MonthF == "  " )
										$MonthF = $Month;

									if ($YearF == "    ")
										$YearF = $Year;

									if ($YearF.$MonthF.$DayF < $Year.$Month.$Day )
									{
										$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha final debe ser mayor o igual que la inicial</b>";
										$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
										$t->set_var( "transaccion_buffer", $transaccion_buffer );
										$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
										break;
									}

									if (!(checkdate($Month,$Day,$Year)))
									{
										$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha inicial es inv&aacute;lida</b>";
										$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
										$t->set_var( "transaccion_buffer", $transaccion_buffer );
										$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
										break;
									}
									if (!(checkdate($MonthF,$DayF,$YearF)))
									{
										$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha final es inv&aacute;lida</b>";
										$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
										$t->set_var( "transaccion_buffer", $transaccion_buffer );
										$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
										break;
									}
								}
							}
							// JAC 05NOV2010 INI
							/*
							echo "
								<script language=\"JavaScript\">
								var page = '/reportespagos/';
								var w=25, h=25, bw, bh, bl, bt, topPos, leftPos;

								bw = document.documentElement.clientWidth;
								bh = document.documentElement.clientHeight;
								bl = window.screenLeft;
								bt = window.screenTop;

								leftPos = (Math.floor(bw/2)-2*w);
								topPos = (Math.floor(bh/2)-h);

								page = page + \"?type=edo_cta&user=\" + '$usr';
								page = page + \"&FrAccount=\" + '$FrAccount';
								page = page + \"&Day=\" + '$Day' + \"&Month=\" + '$Month' + \"&Year=\" + '$Year';
								page = page + \"&DayF=\" + '$DayF' + \"&MonthF=\" + '$MonthF' + \"&YearF=\" + '$YearF';

								attributes = \"width=\" + w + \",height=\" + h + \",top=\" + topPos + \",left=\" + leftPos;
								popUpWin = window.open (page, \"winName\", attributes);

								</script>";
							$transaccion_buffer = "Transacci&oacute;n realizada con &eacute;xito.<br><br>";
							$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
							$t->set_var( "transaccion_buffer", $transaccion_buffer );
							$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
							*/
							//Mensaje - Nexions HM
							$ret_code = 0;
							$trans = $trans . "<?xml version='1.0'?>";
							$trans .= "<mensaje><trxn value='stmt'/><accion value='ConsMovs'/><tipomsj value='rqs'/><CustID value='".
								$user ."'/><FrAccount value='".
								$FrAccount ."'/><Day value='".
								$Day ."'/><Month value='".
								$Month ."'/><Year value='".
								$Year ."'/><DayF value='".
								$DayF ."'/><MonthF value='".
								$MonthF ."'/><YearF value='".
								$YearF ."'/><format value='text'/></mensaje>";

							$JBoss = trim ( readParam ( "JBossHost", "site.ini" ) );
							$JBossPath = trim ( readParam( "JBossPath", "site.ini" ) );

							$Day   = date ( "d" );
							$Month = date ( "M" );
							$Year  = date ( "Y" );

							$FrAccount = str_replace ( " ", "", $FrAccount );

							$transaccion_buffer = requestHTTPtoJBoss ( $JBoss, $JBossPath, "xml", $trans );
							if ( trim ( $transaccion_buffer ) == "" )
								return "<script> alert(' 4503 - " .  $errors[4501]  . " ') </script>";

							/*$code_idx = strpos ( $transaccion_buffer, "020" );
							echo "code_idx: $code_idx<BR>";
							if ( $code_idx !== false )
							{
								$error_code = substr ( $transaccion_buffer, $code_idx, $code_idx + 4 );
								$error_desc = substr ( $transaccion_buffer, $code_idx + 6 );
								$transaccion_buffer = "<br><br><em>Hubo un error al consultar los movimientos. <BR>El c&oacute;digo de error fue <strong>";
								$transaccion_buffer .= $error_code .": </strong>" . $error_desc . "</em>";
								$ret_code = -1;
							}
							*/
							if ( $ret_code == 0) //si hay archivo generado
							{
								$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta_process.tpl") );

								$d = new eZDate( );
								$fecha = $d->year( ) . $d->addZero( $d->month( ) ) . $d->addZero ( $d->day( ) );
								$size = strlen( $transaccion_buffer );
								$FileExt = ".xls";
								$FrAccount2 = str_replace( " ", "", $FrAccount );
								$FrAccount2 = str_replace( "-", "", $FrAccount2 );
								$fileName = "edocta$FrAccount2$fecha$FileExt";
								$fileName2 = "edocta$FrAccount$fecha$FileExt";
								if ( preg_match( "/^edocta([0-9]{1,9})/", $fileName, $regs ) ) /*HB-AGL*/
								{
										$CustNo = $regs[1];
								}
								$tb = preg_replace("/\|/", "\r\n", $transaccion_buffer );    /* HB AGL*/
								$tb = preg_replace( "/\¶/", "\r\n", $tb );			 		/* HB AGL*/
								$tb = preg_replace( "/\»/", "\t", $tb );						 /* HB AGL*/
								$file = new eZFile();
								$file->dumpDataToFile( $tb, $fileName );

								$uploadedFile = new eZVirtualFile( );
								$uploadedFile->setName( $fileName );
								$uploadedFile->setDescription( $fileName2 );
								$uploadedFile->setFile( $file );
								$uploadedFile->storebb( $CustNo );

								$FileID = $uploadedFile->id();
								$FolderID = 1;
								$folder = new eZVirtualFolder( $FolderID );
								eZObjectPermission::setPermission( -1, $FileID, "filemanager_file", 'r' );
								$uploadedFile->setUser( $folder->user() );
								$uploadedFile->store();

								$folder->addFile( $uploadedFile );

								//eZLog::writeNotice( "File added to file manager from transaccion using request from IP: ".$_SERVER['REMOTE_ADDR']."" );	MAOS Oct2013 Quitar Notice

								$t->set_block( "errors_tpl", "error_write_permission", "write_permission" );
								$t->set_var( "write_permission", "&nbsp;" );

								$t->set_block( "errors_tpl", "error_upload_permission", "upload_permission" );
								$t->set_var( "upload_permission", "&nbsp;" );

								$t->set_block( "errors_tpl", "error_name_tpl", "error_name" );
								$t->set_var( "error_name", "&nbsp;" );

								$t->set_block( "errors_tpl", "error_file_upload_tpl", "error_file_upload" );
								$t->set_var( "error_file_upload", "&nbsp" );

								$t->set_block( "errors_tpl", "error_description_tpl", "error_description" );
								$t->set_var( "error_description", "&nbsp;" );

								$t->set_block( "errors_tpl", "error_read_everybody_permission_tpl", "error_read_everybody_permission" );
								$t->set_var( "error_read_everybody_permission", "&nbsp;" );

								$t->set_block( "errors_tpl", "error_write_everybody_permission_tpl", "error_write_everybody_permission" );
								$t->set_var( "error_write_everybody_permission", "&nbsp;" );


								$filename = $uploadedFile->name();
								$t->set_var( "file_id", $uploadedFile->id() );
								$t->set_var( "original_file_name_without_spaces", str_replace( " ", "%20", $filename ) );
								$t->set_var( "original_file_name", $filename );
								$t->set_var( "file_name", $filename );
								$t->set_var( "file_url", $filename );
								$t->set_var( "file_description", $uploadedFile->description() );

								$filePath = $uploadedFile->filePath( true );

								$size = $uploadedFile->siFileSize();
								$t->set_var( "file_size", $size["size-string"] );
								$t->set_var( "file_unit", $size["unit"] );

								$t->set_var( "file_read", "" );
								$t->set_var( "file_write", "" );
								$t->set_var( "td_class", ( $i % 2 ) ? "bgdark" : "bglight" );

								if ( eZObjectPermission::hasPermission( $uploadedFile->id(), "filemanager_file", "r", $user ) || eZVirtualFile::isOwner( $user ,$uploadedFile->id() ) )
								{
									$t->parse( "read", "read_tpl" );
									$i++;
								}
								else
								{
									$t->set_var( "read", "" );
								}

								$t->parse( "file", "file_tpl", true );
								$transaccion_buffer = "";
								$t->set_var( "transaccion_buffer", $transaccion_buffer );
								//ACS  - MenuContenidoAjax I
								//$t->pparse( "output", "estadosdecuenta_tpl" );
								$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
								//ACS  - MenuContenidoAjax F

							}
							else //no existe el archivo y se envia algun error
							{
								$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
								$t->set_var( "transaccion_buffer", $transaccion_buffer );
								//ACS  - MenuContenidoAjax I
								//$t->pparse( "output", "estadosdecuenta_tpl" );
								$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
								//ACS  - MenuContenidoAjax F
							}

							$transaccion_buffer=str_replace("type=\"submit\"","type=\"BUTTON\"",$transaccion_buffer);
							$transaccion_buffer=str_replace("onclick=\"javascript:{ document.getElementById( 'OK' ).disabled = true; document.forms[ 0 ].submit(); }\"","onclick=\"javascript:procesarAjax();\"",$transaccion_buffer);
							// REF JAC 05NOV2010 FIN							break;
							break;
						case "excel":
							break;
						default:
							break;
					}
					if ( $canBreak )
					{
						break;
					}
					// JAC OCT2010 FIN
					/* DGM 24Ene2005 estado de cuenta (mostrar error cuando exista)
					$t->set_file( array(
						"estadosdecuenta_tpl" => "estadosdecuenta_process.tpl"
						) );

					$particularFields = "&Month=".urlencode($Month)."&FrAccount=".urlencode($FrAccount)."&Year=".urlencode($Year)."&File=".urlencode($File);
					$tr->blog($qki,"EdosCuenta", $FrAccount, $Month, $Year, $File, $Accion); */
					// clear what might be in the output buffer and stop the buffer.
					// ob_end_clean();

					$FileExt = ".xls";
					$File = "XLS";	// DGM
					/* DGM 12Feb2008 Se eliminan por instrucciones de Merca
					switch($File) {
					case "TXT":
						// Header("Content-Type: text/csv");
						$FileExt = ".txt";
						break;
					case "QIF":
						// Header("Content-Type: text/qif");
						$FileExt = ".qif";
						break;
					case "OFC":
						// Header("Content-Type: text/ofc");
						$FileExt = ".ofc";
						break;
						case "XLS":
							// Header("Content-Type: text/csv");
							$FileExt = ".xls";
							break;
					}
					*/

					// *********************************************************************************************************************************************************************************************************************************
					// DGM I 12Feb2008 Segmentar estado de cuenta (diario-anterior)
					// Esta consultando el dia de hoy

					$DayHoy		= date("d");	// 28 12 2008  (28 de diciembre del 2008)
					$DayLetra	= strtolower(date("D"));
					$MonthHoy	= date("m");
					$YearHoy	= date("Y");

					/*$DayAyer	= date("d", mktime(0, 0, 0, date("m")  , date("d")-1, date("Y")));
					$MonthAyer	= date("m", mktime(0, 0, 0, date("m")  , date("d")-1, date("Y")));
					$YearAyer	= date("Y", mktime(0, 0, 0, date("m")  , date("d")-1, date("Y")));*/

					// ***********************************************************************
					// DGM 23Ene2006 Se agregan campos de rangos de fechas
					$particularFields = "&Accion=Corte&FrAccount=".urlencode( $FrAccount )."&Day=".urlencode( $Day )."&Month=".urlencode( $Month )."&Year=".urlencode( $Year )."&DayF=".urlencode( $DayF )."&MonthF=".urlencode( $MonthF )."&YearF=".urlencode( $YearF )."&File=".urlencode($File);
					$tr->blog( $qki, "EstadosDeCuenta", $particularFields, "", "", "", "Corte" );
					// ***********************************************************************

					$transaccion_buffer = "";

					if ( $Accion2 == "periododia" ) // Lo debe resuelve un sam exclusivo para estados de cuenta.
					{

						$Day	= $DayHoy;
						$Month	= $MonthHoy;
						$Year	= $YearHoy;

						$DayF   = $DayHoy;
						$MonthF = $MonthHoy;
						$YearF  = $YearHoy;

						$particularFields = "&Accion=Corte&FrAccount=".urlencode( $FrAccount )."&Day=".urlencode( $Day )."&Month=".urlencode( $Month )."&Year=".urlencode( $Year )."&DayF=".urlencode( $DayF )."&MonthF=".urlencode( $MonthF )."&YearF=".urlencode( $YearF )."&File=".urlencode($File);
						$tr->blog( $qki, "EstadosDeCuenta", $particularFields, "", "", "", "Corte" );

						// **********************************************************************************************************************************
						// DGM I 20Feb2008 Saldos al dia (se ejecuta un hst para ser resulto por JBOSS)

						//echo("<br>  Resolviendo [$backend] ");
						//Para JBoss
					//REF WMA-16Jun2008, Inicio. Se define nueva transaccion que luego se disfrazará como un hst
						//if ( in_array( "stm", ( preg_split( "/[,||(,\s)]+/", $ini->read_var( "site", "JBossTransactions" ) ) ) ) )
						if ( in_array( "hststm", ( preg_split( "/[,||(,\s)]+/", $ini->read_var( "site", "JBossTransactions" ) ) ) ) )
					//REF WMA-16Jun2008, Fin
						{
							//echo("<br>Resuelve JBoss con trxn = hst");
							$top	= 9999999;	//10 millones de movimientos


							// Se agrega la T para las cuentas de Plazo
							if (
									substr($FrAccount, -1) != "D"    and 	substr($FrAccount, -1) != "T"
								)
							{
								if ( strpos($FrAccount, "Plazo") > 0   )
								{
									$FrAccount .= " T";
								}
								else
								{
									$FrAccount .= " D";
								}
							}

							$FrAccount = str_replace(" ", "", $FrAccount);
							$FrAccount = str_replace("-", "", $FrAccount);
							$FrAccount = strtolower(substr($FrAccount,0, strlen($FrAccount)-1)).substr($FrAccount,-1);
							$FrAccount = str_replace( "brillante", "brillan", $FrAccount );

							$particularFields = "&FrAccount=".urlencode( $FrAccount )."&Accion=Todos&Day=".urlencode( $Day )."&Month=".urlencode( $Month )."&Year=".urlencode( $Year )."&DayF=".urlencode( $DayF )."&MonthF=".urlencode( $MonthF )."&YearF=".urlencode( $YearF )."&Pos=".urlencode( $Pos )."&FrAmount=".urlencode( $FrAmount )."&Dias=".urlencode( $Dias )."&FrNo=".urlencode( $FrNo )."&top=".urlencode( $top );
							$tr->blog( $qki,"Movimientos", $FrAccount, $Day, $Month, $Year, $Accion );

					//REF WMA-16Jun2008, Inicio
							$backend = $ini->read_var( "site", "JBossHost" ); //Se va al servidor de produccion
							//echo ("Resuelve JBoss Backend: $backend");
							//$ret_code = $tr->PostToHost( $backend, "/IBnkIIS.dll", "Trxn=hst&Access=".urlencode( $Access )."&CustID=".urlencode( $usr )."&Cadpriv=".urlencode( $priv ).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer ); // movimientos
							$ret_code = $tr->PostToHost( $backend, "/IBnkIIS.dll", "Trxn=hststm&Access=".urlencode( $Access )."&CustID=".urlencode( $usr )."&Cadpriv=".urlencode( $priv ).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer ); // movimientos
					//REF WMA-16Jun2008, Fin

							if (strpos($transaccion_buffer, "La cuenta seleccionada no tiene movimientos") > 0)
							{
								$transaccion_buffer = "<br><b>La cuenta seleccionada no tiene movimientos.<b>"; //no hay movimientos
								$ret_code = -1;
							}
							else
							{
								//echo "estadosdecuenta.php FrAccount[" . $FrAccount . "]";
								//ACS-28Ago2008 Inicio Fecha final de estado de cuenta
								//$transaccion_buffer = parserHTMLtoXLS( $transaccion_buffer, $Day."-".$Month."-".$Year, $FrAccount);
								$transaccion_buffer = parserHTMLtoXLS( $transaccion_buffer, $Day."-".$Month."-".$Year, $DayF."-".$MonthF."-".$YearF, $FrAccount);
								//ACS-28Ago2008 Inicio Fecha final de estado de cuenta
								$t->set_var( "transaccion_buffer", $transaccion_buffer );
								$t->pparse( "output", "movimientos_tpl" );
							}

						}
						else
						{

							// Si esta pidiendo un corte diario pero tiene fechas al dia de hoy se manda por respaldo
							// al servidor de respaldo entre semana de 9 a 9
							$hora = date("His");  //17
							if (
									(
										(
											$hora 		>= "090000" and
											$hora 		<= "210000"
										)
											and
										(
											 $DayLetra	!= "sat" and
											 $DayLetra	!= "sab" and
											 $DayLetra	!= "sun" and
											 $DayLetra	!= "dom"
										)
									)
									and
									(
										(
											$Day 	!= $DayHoy 		or ///Validacion extra
											$Month	!= $MonthHoy 	or
											$Year	!= $YearHoy		or
											$DayF   != $DayHoy		or
											$MonthF != $MonthHoy	or
											$YearF  != $YearHoy
										)

									)
								)

							{
								$backend = $ini->read_var( "site", "SAMStm" ); //Se va al servidor de respaldo (srvbases)
							}

							//echo ("Resuelve Backend: $backend");

							$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=stm&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // estados de cuenta
						}


						// DGM F 20Feb2008 Saldos al dia (se ejcuta un hst para ser resulto por JBOSS
						// **********************************************************************************************************************************

					}
					else //corte historico
					{

					//REF WMA-16Jun2008, Inicio
						if ( in_array( "hststm", ( preg_split( "/[,||(,\s)]+/", $ini->read_var( "site", "JBossTransactions" ) ) ) ) )
					    {
							$trn = "hststm";	//se disfraza como un hst de tipo stm para JBoss
						//REF WMA-01Aug2008, Inicio
							if (substr($FrAccount, -1) != "D"    and 	substr($FrAccount, -1) != "T")
							{
								if ( strpos($FrAccount, "Plazo") > 0   )
								{
									$FrAccount .= " T";
								}
								else
								{
									$FrAccount .= " D";
								}
							}
						//REF WMA-01Aug2008, Fin
						//REF WMA-29Jul2008, Inicio
							$FrAccount = str_replace(" ", "", $FrAccount);
							$FrAccount = str_replace("-", "", $FrAccount);
							$FrAccount = strtolower(substr($FrAccount,0, strlen($FrAccount)-1)).substr($FrAccount,-1);
							$FrAccount = str_replace( "brillante", "brillan", $FrAccount );
							$FrAccount = str_replace( "dólar", "dolar", $FrAccount );
						//REF WMA-29Jul2008, Fin
						}
						else
						{
							$trn = "stm";
						}
					//REF WMA-16Jun2008, Fin
						// Si esta pidiendo un corte historico y tiene fechas al dia de hoy se manda por respaldo
						// al servidor de respaldo entre semana de 9 a 9
						$hora = date("His");  //17
						if (
									(
										$hora 		>= "090000" and
										$hora 		<= "210000"
									)
										and
									(
										 $DayLetra	!= "sat" and
										 $DayLetra	!= "sab" and
										 $DayLetra	!= "sun" and
										 $DayLetra	!= "dom"
									)
							)
						{
						//REF WMA 16JUN2008, Inicio
							//$backend = $ini->read_var( "site", "SAMStm" ); //Se va al servidor de respaldo (srvbases)
							if ($trn == "hststm")
							{
								/*echo "X Datos: [" .$Day."][".$Month."][".$Year."][".$DayF."][".$MonthF."][".$YearF."]";
								echo strlen($Month);*/

								// ************************************************************************************************************************
								// DGM I 03Jul2008 Validacion de captura de Rango de Fechas en corte anterior
								if ( $Month == "  " or $Year == "    ")	// error.. debe seleccionar al menos mes anio inicial
								{
									//echo "A Datos: [" .$Day."][".$Month."][".$Year."][".$DayF."][".$MonthF."][".$YearF."]";
									$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>Favor de capturar el mes y año iniciales</b>";
									$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
									$t->set_var( "transaccion_buffer", $transaccion_buffer );
									$t->pparse( "output", "estadosdecuenta_tpl" );
									break;

								}
								else
								{
									// Si esta presente al menos un dato en la fecha final, deben de estar todos capturados
									if (
											( $DayF == "  " or $MonthF == "  " or $YearF == "    " ) and
											( $DayF.$MonthF.$YearF != "        " )
										)
									{
										$transaccion_buffer = "Transacción no realizada.<br><br><b>Favor de capturar la fecha final.</b>";
										$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
										$t->set_var( "transaccion_buffer", $transaccion_buffer );
										$t->pparse( "output", "estadosdecuenta_tpl" );
										break;
									}


									if ($Day == "  ")
										$Day = "01";

									if ($DayF == "  ")
									{
										$DayF = ultimoDia($Month, $Year);
									}

									if ($MonthF == "  " )
										$MonthF = $Month;

									if ($YearF == "    ")
										$YearF = $Year;

									//echo ("|".$YearF."|".$MonthF."|".$DayF. " < ".$Year.$Month.$Day. "??" );

									if ($YearF.$MonthF.$DayF < $Year.$Month.$Day )
									{
										$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha final debe ser mayor o igual que la inicial</b>";
										$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
										$t->set_var( "transaccion_buffer", $transaccion_buffer );
										$t->pparse( "output", "estadosdecuenta_tpl" );
										break;
									}

									//REF WMA-13aug2008, Inicio. Nueva validación para día bien formado
									if (!(checkdate($Month,$Day,$Year)))
									{
										$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha inicial es inv&aacute;lida</b>";
										$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
										$t->set_var( "transaccion_buffer", $transaccion_buffer );
										$t->pparse( "output", "estadosdecuenta_tpl" );
										break;
									}
									if (!(checkdate($MonthF,$DayF,$YearF)))
									{
										$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha final es inv&aacute;lida</b>";
										$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
										$t->set_var( "transaccion_buffer", $transaccion_buffer );
										$t->pparse( "output", "estadosdecuenta_tpl" );
										break;
									}
									//REF WMA-13aug2008, Fin
								}
								// DGM I 03Jul2008 Validacion de captura de Rango de Fechas en corte anterior
								// ************************************************************************************************************************


								$backend = $ini->read_var( "site", "JBossStm" ); //Se va al servidor de respaldo (srvbases)
								$top	= 9999999;	//10 millones de movimientos
								$particularFields = "&FrAccount=".urlencode( $FrAccount )."&Accion=Todos&Day=".urlencode( $Day )."&Month=".urlencode( $Month )."&Year=".urlencode( $Year )."&DayF=".urlencode( $DayF )."&MonthF=".urlencode( $MonthF )."&YearF=".urlencode( $YearF )."&Pos=".urlencode( $Pos )."&FrAmount=".urlencode( $FrAmount )."&Dias=".urlencode( $Dias )."&FrNo=".urlencode( $FrNo )."&top=".urlencode( $top );
							}
							else
							{
								$backend = $ini->read_var( "site", "SAMStm" ); //Se va al servidor de respaldo (srvbases)
							}
						}
						else
						{
							if ($trn == "hststm")
							{
								$backend = $ini->read_var( "site", "JBossHost" ); //Se va al servidor de respaldo (srvbases)
								$top	= 9999999;	//10 millones de movimientos
								$particularFields = "&FrAccount=".urlencode( $FrAccount )."&Accion=Todos&Day=".urlencode( $Day )."&Month=".urlencode( $Month )."&Year=".urlencode( $Year )."&DayF=".urlencode( $DayF )."&MonthF=".urlencode( $MonthF )."&YearF=".urlencode( $YearF )."&Pos=".urlencode( $Pos )."&FrAmount=".urlencode( $FrAmount )."&Dias=".urlencode( $Dias )."&FrNo=".urlencode( $FrNo )."&top=".urlencode( $top );
							}
						}
						//REF WMA 16JUN2008, Fin

						//echo ("Resuelve Backend: $backend");
					//REF WMA-16Jun2008, Inicio
							//$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=stm&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // estados de cuenta

							//echo "Datos: [" .$Day."][".$Month."][".$Year."][".$DayF."][".$MonthF."][".$YearF."]";

							$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=".$trn."&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // estados de cuenta


							if ($trn == "hststm") //solo transforma el HTML si fue a JBoss
							{
								if (strpos($transaccion_buffer, "La cuenta seleccionada no tiene movimientos") > 0)
								{
									$transaccion_buffer = "<br><b>La cuenta seleccionada no tiene movimientos.<b>"; //no hay movimientos
									$ret_code = -1;
								}
								else
								{
									//echo "estadosdecuenta.php FrAccount[" . $FrAccount . "]";
									//ACS-28Ago2008 Inicio Fecha final de estado de cuenta
									//$transaccion_buffer = parserHTMLtoXLS( $transaccion_buffer, $Day."-".$Month."-".$Year,  $FrAccount);
									$transaccion_buffer = parserHTMLtoXLS( $transaccion_buffer, $Day."-".$Month."-".$Year, $DayF."-".$MonthF."-".$YearF, $FrAccount);
									//ACS-28Ago2008 Fin Fecha final de estado de cuenta
									$t->set_var( "transaccion_buffer", $transaccion_buffer );
									$t->pparse( "output", "movimientos_tpl" );
								}
							}
					//REF WMA-16Jun2008, Fin
					}


					//echo("servidor buscando: $backend");
					// DGM F 12Feb2008 Segmentar estado de cuenta (diario-anterior)
					// *********************************************************************************************************************************************************************************************************************************


					//echo ("Particular Fields: [$particularFields] ret_code = [$ret_code]");


					// DGM 24Ene2005 Estado de cuenta con nuevas columnas
					//$tr->blog($qki,"estadosdecuenta", $FrAccount, $Day, $Month, $Year, $Accion);
					$tr->blog( $qki, "EstadosDeCuenta", $particularFields, "", "", "", "Corte" );

					// *********************************************************************************************
					// DGM I Estados de Cuenta con columnas nuevas y mostrando pagina de error
					if ( $ret_code == 0) //si hay archivo generado
					{
						$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta_process.tpl") );

						$d = new eZDate( );
						$fecha = $d->year( ) . $d->addZero( $d->month( ) ) . $d->addZero ( $d->day( ) );
						$size = strlen( $transaccion_buffer );
                        $FrAccount2 = str_replace( " ", "", $FrAccount );
                        $FrAccount2 = str_replace( "-", "", $FrAccount2 );
                        $fileName = "edocta$FrAccount2$fecha$FileExt";
                        $fileName2 = "edocta$FrAccount$fecha$FileExt";
                        if ( preg_match( "/^edocta([0-9]{1,9})/", $fileName, $regs ) ) /*HB-AGL*/
                        {
                                $CustNo = $regs[1];
                        }
                        //echo "[" . $CustNo . "]";
                        $tb = preg_replace("/\|/", "\r\n", $transaccion_buffer );  /*HB AGL*/
                        $tb = preg_replace( "/\¶/", "\r\n", $tb );				 /*HB AGL*/
                        $tb = preg_replace( "/\»/", "\t", $tb );					 /*HB AGL*/

                        $file = new eZFile();
                        $file->dumpDataToFile( $tb, $fileName );

                        $uploadedFile = new eZVirtualFile( );
                        $uploadedFile->setName( $fileName );
                        $uploadedFile->setDescription( $fileName2 );
                        $uploadedFile->setFile( $file );
                        $uploadedFile->storebb( $CustNo );


						$FileID = $uploadedFile->id();
						$FolderID = 1;
						$folder = new eZVirtualFolder( $FolderID );
						eZObjectPermission::setPermission( -1, $FileID, "filemanager_file", 'r' );
						$uploadedFile->setUser( $folder->user() );
						$uploadedFile->store();
						$folder->addFile( $uploadedFile );


						//eZLog::writeNotice( "File added to file manager from transaccion using request from IP: ".$_SERVER['REMOTE_ADDR'] );	MAOS Oct2013 Quitar Notice

						$t->set_block( "errors_tpl", "error_write_permission", "write_permission" );
						$t->set_var( "write_permission", "&nbsp;" );

						$t->set_block( "errors_tpl", "error_upload_permission", "upload_permission" );
						$t->set_var( "upload_permission", "&nbsp;" );

						$t->set_block( "errors_tpl", "error_name_tpl", "error_name" );
						$t->set_var( "error_name", "&nbsp;" );

						$t->set_block( "errors_tpl", "error_file_upload_tpl", "error_file_upload" );
						$t->set_var( "error_file_upload", "&nbsp" );

						$t->set_block( "errors_tpl", "error_description_tpl", "error_description" );
						$t->set_var( "error_description", "&nbsp;" );

						$t->set_block( "errors_tpl", "error_read_everybody_permission_tpl", "error_read_everybody_permission" );
						$t->set_var( "error_read_everybody_permission", "&nbsp;" );

						$t->set_block( "errors_tpl", "error_write_everybody_permission_tpl", "error_write_everybody_permission" );
						$t->set_var( "error_write_everybody_permission", "&nbsp;" );


						$filename = $uploadedFile->name();
						$t->set_var( "file_id", $uploadedFile->id() );
						$t->set_var( "original_file_name_without_spaces", str_replace( " ", "%20", $filename ) );
						$t->set_var( "original_file_name", $filename );
						$t->set_var( "file_name", $filename );
						$t->set_var( "file_url", $filename );
						$t->set_var( "file_description", $uploadedFile->description() );

						$filePath = $uploadedFile->filePath( true );

						$size = $uploadedFile->siFileSize();
						$t->set_var( "file_size", $size["size-string"] );
						$t->set_var( "file_unit", $size["unit"] );

						$t->set_var( "file_read", "" );
						$t->set_var( "file_write", "" );
						$t->set_var( "td_class", ( $i % 2 ) ? "bgdark" : "bglight" );

						if ( eZObjectPermission::hasPermission( $uploadedFile->id(), "filemanager_file", "r", $user ) || eZVirtualFile::isOwner( $user ,$uploadedFile->id() ) )
						{
							$t->parse( "read", "read_tpl" );
							$i++;
						}
						else
						{
							$t->set_var( "read", "" );
						}

						$t->parse( "file", "file_tpl", true );
						$transaccion_buffer = "";
						$t->set_var( "transaccion_buffer", $transaccion_buffer );
						$t->pparse( "output", "estadosdecuenta_tpl" );

					}
					else //no existe el archivo y se envia algun error
					{
						$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
						$t->set_var( "transaccion_buffer", $transaccion_buffer );
						$t->pparse( "output", "estadosdecuenta_tpl" );
					}

					// DGM F Estados de Cuenta con columnas nuevas y mostrando pagina de error
					// *********************************************************************************************
					break;
				case "Mensual":
					$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "estadosdecuenta.php" );
					$t->setAllStrings();

					$FrAccount = trim( $FrAccount );
					$particularFields = "&Accion=Mensual&FrAccount=" . urlencode( $FrAccount ) . "&Month=".urlencode( $Month )."&Year=" . urlencode( $Year );
					$month = array( "", "enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre" );

					if
						(
							//	file_exists( "/var/www/data/eec/" . $Year . "/" . $Month . "/" . $Year . $Month . "_" . $FrAccount . ".pdf" )
							//&&	is_readable( "/var/www/data/eec/" . $Year . "/" . $Month . "/" . $Year . $Month . "_" . $FrAccount . ".pdf" )
								file_exists( "/var/www/repo_eec/" . $Year . "/" . $Month . "/" . $Year . $Month . "_" . $FrAccount . ".pdf" )
							&&  is_readable( "/var/www/repo_ecc/" . $Year . "/" . $Month . "/" . $Year . $Month . "_" . $FrAccount . ".pdf" )
						)
					{

						$t->set_file( array( "estadosdecuenta_tpl" => "estadosdecuenta_process.tpl" ) );

						$file = new eZFile();
						//$file->TmpFileName = "/var/www/data/eec/" . $Year . "/" . $Month . "/" . $Year . $Month . "_" . $FrAccount . ".pdf";
						$file->TmpFileName = "/var/www/repo_eec/" . $Year . "/" . $Month . "/" . $Year . $Month . "_" . $FrAccount . ".pdf";
						$file->FileName = $Year . $Month . "_" . $FrAccount .".pdf";

						$uploadedFile = new eZVirtualFile();
						$uploadedFile->setName( $file->FileName );
						$uploadedFile->setDescription( $file->FileName );
						$uploadedFile->setFile( $file );
						//LVPR I Mejora para entregar el estado de cuenta al cliente
						//$uploadedFile->store();
						$uploadedFile->storebb( $FrAccount );
						//LVPR I Mejora para entregar el estado de cuenta al cliente

						$size = $uploadedFile->siFileSize();
						$t->set_var( "file_id", $uploadedFile->id() );
						$t->set_var( "file_description", "Estado de Cuenta de " . $month[ intval( $Month ) ] ." de " . $Year );
						$t->set_var( "file_size", $size["size-string"] );
						$t->set_var( "file_unit", $size["unit"] );
						$t->set_var( "original_file_name", $file->FileName );
						$t->set_var( "original_file_name_without_spaces", $file->FileName );

						$folder = new eZVirtualFolder( 1 );
						eZObjectPermission::setPermission( -1, $uploadedFile->id(), "filemanager_file", "r" );
						$uploadedFile->setUser( $folder->user() );
						$uploadedFile->store();
						$folder->addFile( $uploadedFile );

						$t->parse( "file", "file_tpl", true );
						$transaccion_buffer = "";
						$t->set_var( "transaccion_buffer", $transaccion_buffer );
						$t->pparse( "output", "estadosdecuenta_tpl" );
					}
					else
					{
						$t->set_file( array( "estadosdecuenta_tpl" => "estadosdecuenta.tpl" ) );
						if ( ( $Year . $Month ) < ( date( "Y", ( time() - ( 60 * 60 * 24 * 30.4 * 15 ) ) ) . date( "m", ( time() - ( 60 * 60 * 24 * 30.4 * 15 ) ) ) ) )
						{
							$transaccion_buffer = "<p>No existe el Estado de Cuenta correspondiente al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>.</p><p>Recuerde que s&oacute;lo puede obtener Estados de Cuenta Mensuales de 15 meses atr&aacute;s.</p>";
						}
						elseif
							(
									( $Year . $Month ) == ( date( "Y" ) . date( "m" ) )
								or	( $Year . $Month ) <= ( date( "Y", ( time() - ( 60 * 60 * 24 * 5 ) ) ) . date( "m", ( time() - ( 60 * 60 * 24 * 5 ) ) ) )
							)
						{
							$transaccion_buffer = "<p>No existe el Estado de Cuenta correspondiente al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>.</p><p>Favor de volverlo a intentar m&aacute;s tarde.</p>";
							//eZLog::writeNotice( "At estadosdecuenta: Statement services.bb.com.mx/var/www/data/eec/" . $Year . "/" . $Month . "/" . $Year . $Month . "_" . $FrAccount . ".pdf" . " Not Found.|" );	MAOS Oct2013 Quitar Notice
							sendmail( $ini->read_var( "site", "ErrEMail1" ), "BajíoNET. Estados de Cuenta Mensuales.", "El Estado de Cuenta services.bb.com.mx/var/www/data/eec/" . $Year . "/" . $Month . "/" . $Year . $Month . "_" . $FrAccount . ".pdf" . " No Existe. Favor de verificarlo.\r\n\r\nSi es así, favor de gestionar las acciones necesarias para dejarlo displonible." );
							sendmail( $ini->read_var( "site", "ErrEMail2" ), "BajíoNET. Estados de Cuenta Mensuales.", "El Estado de Cuenta services.bb.com.mx/var/www/data/eec/" . $Year . "/" . $Month . "/" . $Year . $Month . "_" . $FrAccount . ".pdf" . " No Existe. Favor de verificarlo.\r\n\r\nSi es así, favor de gestionar las acciones necesarias para dejarlo displonible." );
						}
						else
						{
							$transaccion_buffer = "<p>A&uacute;n no ha sido generado el Estado de Cuenta correspondiente al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>.</p><p>Favor de volverlo a intentar 5 d&iacute;as despu&eacute;s del d&iacute;a de corte.</p>";
						}
						$transaccion_buffer .= "Si desea descargar otro Estado de Cuenta haga clic <a href=\"/transaccion/estadosdecuenta/\">aqu&iacute;</a>.";
						$t->set_var( "transaccion_buffer", $transaccion_buffer );
						$t->pparse( "output", "estadosdecuenta_tpl" );

					}
					//$transaccion_buffer = "";
					//$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=stm&Access=" . urlencode( $Access ) . "&CustID=" . urlencode( $usr ) . "&Cadpriv=" . urlencode( $priv ) . "&Action=JustLog" . $particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer );
					$tr->blog( $qki, "EstadosDeCuenta", $particularFields, "", "", "", "Mensual" );
					break;
			}
	}
}
else
{
	$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "userlogin.php" );
	$t->setAllStrings();
	$t->set_file( array( "user_login_tpl" => "userlogin.tpl" ) );
	$t->set_var( "redirect_url", "/transaccion/estadosdecuenta/" );
	$t->pparse( "output", "user_login_tpl" );
}
?>