<?php
//
//
// Created on: <1-Nov-2001 17:37:53 smb>
//
// This source file is part of IVA.
//
// Copyright (C) 1997-2004 Internet de Alta Calidad, S.A. de C.V.  Todos los der//echos reservados. All rights reserved.
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
include_once( "eztsys/classes/eztsys.php" );

$user =& eZUserBB::currentUser();

// DebugBreak();

function Muestra_Error( $titulo_pagina, $Mensage_Error, $Codigo_Error )
{
	$html_de_salida = "
	<TABLE width=\"100%\" border=0 cellPadding=0 cellSpacing=0>
	  <TBODY>
		<TR>
		  <TD vAlign=top width=\"100%\"><FONT face=\"Verdana, Arial, Helvetica, sans-serif\" color=#006699 size=+2>&nbsp;</FONT>
			<TABLE cellSpacing=0 cellPadding=1 width=\"100%\" border=0>
			  <TBODY>
				<TR>
				  <TD><FONT face=\"Verdana, Arial, Helvetica, sans-serif\" color=#f70000 size=1><B><STRONG>$titulo_pagina</STRONG></B></FONT></TD>
				  <TD><DIV align=right><IMG src=\"/images/membrete.jpg\" height=53  width=390></DIV></TD>
				</TR>
			  </TBODY>
			</TABLE>
		  </TD>
		</TR>
		<TR>
		  <TD width=\"100%\">
			<TABLE cellSpacing=0 cellPadding=0 width=\"100%\" border=0>
			  <TBODY>
				<TR>
				  <TD align=left bgColor=#ffffff>&nbsp;</TD>
					<TD align=right color=\"#006699\"><DIV align=right><P><FONT face=\"Verdana, Arial, Helvetica, sans-serif\" size=-1><B>" . $titulo_pagina . "<BR>VISA INTERNACIONAL<BR><IMG height=22 src=\"/images/visa.gif\" width=36><BR></B></FONT><FONT color=#ff9900 size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><FONT color=#000000 size=1></DIV></TD>
				</TR>
			  </TBODY>
			</TABLE>
		  </TD>
		</TR>
	  </TBODY>
	</TABLE>
	";
	$html_de_salida .= "<table cellspacing=0 cellpadding=0 width=\"70%\" align=center border=0>
	<tbody>
	<tr>
	  <td bgcolor=white colspan=2></td>
	</tr>
	<tr>
	  <td bgcolor=white colspan=2></td>
	</tr>
	<tr>
	  <td bgcolor=white colspan=2></td>
	</tr>
	<tr>
	  <td bgcolor=white colspan=2></td>
	</tr>
	<tr bgcolor=white>
	  <td colspan=2>
	  <font face=\"Verdana, Arial, Helvetica, sans-serif\" color=black size=-1>
			<B>Consulta de Informaci&oacute;n no disponible</B><BR>" .
	        "(" . $Codigo_Error . ") " . $Mensage_Error .
	 " </font></td>
	  <td></td>
	</tr>
	<tr>
	  <td bgcolor=white colspan=2></td>
	</tr>
	<tr>
	  <td bgcolor=white colspan=2></td>
	</tr>
	<tr>
	  <td bgcolor=white colspan=2></td>
	</tr>";
	return $html_de_salida;
}

function GeneraMovtos( &$QryTDC, &$html_de_salida )
{
	$nl_NoRenglon = 0;
	do
	{
		if ( $nl_NoRenglon % 2 ) { $cl_BGColor  = "ffffff"; $cl_BGColor2 = "ffeecc"; }
							else { $cl_BGColor  = "flflfl"; $cl_BGColor2 = "ffffff"; }
		$nl_NoRenglon ++;

		$html_de_salida .=
		   "<tr>
			  <td align=left bgcolor=".$cl_BGColor."><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=-1><b>" . $QryTDC->GetTransRegFormat( "F_Compra" ) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$QryTDC->GetTransReg( "Descr" )." " . $QryTDC->GetTransReg( "Descr2" ) . " </b><br></font></td>
			  <td align=right bgcolor=".$cl_BGColor."><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=-1>" . $QryTDC->GetTransRegFormat( "Importe" ) . "</font></td>
			</tr>
			<tr>
			  <td colspan=2 align=left valign=\"top\" bgcolor=".$cl_BGColor."><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=-2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $QryTDC->GetTransRegFormat( "F_Corte" ) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $QryTDC->GetTransReg( "Referencia" ) . "<br> </font></td>
			</tr>
			<tr>
			  <td bgcolor=black colspan=2></td>
			</tr>
			<tr>
			  <td bgcolor=".$cl_BGColor2." colspan=2></td>
			</tr>";
	}while( $QryTDC->GetTransNext( ) );

	$QryTDC->GetTransEnd( );
}

function FinMovtos( &$html_de_salida )
{
	$html_de_salida .= "<tr>
	  <td bgcolor=black colspan=2></td>
	</tr>
	</tbody>
	</table>";
}

if ( $user )
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "saldosTC.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "saldosTC_tpl" => "saldosTC.tpl"
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
    $ret_code = 0;
    $sna_ret_code = 0;

    if(empty($Access)) {
        $Access = "FrAc";
    }
    switch($Access) {
    case "FrAc":
        $Accion  = "MenuTar";
        $DiasPzo = "";   // Cadena con lista de tarjetas p actualizar en ovation char de 150
        $particularFields = "&Accion=".urlencode($Accion);
        $tr->blog($qki,"TarjetasCto", $FrAccount, $ToAccount, $DiasPzo, $Amount, $Accion);
        break;
    case "Confirm":
    case "Process":
        if( $Button == "Alta Tarjeta" ) {
            $Accion = "NewCard";
        }
        if( $Button == "Aceptar" ) {
            $Accion = $Operacion; // Para cachar el radio button.
         }
        break;
    }
    $transaccion_buffer = "";
    // $Accion = "Movtos";
    // $Accion = "EdoCta";
    // $Month = "02";
    // $Accion = "NewCard";
    // $Accion = "NewCard";
    // $Accion = "Saldos";
    // $Access = "Process";
    // $Access = "EdoCta2Meses";
    // $Access = "EdoCta3Meses";
    // $Access = "MovtosFecha";
    // $Access = "UltimoEstadoCta";

    //echo "<em>Accion</em> "  . var_dump ( $Accion ) . "<br>\n";
    //echo "<em>RFC</em> "  . var_dump ( $RFC ) . "<br>\n";
    $rfc = $RFC;
    $pan = $PAN;
    $tarjeta = "";
    $tipo = "";
    $nombre = "";
    $html_de_salida = "";
    $hubo_error = 0;
    $sna_call_ret_code = 0;
    $sna_ret_code = 0;
    $sna_ver_code = 0;
    list( $tarjeta, $tipo, $nombre ) = explode( "-", $pan);

   switch($Accion) {
    case "MenuTar":     // Menu, nada que hacer, lo resuelve Ovation.
        break;

    case "SalPtos":     // AGG FUNCIONADLIAD PUNTOS saldo de tarjeta puntos
        $particularFields = "&PAN=".urlencode($tarjeta)."&Access=".urlencode($Access);
        break;

    case "Saldos":     // Saldos
		$titulo_pagina = "Consulta de Saldo";
        //$AccionSNA = "ConsultaSaldo";
        //echo "saldosTC.php - Tipo de Cuenta [" . $tipo . "]";
		$QryTDC = new eZTSys( );
		$QryTDC->SetTarjeta( $tarjeta );		//Guarda el No de TDC que se esta consultando, la original
        if ( $tipo != "ICA" )
        {
            $tarjeta = trim($Apocope);	//Toma el del consolidado
        }
        // - Realizar la transaccion con eZTSys
        //echo "saldosTC.php - Voy a llamar eZTSys() con Account Number [$tarjeta]";
		$QryTDC->GetBase( $tarjeta );
        //echo "saldosTC.php - Ya debi traerme datos";
		if ( $QryTDC->GetBaseReg( "Acct_Num") == $tarjeta )
		{
			$QryTDC->GetHistory( "UltimoEstadoCta" );
			if ( $QryTDC->GetHistoryReg( "Acct_Num" ) != $tarjeta )
			{
				$html_de_salida = Muestra_Error( "Reporte de " . $Accion, "Ultimo registro de Estado de Cuenta no encontrado.", 7201 );
				break;
			}

			$LimiteCredito = $QryTDC->GetBaseRegFormat( "Limite" );
			$CreditoDisponible = $QryTDC->GetBaseRegFormat( "Disponible" );
			if ( $QryTDC->GetBaseReg( "VIP" ) == 1 )
			{
				$CreditoDisponible = "V.I.P.";
			}

			$html_de_salida = "
			<TABLE width=\"90%\" border=0 cellPadding=0 cellSpacing=0>
			  <TBODY>
				<TR>
				  <TD vAlign=top width=\"100%\"><FONT face=\"Verdana, Arial, Helvetica, sans-serif\" color=#006699 size=+2>&nbsp;</FONT>
					<TABLE cellSpacing=0 cellPadding=1 width=\"100%\" border=0>
					  <TBODY>
						<TR>
						  <TD><FONT face=\"Verdana, Arial, Helvetica, sans-serif\" color=#f70000 size=1><B><STRONG>$titulo_pagina</STRONG></B></FONT></TD>
						  <TD><DIV align=right><IMG height=53 src=\"/images/membrete.jpg\" width=390></DIV></TD>
						</TR>
					  </TBODY>
					</TABLE></TD>
				</TR>
				<TR>
				  <TD width=\"90%\"> <TABLE cellSpacing=0 cellPadding=0 width=\"100%\" border=0>
					  <TBODY>
						<TR>
						  <TD align=left bgColor=#ffffff>&nbsp;</TD>
						  <TD align=right color=\"#006699\"><DIV align=right><P><FONT face=\"Verdana, Arial, Helvetica, sans-serif\" size=-1><B>$titulo_pagina<BR>VISA INTERNACIONAL<BR><IMG height=22 src=\"/images/visa.gif\" width=36><BR></B></FONT><FONT color=#ff9900 size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><FONT color=#000000 size=1>
						  		<FONT face=\"Verdana, Arial, Helvetica, sans-serif\" color=#000000 size=1>Favor de pagar antes de</FONT></FONT><BR><FONT color=#000000 size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\"><B>". $QryTDC->GetBaseRegFormat( "F_Lim_Pago" ) ."</B></FONT></P>
						  		<P>&nbsp;</P></DIV></TD>
						</TR>
					  </TBODY>
					</TABLE>
				  </TD>
				</TR>
				<TR>
				  <TD width=\"100%\"> <TABLE cellSpacing=0 cellPadding=0 width=\"100%\" border=0>
					  <TBODY>
						<TR>
						  <TD bgColor=black colSpan=3> <DIV align=right>
							  <TABLE cellSpacing=0 cellPadding=1 width=\"100%\" border=0>
								<TBODY>
								  <TR>
									<TD width=\"50%\"><FONT face=\"Verdana, Arial, Helvetica, sans-serif\" color=#ffffff size=-1><b>". $QryTDC->GetBaseReg( "Nombre" ) ."</b></FONT></TD>
									<TD width=\"50%\"><DIV align=right><FONT face=\"Verdana, Arial, Helvetica, sans-serif\" color=#ffffff size=-1><B>Tarjetahabiente desde: ". $QryTDC->GetBaseRegFormat( "F_Alta" ) ."</B></FONT></DIV></TD>
								  </TR>
								</TBODY>
							  </TABLE>
							</DIV>
						  </TD>
						</TR>
						<TR>
						  <TD bgColor=black colSpan=3></TD>
						</TR>
					  </TBODY>
					</TABLE>
					<BR>
					<TABLE cellSpacing=0 cellPadding=1 width=\"100%\" border=0>
					  <TBODY>
						<TR>
						  <TD width=\"45%\" bgColor=#5a419c> <DIV align=center><FONT color=#ffffff size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><B>Información de la cuenta</B></FONT></DIV></TD>
						</TR>
						<TR>
						  <TD width=\"46%\"> <TABLE cellSpacing=0 cellPadding=1 width=\"100%\" border=0>
							  <TBODY>
								<TR bgColor=#f1f1f1>
								  <TD><B><FONT face=\"Verdana, Arial, Helvetica, sans-serif\" size=2>N&uacute;mero de cuenta</FONT></B></TD>
								  <TD><DIV align=right><FONT size=2 face=\"Verdana, Arial, Helvetica, sans-serif\"><B>XXXX-XXXX-XXXX-". $QryTDC->GetTarjeta( "L4" ) ."</B></FONT></DIV></TD>
								</TR>
								<TR>
								  <TD bgColor=black colSpan=4></TD>
								</TR>
								<TR>
								  <TD><B><FONT face=\"Verdana, Arial, Helvetica, sans-serif\" size=2>L&iacute;mite de cr&eacute;dito</FONT></B></TD>
								  <TD><DIV align=right><FONT size=2 face=\"Verdana, Arial, Helvetica, sans-serif\"><B>". $LimiteCredito ."</B></FONT></DIV></TD>
								</TR>
								<TR>
								  <TD bgColor=black colSpan=4></TD>
								</TR>
								<TR bgColor=#f1f1f1>
								  <TD><B><FONT face=\"Verdana, Arial, Helvetica, sans-serif\" size=2>Cr&eacute;dito disponible</FONT></B></TD>
								  <TD><DIV align=right><FONT size=2 face=\"Verdana, Arial, Helvetica, sans-serif\"><B>". $CreditoDisponible ."</B></FONT></DIV></TD>
								</TR>
								<TR>
								  <TD bgColor=black colSpan=4></TD>
								</TR>
								<TR>
								  <TD><B><FONT face=\"Verdana, Arial, Helvetica, sans-serif\" size=2>Fecha de corte</FONT></B></TD>
								  <TD><DIV align=right><FONT size=2 face=\"Verdana, Arial, Helvetica, sans-serif\"><B>". $QryTDC->GetBaseRegFormat( "F_Corte" ) ."</B></FONT></DIV></TD>
								</TR>
								<TR>
								  <TD bgColor=black colSpan=4></TD>
								</TR>
								<TR bgColor=#f1f1f1>
								  <TD><B><FONT face=\"Verdana, Arial, Helvetica, sans-serif\" size=2>Favor de pagar antes de</FONT></B></TD>
								  <TD><DIV align=right><FONT size=2 face=\"Verdana, Arial, Helvetica, sans-serif\"><B>". $QryTDC->GetBaseRegFormat( "F_Lim_Pago" ) ."</B></FONT></DIV></TD>
								</TR>
								<TR>
								  <TD bgColor=black colSpan=4></TD>
								</TR>
								<TR>
								  <TD><B><FONT face=\"Verdana, Arial, Helvetica, sans-serif\" size=2>Saldo</FONT></B></TD>
								  <TD><div align=right><FONT size=2 face=\"Verdana, Arial, Helvetica, sans-serif\"><B>". $QryTDC->GetBaseRegFormat( "Saldo" ) ."</B></FONT></div></TD>
								</TR>
								<TR>
								  <TD bgColor=black colSpan=4></TD>
								</TR>
								<TR bgColor=#f1f1f1>
								  <TD><B><FONT face=\"Verdana, Arial, Helvetica, sans-serif\" size=2>Pago m&iacute;nimo</FONT></B></TD>
								  <TD><DIV align=right><FONT size=2 face=\"Verdana, Arial, Helvetica, sans-serif\"><B>". $QryTDC->GetHistoryRegFormat( "Pago_Minimo" ) ."</B></FONT></DIV></TD>
								</TR>
								<TR>
								  <TD bgColor=black colSpan=4></TD>
								</TR>
							  </TBODY>
							</TABLE></TD>
						</TR>
					  </TBODY>
					</TABLE>
					<BR>
					</TD>
				</TR>
			  </TBODY>
			</TABLE>
			";
		}
		else
		{
			$html_de_salida = Muestra_Error( "Consulta de " . $Accion, "Registro Base no encontrado.", 7202 );
		}
        break;

    case "NewCard":     // Afiliacion
        // - Definir la transaccion a realizar
        //$AccionSNA = "Afiliacion";
        // - Definir los datos a enviar
        $data_to_send = $tarjeta; // Solo mando el numero de tarjeta, yo, de regreso, tengo que comparar con el rfc.

		// ************************************************************************************************************************************************************************************
		// DGM I 11Sep2007 Proyecto reemplazo comm server

	// ****************************************************************
	// DGM 11Sep2007 Proyecto reemplazo CommServer
	$TSysDB		= $ini->read_var( "site", "TSysDB" );
	$TSysUsr	= $ini->read_var( "site", "TSysUsr" );
	$TSysPwd	= $ini->read_var( "site", "TSysPwd" );
	// DGM 11Sep2007 Proyecto reemplazo CommServer
	// ****************************************************************
		if ( strlen($tarjeta) != 16 )
		{
			$html_de_salida  = "<BR> Su transacci&oacute;n no puede ser procesada.<BR><BR>";
			$html_de_salida .= "<BR> La longitud del n&uacute;mero de la Tarjeta de Cr&eacute;dito debe ser de 16 d&iacute;gitos. <BR><BR>";
			$html_de_salida .= "<P ALIGN=CENTER><A HREF=/transaccion/saldosTC/>Regresar</A></P>";
            $hubo_error = 1;
            break;
		}
		// $enlace = mysqli_connect('sql1.bb.com.mx', $TSysUsr, $TSysPwd) or die('Su transacci&oacute;n no puede ser procesada. Favor de intentar más tarde');
		$enlace = mysqli_connect('sql1.bb.com.mx', $TSysUsr, $TSysPwd);
		if ( !$enlace)	//No hay conexion con el servidor mysql
		{
			$html_de_salida  = "<BR> Su transacci&oacute;n no puede ser procesada.<BR><BR>";
			$html_de_salida .= "<BR> Favor de intentar en unos momentos. (Error 5000) <BR><BR>";
			$html_de_salida .= "<P ALIGN=CENTER><A HREF=/transaccion/saldosTC/>Regresar</A></P>";
            $hubo_error = 1;
            break;
		}

		//echo 'Conexi&oacute;n exitosa seleccionado bd';

		if (! mysqli_select_db( $enlace, $TSysDB) )
		{
			$html_de_salida  = "<BR> Su transacci&oacute;n no puede ser procesada.<BR><BR>";
			$html_de_salida .= "<BR> Favor de intentar en unos momentos. (Error 5010)<BR><BR>";
			$html_de_salida .= "<P ALIGN=CENTER><A HREF=/transaccion/saldosTC/>Regresar</A></P>";
            $hubo_error = 1;
            break;
		}

		// Se busca la tarjeta selccionada en la tabla de BASE
		$consulta  = "SELECT acct_num, name1, mjv_acct_typ, tax_id, oth_acct_num FROM base WHERE acct_num = $tarjeta";
		$resultado = mysqli_query($enlace,$consulta);
		$registro  = mysqli_fetch_array($resultado, MYSQL_BOTH);
		mysqli_free_result($resultado);

		if  (!$registro)
		{

			$html_de_salida  = "<BR> Su transacci&oacute;n no puede ser procesada. <BR><BR>";
			$html_de_salida .= "<BR> La Tarjeta de Cr&eacute;dito no existe. <BR><BR>";
			$html_de_salida .= "<P ALIGN=CENTER><A HREF=/transaccion/saldosTC/>Regresar</A></P>";
            $hubo_error = 1;
            break;
		}
		else
		{

			$tipo = $registro["mjv_acct_typ"];
			switch( $tipo )
			{
				case "I": $tipo = "ICA"; break;	// Unica
				case "P": $tipo = "PCA"; break; // Principal
				case "C": $tipo = "CBA"; break; // CBA (cuenta llave)
				case "S": $tipo = "SCA"; break; // Adicional
			}

			if ( $tipo != "PCA" and $tipo != "ICA")	// Aceptar solo pca o ica
			{
				$html_de_salida  = "<BR> Su transacci&oacute;n no puede ser procesada. <BR><BR>";
				$html_de_salida .= "<BR> Esta operaci&oacute;n es permitida &uacute;nicamente para los titulares de las Tarjetas de Cr&eacute;dito. <BR>";
				$html_de_salida .= "<BR> Si su Tarjeta de Cr&eacute;dito es adicional, solicite al titular de la misma que realice la operaci&oacute;n en su sucursal. <BR>";
				$html_de_salida .= "<BR> Si tiene dudas acuda a su Sucursal m&aacute;s cercana  o bien llame al 01 800 47 10 400 donde con gusto le atenderemos.<BR><BR>";
				$html_de_salida .= "<P ALIGN=CENTER><A HREF=/transaccion/saldosTC/>Regresar</A></P>";
				$hubo_error = 1;
				break;
			}


			// el rfc del cliente debe ser igual al rfc de la tarjeta
			$rfc_tarjetas	= $registro["tax_id"];
			$rfc_ovation	= $RFC;

			//Si es el RFC de Ovation es de persona moral empezando con espacio lo quita, y toma los 9 caracters del 2-10
			if ( substr( $rfc_ovation, 0, 1 ) == " " )	{ $rfc_ovation = substr( $rfc_ovation, 1, 9  );	}
												else	{ $rfc_ovation = substr( $rfc_ovation, 0, 10 ); }

			//Si es el RFC de TSys es de persona moral el 4o caracter es numerico, toma los primeros 9
			if ( is_numeric( substr( $rfc_tarjetas, 3, 1 ) ) )	{ $rfc_tarjetas = substr( $rfc_tarjetas, 0, 9  );	}
														else	{ $rfc_tarjetas = substr( $rfc_tarjetas, 0, 10 );	}

			if ( trim($rfc_tarjetas) != trim($rfc_ovation) )
			{
				$html_de_salida  = "<BR> Su transacci&oacute;n no puede ser procesada. <BR><BR>";
				$html_de_salida .= "<BR> El R.F.C. del Sistema no corresponde al R.F.C. de su Tarjeta de Cr&eacute;dito. <BR><BR>";
				$html_de_salida .= "<P ALIGN=CENTER><A HREF=/transaccion/saldosTC/>Regresar</A></P>";
				$hubo_error = 1;
				break;
			}

			// Hasta aqui todo bien ...ahora si es una PCA nos traemos sus adicionales
			$NombreTitular	= $registro["name1"];			//Nombre del titular de la tarjeta
			$cba			= $registro["oth_acct_num"];	//Cba de la tarjeta
			$Empresa		= "  ";
			$Parent1		= "";							//tarjeta1SCA_nombretitular1SCA_SCA
			$Parent2		= "";							//tarjeta2SCA_nombretitular1SCA_SCA
			$Parent3		= "";							//tarjeta3SCA_nombretitular1SCA_SCA
			$Empresa		= "";


			if ( $tipo == "PCA" )	// La tarjeta es una principal ahora se buscan las adicionales
			{
				$consulta = "SELECT acct_num, name1 FROM base WHERE oth_acct_num = $cba AND mjv_acct_typ = 'S'";
				$resultado = mysqli_query($enlace,$consulta) or die('Su transacci&oacute;n no puede ser procesada. Favor de intentar más tarde ');// . $mysqli->error);


				$noCBAs = 0;
				while ( $registro = mysqli_fetch_array($resultado, MYSQL_BOTH) )	//si tiene adicionales
				{
					// El formato lo necesita ovation asi: Parent1=4201990000062134_LUZ+ADRIANA+RIVERA+RODRIGUEZ_++_SCA&
					// Solo se pueden dar de alta 3 adicionales

					//se van concatenando  de 3 adicionales por linea
					$noCBAs += 1;
					if ( $noCBAs >=1 and $noCBAs <= 3 )
					{
						$Parent1 .= $registro["acct_num"]."_".$registro["name1"]."_  _SCA";
					}
					else if ( $noCBAs >=4 and $noCBAs <= 6 )
					{
						$Parent2 .= $registro["acct_num"]."_".$registro["name1"]."_  _SCA";
					}
					else if ( $noCBAs >=7 and $noCBAs <= 9 )
					{
						$Parent3 .= $registro["acct_num"]."_".$registro["name1"]."_  _SCA";
					}

					if ( $noCBAs > 0 and $noCBAs < 10 )
					{
						$Day = $noCBAs;
					}

				}
				mysqli_free_result($resultado);

				$particularFields = "&PAN="		.urlencode( $tarjeta		).
									"&Nomben2="	.urlencode( $NombreTitular	).
									"&EspecCh="	.urlencode( $cba			).
									"&UbNo="	.urlencode( $tipo			).
									"&Empresa="	.urlencode( $Empresa		).
									"&Day="		.urlencode( $Day			).
									"&Parent1="	.urlencode( $Parent1		).
									"&Parent2="	.urlencode( $Parent2		).
									"&Parent3="	.urlencode( $Parent3		);

			}
			else if ($tipo == "ICA")
			{
				// Asi debe de ir

				$particularFields = "&PAN="		.urlencode( $tarjeta		).
									"&Nomben2="	.urlencode( $NombreTitular	).
									"&EspecCh="	.urlencode( $cba			).
									"&UbNo="	.urlencode( $tipo			).
									"&Empresa="	.urlencode( $Empresa		).
									"&Day="		.urlencode( $Day			).
									"&Parent1="	.urlencode( $Parent1		).
									"&Parent2="	.urlencode( $Parent2		).
									"&Parent3="	.urlencode( $Parent3		);

			}
			$html_de_salida = "";
		}

		mysqli_free_result($resultado);	// Liberar conjunto de resultados
		mysqli_close($enlace);			// Cerrar la conexion

		// DGM I 11Sep2007 Proyecto reemplazo comm server
		// ************************************************************************************************************************


        break;

    case "Extravio":    // Extravio
    case "Robo":        // Robo
		$ini =& $GLOBALS["GlobalSiteIni"];
		$RobExt_SiNo = $ini->read_var( "site", "TSysRobExt" );
		if ( $RobExt_SiNo == "si" )
		{
        // - Definir la transaccion a realizar
        $AccionSNA = "RepRoboExtravio";
        // - Definir los datos a enviar
        if( $tipo == "CBA" ) {
            $html_to_send = "";
            $Access = "Process";
            $Parent1 = "Tarjeta CBA no operan esta transaccion.";
            $particularFields = "&PAN=".urlencode($PAN)."&Access=".urlencode($Access)."&Parent1=".urlencode($Parent1);
            $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=tct&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // tarjetas de credito
            $hubo_error = 1;
            break;
        }
        $data_to_send = $tarjeta;
        // - Realizar la transaccion con SNA
        $sna_call_ret_code = $tr->PostToSNA($snabackend, $snabackendport, $snaappid, $AccionSNA, $snafs, $data_to_send, $sna_transaccion_buffer); // tarjeta de credito Consulta Saldo
        echo "<br><br>****************************";
        echo "[" . $sna_transaccion_buffer . "]";
        echo "<br><br>****************************";
        // - Si hubo errores de comunicacion
        switch ( $sna_call_ret_code ) {
        case -1:
            //  - Reportar a Ovation
            //  - Salir
            $transaccion_buffer = ReportSNAError( $backend, $usr, $priv, $qki, $tr, $Accion, $fs, $sna_transaccion_buffer );
            $html_de_salida = "";
            $hubo_error = 1;
            break;
        case -2:
            $transaccion_buffer = ReportSNAError( $backend, $usr, $priv, $qki, $tr, $Accion, $fs, $sna_transaccion_buffer );
            $html_de_salida = "";
            $hubo_error = 1;
            break;
        default:
            continue;
        }
        // - Si no hubo error
        //  - Decodificar respuesta
        $sna_ver_code = substr($sna_transaccion_buffer, 0, 2); // Los primeros dos bytes
        //echo "<em>sna_ver_code</em> "  . var_dump ( $sna_ver_code ) . "<br>\n";
        $sna_ret_code = substr($sna_transaccion_buffer, 2, 3); // Los siguientes tres bytes
        //echo "<em>sna_ret_code</em> "  . var_dump ( $sna_ret_code ) . "<br>\n";
        switch( $sna_ret_code ) {
            case "001":
            case "002":
            case "003":
            case "004":
            case "005":
            case "006":
            case "007":
            case "008":
            case "009":
            case "010":
            case "011":
                //echo "<em>Toy en los chorros<br>\n";
                //  - Si hubo errores de aplicacion
                //      - Reportar a Ovation
                //      - Salir
                $transaccion_buffer = ReportSNAError( $backend, $usr, $priv, $qki, $tr, $Accion, $fs, str_replace(chr($snafs), " ", substr( substr($sna_transaccion_buffer, 2), 0, -2) ) );
                $html_de_salida = "";
                $hubo_error = 1;
                break;
            case "999":
                //  - Si hubo errores de aplicacion
                //      - Reportar a Ovation
                //      - Salir
                $transaccion_buffer = ReportSNAError( $backend, $usr, $priv, $qki, $tr, $Accion, $fs, str_replace(chr($snafs), " ", substr( substr($sna_transaccion_buffer, 2), 0, -2) ) );
                $html_de_salida = "";
                $hubo_error = 1;
                break;
            case "000":
                // - Si no hubo error de aplicacion
                //  - Decodificar respuesta
                //  - Preparar resultado
                //  - En esta transaccion nosotros no presentamos nada al usuario, lo hace Ovation. Lo que
                //    en realidad nos interesa es el particular fields que regresa decode.
                $sna_ver_code2 = substr($sna_transaccion_buffer, 20, 45);
                //echo "<em>sna_ver_code2</em> "  . var_dump ( $sna_ver_code2 ) . "<br>\n";

                switch( $sna_ver_code2 ) {
				            case "MASTER RECORD UPDATED. CRV STATUS ADDED.     ":
				            case "NO CHANGE DETECTED. MAINTENANCE IGNORED. ":
				            case "NO CHANGE DETECTED. MAINTENANCE IGNORED.     ":
				 				$html_de_salida = $tr->decode(substr($sna_transaccion_buffer, 5), $AccionSNA, $snafs, $tarjeta, $particularFields, $rfc, $tipo, $nombre );
				 				break;
				            default:
								$transaccion_buffer = ReportSNAError( $backend, $usr, $priv, $qki, $tr, $Accion, $fs, str_replace(chr($snafs), " ", substr( substr($sna_transaccion_buffer, 2), 0, -2) ) );
								$html_de_salida = "";
								$hubo_error = 1;
								break;
				}
                //$html_de_salida = $tr->decode(substr($sna_transaccion_buffer, 5), $AccionSNA, $snafs, $tarjeta, $particularFields, $rfc, $tipo, $nombre );
        }
		}
		else
		{
			// - Enviar HTML mostrando telefonos para Reporte de Robo / Extravio
			$titulo_pagina = "Reporte de " . $Accion;
			$html_de_salida = "
			<TABLE width=\"100%\" border=0 cellPadding=0 cellSpacing=0>
			  <TBODY>
				<TR>
				  <TD vAlign=top width=\"100%\"><FONT face=\"Verdana, Arial, Helvetica, sans-serif\" color=#006699 size=+2>&nbsp;</FONT>
					<TABLE cellSpacing=0 cellPadding=1 width=\"100%\" border=0>
					  <TBODY>
						<TR>
						  <TD><FONT face=\"Verdana, Arial, Helvetica, sans-serif\" color=#f70000 size=1><B><STRONG>$titulo_pagina</STRONG></B></FONT></TD>
						  <TD><DIV align=right><IMG src=\"/images/membrete.jpg\" height=53  width=390></DIV></TD>
						</TR>
					  </TBODY>
					</TABLE>
				  </TD>
				</TR>
				<TR>
				  <TD width=\"100%\">
					<TABLE cellSpacing=0 cellPadding=0 width=\"100%\" border=0>
					  <TBODY>
						<TR>
						  <TD align=left bgColor=#ffffff>&nbsp;</TD>
							<TD align=right color=\"#006699\"><DIV align=right><P><FONT face=\"Verdana, Arial, Helvetica, sans-serif\" size=-1><B>" . $titulo_pagina . "<BR>VISA INTERNACIONAL<BR><IMG height=22 src=\"/images/visa.gif\" width=36><BR></B></FONT><FONT color=#ff9900 size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><FONT color=#000000 size=1></DIV></TD>
						</TR>
					  </TBODY>
					</TABLE>
				  </TD>
				</TR>
			  </TBODY>
			</TABLE>
			";
			$html_de_salida .= "<table cellspacing=0 cellpadding=0 width=\"70%\" align=center border=0>
			<tbody>
			<tr>
			  <td bgcolor=white colspan=2></td>
			</tr>
			<tr>
			  <td bgcolor=white colspan=2></td>
			</tr>
			<tr>
			  <td bgcolor=white colspan=2></td>
			</tr>
			<tr>
			  <td bgcolor=white colspan=2></td>
			</tr>
			<tr bgcolor=white>
			  <td colspan=2>
			  <font face=\"Verdana, Arial, Helvetica, sans-serif\" color=black size=-1>
					<B>Centro de Atenci&oacute;n Telef&oacute;nica:</B><BR>
					(477)710-4640 en la Ciudad de Le&oacute;n Guanajuato, M&eacute;xico,<BR>
					01-800-471-0400, en el resto de la Rep&uacute;blica Mexicana,
			  </font></td>
			  <td></td>
			</tr>
			<tr>
			  <td bgcolor=white colspan=2></td>
			</tr>
			<tr bgcolor=white>
			  <td colspan=2><font face=\"Verdana, Arial, Helvetica, sans-serif\" color=black size=-1>
					<BR><B>Centro de Asistencia Visa:</B><BR>
					En Estados Unidos y Canad&aacute; 1-800-847-2911<BR>
					Fuera de Estados Unidos por cobrar (410)581-0120
			  </font></td>
			  <td></td>
			</tr>
			<tr>
			  <td bgcolor=white colspan=2></td>
			</tr>
			<tr>
			  <td bgcolor=white colspan=2></td>
			</tr>";
		}
        break;

    case "Movtos":      // Movimientos
		$titulo_pagina = "Movimientos a la fecha";
        //$AccionSNA = "MovtosFecha";

		//echo "tarjeta[" . $tarjeta . "]";
		$QryTDC = new eZTSys( );
		$QryTDC->SetTarjeta( $tarjeta );		//Guarda el No de TDC que se esta consultando, la original
		$QryTDC->GetBase( $tarjeta );
		if ( $QryTDC->GetBaseReg( "Acct_Num") == $tarjeta )
		{
			$html_de_salida = "
			<TABLE width=\"100%\" border=0 cellPadding=0 cellSpacing=0>
			  <TBODY>
				<TR>
				  <TD vAlign=top width=\"100%\"><FONT face=\"Verdana, Arial, Helvetica, sans-serif\" color=#006699 size=+2>&nbsp;</FONT>
					<TABLE cellSpacing=0 cellPadding=1 width=\"100%\" border=0>
					  <TBODY>
						<TR>
						  <TD><FONT face=\"Verdana, Arial, Helvetica, sans-serif\" color=#f70000 size=1><B><STRONG>$titulo_pagina</STRONG></B></FONT></TD>
						  <TD><DIV align=right><IMG src=\"/images/membrete.jpg\" height=53  width=390></DIV></TD>
						</TR>
					  </TBODY>
					</TABLE>
				  </TD>
				</TR>
				<TR>
				  <TD width=\"100%\">
					<TABLE cellSpacing=0 cellPadding=0 width=\"100%\" border=0>
					  <TBODY>
						<TR>
						  <TD align=left bgColor=#ffffff>&nbsp;</TD>
							<TD align=right color=\"#006699\"><DIV align=right><P><FONT face=\"Verdana, Arial, Helvetica, sans-serif\" size=-1><B>Movimientos a la fecha<BR>VISA INTERNACIONAL<BR><IMG height=22 src=\"/images/visa.gif\" width=36><BR></B></FONT><FONT color=#ff9900 size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><FONT color=#000000 size=1></DIV></TD>
						</TR>
					  </TBODY>
					</TABLE>
				  </TD>
				</TR>
			  </TBODY>
			</TABLE>
			";
			$html_de_salida .= "<table cellspacing=0 cellpadding=0 width=\"100%\" border=0>
			<tbody>
			<tr>
			  <td bgcolor=white colspan=2></td>
			</tr>
			<tr>
			  <td bgcolor=white colspan=2></td>
			</tr>
			<tr>
			  <td bgcolor=white colspan=2></td>
			</tr>
			<tr>
			  <td bgcolor=black colspan=2></td>
			</tr>
			<tr bgcolor=#5a419c>
			  <td colspan=2><font face=\"Verdana, Arial, Helvetica, sans-serif\" color=#ffffff size=-1><b>Transacciones con la tarjeta de " . $QryTDC->GetBaseReg( "Nombre" ) . " </b><br> Cuenta No.: XXXX-XXXX-XXXX-". $QryTDC->GetTarjeta( "L4" ) ."</font></td>
			  <td></td>
			</tr>
			<tr>
			  <td bgcolor=black colspan=2></td>
			</tr>
			<tr bgcolor=#000000>
			  <td colspan=2><font face=\"Verdana, Arial, Helvetica, sans-serif\" color=#ffffff size=-1><b>Detalle de cargos en pesos</b></font></td>
			  <td></td>
			</tr>
			<tr>
			  <td bgcolor=black colspan=2></td>
			</tr>
			<tr>
			  <td bgcolor=#ffffff colspan=2></td>
			</tr>";

			$hasta = date( "Y-m-d" );		//yyyymmdd
			$QryTDC->GetTrans( "Movs", $QryTDC->GetBaseReg( "F_Corte" ), $hasta );

			GeneraMovtos( $QryTDC, $html_de_salida );

			//******Vamos para ver si tiene transcodificaciones ...********
			$TransCodificada = $QryTDC->GetBaseReg( "Acct_Num" );
			$QryTDC->GetTranscod( );
			$desde = $QryTDC->GetBaseReg( "F_Corte" );

			while ( $QryTDC->GetTranscodReg( "Acct_Num" ) == $TransCodificada )
			{
				$QryTDC->GetBase( $QryTDC->GetTranscodReg( "Acct_Num_Old" ) );
				if ( $QryTDC->GetBaseReg( "Acct_Num") == $QryTDC->GetTranscodReg( "Acct_Num_Old" ) )
				{
					$QryTDC->GetTrans( "Movs", $desde, $QryTDC->GetTranscod( "F_Transcod" ) );	//-1 un día
					GeneraMovtos( $QryTDC, $html_de_salida );

					$TransCodificada = $QryTDC->GetTranscodReg( "Acct_Num_Old" );
					$QryTDC->GetTranscod(  );
				}
				else
				{
					break;
				}
			}
			//******Termina transcodificaciones********

			$QryTDC->GetTransEnd( );

			FinMovtos( $html_de_salida );
		}
		else
		{
			$html_de_salida = Muestra_Error( "Consulta de " . $Accion, "Registro Base no encontrado.", 7203 );
		}
		//$particularFields = "&PAN=".urlencode($PAN)."&Empresa=".urlencode($Empresa)."&EspecCh=".urlencode($EspecCh);

        break;
    case "EdoCta":      // Estado de cuenta
    	//echo "tarjeta[" . $tarjeta . "]";
        $QryTDC = new eZTSys( );
		$QryTDC->SetTarjeta( $tarjeta );		//Guarda el No de TDC que se esta consultando, la original
        if ( $tipo != "ICA" )
        {
            $tarjeta = trim($Apocope);	//Toma el del consolidado
        }
        $QryTDC->GetBase( $tarjeta );
		if ( $QryTDC->GetBaseReg( "Acct_Num") == $tarjeta )
		{
			// - Definir la transaccion a realizar
			switch($Month) {
			case "01":
				$AccionTSys = "UltimoEstadoCta";
				$titulo_pagina = "Último estado de cuenta";
				$QryTDC->GetHistory( "EdoCta2Meses" );
				if ( $QryTDC->GetHistoryReg( "Acct_Num" ) == $tarjeta )
				{
					$nl_SaldoAnterior = $QryTDC->GetHistoryRegFormat( "Saldo_Corte" );		//Saldo Corte Anterior
				}
				else
				{
					$nl_SaldoAnterior = "N.D.";
				}
				//echo "\n\nMonth[". $Month. "] nl_SaldoAnterior[".$nl_SaldoAnterior."]";
				break;
			case "02":
				$AccionTSys = "EdoCta2Meses";
				$titulo_pagina = "Estado de cuenta de hace dos meses";
				$QryTDC->GetHistory( "EdoCta3Meses" );
				if ( $QryTDC->GetHistoryReg( "Acct_Num" ) == $tarjeta )
				{
					$nl_SaldoAnterior = $QryTDC->GetHistoryRegFormat( "Saldo_Corte" );		//Saldo Corte Anterior
				}
				else
				{
					$nl_SaldoAnterior = "N.D.";
				}
				//echo "\n\nMonth[". $Month. "] nl_SaldoAnterior[".$nl_SaldoAnterior."]";
				break;
			case "03":
				$AccionTSys = "EdoCta3Meses";
				$titulo_pagina = "Estado de cuenta de hace tres meses";
				$nl_SaldoAnterior = "N.D.";		//Saldo Corte Anterior
				//echo "\n\nMonth[". $Month. "] nl_SaldoAnterior[".$nl_SaldoAnterior."]";
				break;
			}
			$QryTDC->GetHistory( $AccionTSys );

			$QryTDC->History->GetPeriodo( $dl_Desde, $dl_Hasta );
			if ( $QryTDC->GetHistoryReg( "Acct_Num" ) != $tarjeta )
			{
				$html_de_salida = Muestra_Error( "Consulta de " . $Accion, "Registro de Estado de Cuenta no encontrado.", 7204 );
				break;
			}

			$PagoMinimo = $QryTDC->GetHistoryRegFormat( "Pago_Minimo" );
			if ( $QryTDC->GetBaseReg( "Tipo" ) != "I" and $QryTDC->GetBaseReg( "Tipo_Cta" ) == "B" )
			{
				$PagoMinimo = $QryTDC->GetHistoryRegFormat( "Saldo_Corte" );
			}

			$LimiteCredito = $QryTDC->GetBaseRegFormat( "Limite" );
			/*if ( $QryTDC->GetBaseReg( "VIP" ) == 1 )
			{
				$LimiteCredito = "V.I.P.";
			}*/
			$html_de_salida = "
			<TABLE width=\"100%\" border=0 cellPadding=0 cellSpacing=0>
			<TBODY>
			<TR>
			<TD vAlign=top width=\"100%\"><FONT face=\"Verdana, Arial, Helvetica, sans-serif\" color=#006699 size=+2>&nbsp;</FONT>
			<TABLE cellSpacing=0 cellPadding=1 width=\"100%\" border=0>
			  <TBODY>
			<TR>
			  <TD><FONT face=\"Verdana, Arial, Helvetica, sans-serif\" color=#f70000 size=1><B><STRONG>$titulo_pagina</STRONG></B></FONT></TD>
			  <TD><DIV align=right><IMG src=\"/images/membrete.jpg\" height=53  width=390></DIV></TD>
			</TR>
			  </TBODY>
			</TABLE></TD>
			</TR>
			<TR>
			<TD width=\"100%\"> <TABLE cellSpacing=0 cellPadding=0 width=\"100%\" border=0>
			  <TBODY>
			<TR>
			  <TD align=left bgColor=#ffffff>&nbsp;</TD>
			  <TD align=right color=\"#006699\"><DIV align=right><P><FONT face=\"Verdana, Arial, Helvetica, sans-serif\" size=-1><B>$titulo_pagina<BR>VISA INTERNACIONAL<BR><IMG height=22 src=\"/images/visa.gif\" width=36><BR></B></FONT><FONT color=#ff9900 size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><FONT color=#000000 size=1></DIV></TD>
			</TR>
			<TR>
			  <TD>&nbsp;</TD>
			  <TD>&nbsp;</TD>
			</TR>
			  </TBODY>
			</TABLE>
			</TD>
			</TR>
			</TBODY>
			</TABLE>
			";
			$html_de_salida .= "<table width=\"100%\" border=0 cellpadding=0 cellspacing=0>
			<tbody>
			<tr>
			  <td width=\"100%\"> <table cellspacing=0 cellpadding=0 width=\"100%\" border=0>
				  <tbody>
					<tr>
					  <td bgcolor=black colspan=3> <div align=right>
						  <table cellspacing=0 cellpadding=1 width=\"100%\" border=0>
							<tbody>
							  <tr>
								<td width=\"50%\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" color=#ffffff size=-1><b>" . $QryTDC->GetBaseReg( "Nombre" ) . "</b></font></td>
								<td width=\"50%\"> <div align=right></div></td>
							  </tr>
							</tbody>
						  </table>
						</div></td>
					</tr>
					<tr>
					  <td bgcolor=black colspan=3></td>
					</tr>
				  </tbody>
				</table>
				<br> <table cellspacing=0 cellpadding=1 width=\"100%\" border=0>
				  <tbody>
					<tr>
					  <td width=\"45%\" bgcolor=#5a419c> <div align=center><font color=#ffffff size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><b>Información de la cuenta</b></font></div></td>
					</tr>
					<tr>
					  <td width=\"46%\"> <table cellspacing=0 cellpadding=1 width=\"100%\" border=0>
						  <tbody>
							<tr bgcolor=#f1f1f1>
							  <td><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=2>N&uacute;mero de cuenta</font></b></td>
							  <td> <div align=right><font size=2 face=\"Verdana, Arial, Helvetica, sans-serif\"><b>XXXX-XXXX-XXXX-" . $QryTDC->GetTarjeta( "L4" ) . "</b></font></div></td>
							</tr>
							<tr>
							  <td bgcolor=black colspan=4></td>
							</tr>
							<tr>
							  <td><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=2>L&iacute;mite de cr&eacute;dito</font></b></td>
							  <td> <div align=right><font size=2 face=\"Verdana, Arial, Helvetica, sans-serif\"><b>" . $LimiteCredito . "</b></font></div></td>
							</tr>
							<tr>
							  <td bgcolor=black colspan=4></td>
							</tr>" .
							/*<tr bgcolor=#f1f1f1>
							  <td><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=2>Cr&eacute;dito disponible</font></b></td>
							  <td> <div align=right><font size=2 face=\"Verdana, Arial, Helvetica, sans-serif\"><b>" . $QryTDC->GetHistoryRegFormat( "L_Credito" ) . "</b></font></div></td>
							</tr>*/
							"<tr>
							  <td bgcolor=black colspan=4></td>
							</tr>
							<tr>
							  <td><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=2>Pago m&iacute;nimo</font></b></td>
							  <td> <div align=right><font size=2 face=\"Verdana, Arial, Helvetica, sans-serif\"><b>" . $PagoMinimo . "</b></font></div></td>
							</tr>
							<tr>
							  <td bgcolor=black colspan=4></td>
							</tr>
							<tr bgcolor=#f1f1f1>
							  <td><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=2>Fecha de corte</font></b></td>
							  <td> <div align=right><font size=2 face=\"Verdana, Arial, Helvetica, sans-serif\"><b>" . $QryTDC->GetHistoryRegFormat( "Effective" ) . "</b></font></div></td>
							</tr>
							<tr>
							  <td bgcolor=black colspan=4></td>
							</tr>
							<tr>
							  <td><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=2>Fecha l&iacute;mite de pago</font></b></td>
							  <td> <div align=right><font size=2 face=\"Verdana, Arial, Helvetica, sans-serif\"><b>" . $QryTDC->GetBaseRegFormat( "F_Lim_Pago" ) . "</b></font></div></td>
							</tr>
							<tr>
							  <td bgcolor=black colspan=4></td>
							</tr>
							<tr bgcolor=#f1f1f1>
							  <td><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=2>Saldo anterior</font></b></td>
							  <td> <div align=right><font size=2 face=\"Verdana, Arial, Helvetica, sans-serif\"><b>" . $nl_SaldoAnterior . "</b></font></div></td>
							</tr>
							<tr>
							  <td bgcolor=black colspan=4></td>
							</tr>
							<tr>
							  <td><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=2>Intereses</font></b></td>
							  <td> <div align=right><font size=2 face=\"Verdana, Arial, Helvetica, sans-serif\"><b>" . $QryTDC->GetHistoryRegFormat( "Intereses" ) . "</b></font></div></td>
							</tr>
							<tr>
							  <td bgcolor=black colspan=4></td>
							</tr>
							<tr bgcolor=#f1f1f1>
							  <td><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=2>Compras y otros cargos</font></b></td>
							  <td> <div align=right><font size=2 face=\"Verdana, Arial, Helvetica, sans-serif\"><b>" . $QryTDC->GetHistoryRegFormat( "Monto_Compras" ) . "</b></font></div></td>
							</tr>
							<tr>
							  <td bgcolor=black colspan=4></td>
							</tr>
							<tr>
							  <td><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=2>Pagos y dep&oacute;sitos</font></b></td>
							  <td> <div align=right><font size=2 face=\"Verdana, Arial, Helvetica, sans-serif\"><b>" . $QryTDC->GetHistoryRegFormat( "Monto_Pagos" ) . "</b></font></div></td>
							</tr>
							<tr>
							  <td bgcolor=black colspan=4></td>
							</tr>
							<tr bgcolor=#f1f1f1>
							  <td><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=2>Saldo al Corte</font></b></td>
							  <td> <div align=right><font size=2 face=\"Verdana, Arial, Helvetica, sans-serif\"><b>" . $QryTDC->GetHistoryRegFormat( "Saldo_Corte" ) . "</b></font></div></td>
							</tr>
							<tr>
							  <td bgcolor=black colspan=4></td>
							</tr>
							<tr>
							  <td><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=2>Importe de pago vencido</font></b></td>
							  <td> <div align=right><font size=2 face=\"Verdana, Arial, Helvetica, sans-serif\"><b>" . $QryTDC->GetHistoryRegFormat( "Monto_Vdo" ) . "</b></font></div></td>
							</tr>
							<tr>
							  <td bgcolor=black colspan=4></td>
							</tr>
						  </tbody>
						</table></td>
					</tr>
				  </tbody>
				</table>
				<br></td>
			</tr>
		  </tbody>
		</table>";

			$html_de_salida .= "<table cellspacing=0 cellpadding=0 width=\"100%\" border=0>
			<tbody>
			<tr>
			  <td bgcolor=white colspan=2></td>
			</tr>
			<tr>
			  <td bgcolor=white colspan=2></td>
			</tr>
			<tr>
			  <td bgcolor=white colspan=2></td>
			</tr>
			<tr>
			  <td bgcolor=black colspan=2></td>
			</tr>
			<tr bgcolor=#5a419c>
			  <td colspan=2><font face=\"Verdana, Arial, Helvetica, sans-serif\" color=#ffffff size=-1>";
			 if ( $QryTDC->GetBaseReg( "Tipo" ) == 'I' )
			 {
			  	$html_de_salida .= "<b>Transacciones con la tarjeta de " . $QryTDC->GetBaseReg( "Nombre" ) . "</b><br> Cuenta No.: XXXX-XXXX-XXXX-" . $QryTDC->GetTarjeta( "L4" );
			 }
			 else
			 {
			  	$html_de_salida .= "<b>Total de Transacciones de la cuenta perteneciente a la tarjeta de " . $QryTDC->GetBaseReg( "Nombre" ) . "</b><br> Cuenta No.: XXXX-XXXX-XXXX-" . $QryTDC->GetTarjeta( "L4" );
			 }
			 $html_de_salida .= "</font></td>
			  <td></td>
			</tr>
			<tr>
			  <td bgcolor=black colspan=2></td>
			</tr>
			<tr bgcolor=#000000>
			  <td colspan=2><font face=\"Verdana, Arial, Helvetica, sans-serif\" color=#ffffff size=-1><b>Detalle de cargos en pesos</b></font></td>
			  <td></td>
			</tr>
			<tr>
			  <td bgcolor=black colspan=2></td>
			</tr>
			<tr>
			  <td bgcolor=#ffffff colspan=2></td>
			</tr>";

			$QryTDC->GetTrans( "EC", $dl_Desde, $dl_Hasta );

			GeneraMovtos( $QryTDC, $html_de_salida );

			FinMovtos( $html_de_salida );
		}
		else
		{
			$html_de_salida = Muestra_Error( "Consulta de " . $Accion, "Registro Base no encontrado.", 7205 );
		}

        break;
    }

    if( ! $hubo_error ) {
        $particularFields .= "&Accion=".urlencode($Accion);
        //echo "<em>particularFields</em> " . var_dump ( $particularFields ) . "<br>\n";
        $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=tct&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // tarjetas de credito
    }
    //echo "Accion[" . $Accion . "]";
	if ( ( $Accion == "Robo" or $Accion == "Extravio" ) and $RobExt_SiNo == "no" )
	{
	    $transaccion_buffer = $html_de_salida;
	}
	else
	{
	    $transaccion_buffer = $transaccion_buffer . $html_de_salida;
	}
    $t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "saldosTC_tpl" );
    // phpinfo();
} else {
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/saldosTC/" );
    $t->pparse( "output", "user_login_tpl" );
}

?>
