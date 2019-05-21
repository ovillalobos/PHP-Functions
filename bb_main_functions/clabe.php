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

// *************************************************************************************************************
// DGM - DBA I 04Abr2008  Esquema de validacion de seguridad informatica

// Cuando jboss sirve la 1a pagina para usuarios con agrupacion, pinta un <select> con las posibles cuentas a consultar
// entonces en saldos.php estas cuentas son extraidas para aplicarles un md5 y posteriormente estos md5 pintalos en campos hidden en el transaccion_buffer
// estos md5 calculados indican las unicas posibles cuentas de donde se podra consultar saldos.
// finalmente cuando el usuario hace click en aceptar el sistema busca la cuenta que seleccionó o tecleó el usuario, le aplica un md5
// y lo compara contra los md5 primeros permitidos, en caso de exito lo deja pasar, de lo contrario le marca un error generico.

// DGM - DBA F 04Abr2008  Esquema de validacion de seguridad informatica
// *************************************************************************************************************

// --------	-------	---------------------------------------------------------------------------------------------- *
// ¿Cuándo?	¿Quién?	¿Qué?                                                                                          *
// --------	-------	---------------------------------------------------------------------------------------------- *
// 13Sep2012 CLG    Retirar boton "Imprimir" y "Regresar" de la ventana emergente para imprimir el documento.	   *
//
//
// --------	-------	---------------------------------------------------------------------------------------------- *

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezhttptool.php" );

include_once( "eztransaccion/classes/eztransaccion.php" );
include_once( "ezsession/classes/ezsession.php" );

$session =& eZSession::globalSession();
global $md5BB;

// *************************************************************************************************
// DGM-DBA I 04Abr2008 Observaciones seguridad informática
// Esta funci8on extrae las cuentas del transaccion_buffer y calcula su md5 de cada una de ellas
// y posteriormente esos md5 los pinta como hidden en el mismo transaccion_buffer

function MD5_Ctas($buffer,$galletita, $usr)
{

	$posini = strpos($buffer,"<SELECT NAME=\"FrAccount\">");
	$posfin = strpos($buffer,"</SELECT>");

	$ctas 		 = substr ($buffer,$posini,($posfin-($posini+25)));
	$md5hide 	 = "";
	$cuenta 	 = "";
	$posn 	 	 = 0;
	$a 		 	 = 1;
	$ctasArreglo = "";

	while ($a)
	{
		$a = strpos($ctas,"<OPTION VALUE=\"",$posn); //extrae cuenta por cuenta
		if ($a)
		{
			$cuenta = "";
			for ($i=$a+strlen("<OPTION VALUE=\""); $i<strlen($ctas); $i++)
			{
				$caracter = $ctas[$i];
				if ($caracter >= "0" and $caracter <= "9")  //solo toma los caracteres numericos del <option>
				{
					$cuenta .= $caracter;
				}
				else
				{
					$posn = $i;
					$i = strlen($ctas);
				}
			}

			$cuenta 		= md5($galletita.trim($cuenta).trim($usr)); //se calcula su md5
			$ctasArreglo   .= $cuenta."|";
		}
	}

	// finalmente se agrega un campo hidden con todos los md5 calculados separados por pipe
	return     substr($buffer, 0, $posfin + 9) .
			   "<INPUT TYPE=HIDDEN NAME=\"ctasArreglo\" VALUE=$ctasArreglo>" .
			   substr($buffer, $posfin + 9);

}


// ******************************************************************************************************
// Esta funcion compara la cuenta seleccionada o tecleada por el usuario contra el campo de md5s validos
function MD5_Valida($ctasPermitidas,$cuentaConsultando,$galletita, $usr)
{

	$ctaArreglo = explode("|", $ctasPermitidas); //extrae el campo md5 (pintado desde el fracc) en un arreglo

	foreach($ctaArreglo as $ctaPermitida)
	{
		$tmp =md5($galletita.trim($cuentaConsultando).trim($usr));	// calcula el md5 de la cuenta que quiere consultar

		if ($ctaPermitida == $tmp) // si es igual entonces es una cuenta permitida
		{
			return 0;
		}
	}
	return -1;  // es una cuenta que no es permitida (indica que el usuario modifico de alguna manera la cuenta destino y no es permitida para consulta)
}

// DGM-DBA F 04Abr2008 Observaciones seguridad informática
// *************************************************************************************************


if( !$session->fetch() )
    $session->store();

$ini =& $GLOBALS["GlobalSiteIni"];

$Language = $ini->read_var( "eZTransaccionMain", "Language" );
// $backend = $ini->read_var( "eZTransaccionMain", "Backend" );
$ServerNumber = $ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );

$backend = "gw" . $ServerNumber. $DomainPostfix;
if ( $GLOBALS["DEBUGA"] == true ) {
		$log = "En ".$_SERVER['SCRIPT_FILENAME']." que es ".$_SERVER['PHP_SELF']." (backend) ->" . print_r( $backend, true ) . "|";
        eZLog::writeNotice( $log );
		define_syslog_variables();
		openlog( "IVA", LOG_PID | LOG_PERROR, LOG_DAEMON );
		syslog( LOG_WARNING, "$log" );
		closelog();
}

include_once( "ezuserbb/classes/ezuser.php" );

$user =& eZUserBB::currentUser();

if ( $GLOBALS["DEBUGA"] == true ) {
        eZLog::writeNotice( "En saldos (user) ->" . print_r( $user, true ) . "|" );
}

function formatPage ( $HTML )
{
		//echo "el HTML antes....[$HTML]";
		//$HTML = str_replace("<INPUT TYPE=\"BUTTON\" NAME=\"Button2\" VALUE=\"Regresar\" LANGUAGE=\"JavaScript\" ONCLICK=\"parent.history.back()\"><INPUT TYPE=\"SUBMIT\" NAME=\"Button\" VALUE=\"Ver más\" LANGUAGE=\"JavaScript\"><INPUT TYPE=\"BUTTON\" NAME=\"ButtonImprimir\" VALUE=\"Imprimir\" LANGUAGE=\"JavaScript\" ONCLICK=\"ventana()\">","",$HTML); 		//CLG - 13Sep2012 - Track 236543

		$HTML = str_replace("<INPUT TYPE=\"BUTTON\" NAME=\"Button2\" VALUE=\"Regresar\" LANGUAGE=\"JavaScript\" ONCLICK=\"parent.history.back()\">"," ",$HTML); //CLG - 13Sep2012 - Track 236543
		$HTML = str_replace("<INPUT TYPE=\"BUTTON\" NAME=\"ButtonImprimir\" VALUE=\"Imprimir\" LANGUAGE=\"JavaScript\" ONCLICK=\"ventana()\">"," ",$HTML); 		//CLG - 13Sep2012 - Track 236543

		//echo "el HTML despues....[$HTML]";

		//$HTML = str_replace("<INPUT TYPE=\"BUTTON\" NAME=\"Button2\" VALUE=\"Regresar\" LANGUAGE=\"JavaScript\" ONCLICK=\"parent.history.back()\"><INPUT TYPE=\"BUTTON\" NAME=\"ButtonImprimir\" VALUE=\"Imprimir\" LANGUAGE=\"JavaScript\" ONCLICK=\"ventana()\">","", $HTML);		//CLG - 13Sep2012 - Track 236543
		//$HTML = ereg_replace("<INPUT TYPE\=\"SUBMIT\" NAME=\"Button\" VALUE=\"Ver más\" LANGUAGE=\"JavaScript\"> ([^>]+)","", $HTML );
		//$HTML = ereg_replace("","", $HTML );

		return ( $HTML );
}

if ( $user )
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "clabe.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "clabe_tpl" => "clabe.tpl"
        ) );

    $session =& eZSession::globalSession();

    if ( !$session->fetch() )
    {
        $session->store();
    }

    if( !isset /*(HB AGL - Ajustes PHP5)*/( $_POST['FrAccount'] ) ) {
    	$_POST['FrAccount'] = "0";
    }

    $tr = new eZTransaccion( );
    $usr = $session->variable( "r_usr" );
    $qki = $session->variable( "r_qki" );
    $priv = $session->variable( "r_priv" );

    $result = 0;	// DBA-DBM 04Abr2008 observaciones seguridad informatica


	// ***********************************************************
	// DBA-DGM I 04Abr2008 observaciones seguridad informatica
	if($_POST['FrAccount'] != "0")
    {
    	// Aqui solo entra a validar la cuenta que selcciono contra las cuentas md5 calculadas previamente
		$result = MD5_Valida($_POST['ctasArreglo'],$_POST['FrAccount'],$session->variable( "r_qki" ),$usr);
		if ($result != 0)
		{
			$transaccion_buffer = "<p>No existe una cuenta que pueda procesar esta transacci&oacute;n.</p><br>";
		}

	}
	// DBA-DGM F 04Abr2008 observaciones seguridad informatica
	// ***********************************************************

	if( !isset /*(HB AGL - Ajustes PHP5)*/( $transaccion_buffer ) ) {
	    	$transaccion_buffer = "";
    }

	if ($result == 0) // DBA-DBM F 04Abr2008 observaciones seguridad informatica
	{
		if($_POST['FrAccount'] != "0")
		{
		$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=cbe&Access=FrAc&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv)."&FrAccount=".urlencode($_POST['FrAccount'])."&AgruPagIni=".urlencode($_POST['AgruPagIni']), $qki, $usr, $qki, $priv, $transaccion_buffer); // Consulta CLABE
		}
		else //Para mostrar agrupadas
		{
			$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=cbe&Access=FrAc&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv)."&FrAccount=TODOS&AgruPagIni=".urlencode($_POST['AgruPagIni']), $qki, $usr, $qki, $priv, $transaccion_buffer); // Consulta CLABE
		}
		$tr->blog($qki,"CLABE", $_POST['FrAccount'], $Day, $Month, $Year, $Accion);


		// DBA-DGM F 04Abr2008 observaciones seguridad informatica
		if ($_POST['FrAccount'] == "0" and strpos($transaccion_buffer,"Seleccione el Cliente:")>0 ) // aqui solo entra para el 1st access de los saldos de clientes agrupados
		{
			$transaccion_buffer = MD5_Ctas($transaccion_buffer,$session->variable( "r_qki" ),$usr); // aqui agrega en la pagina los md5 que seran validos, para cada cliente seleccionado
		}
	}

			$ini =& INIFile::globalINI();
					$SecureServer = $ini->read_var( "site", "SecureServer" );
					$SecureServerType = $ini->read_var( "site", "SecureServerType" );
					$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );
					$ServerNumber = $ini->read_var( "site", "ServerNumber" );

					//echo "Imprimiendo el buffer1.....[$transaccion_buffer]";

					$buffer2 = formatPage($transaccion_buffer);

					$buffer2 = str_replace(chr(34),chr(92).chr(34),$buffer2);	//coloco \ antes del "
					$buffer2 = str_replace("<FONT COLOR=".chr(92).chr(34)."White".chr(92).chr(34).">","<FONT COLOR=".chr(92).chr(34)."BLACK".chr(92).chr(34)."><span style='font-size:11px;font-family:Arial'>", $buffer2); //fuente Arial-8 para encabezados. Ademas alineacion superior
					$buffer2 = str_replace("</P><BR>","</P>",$buffer2);
					$buffer2 = str_replace("<P ALIGN=CENTER>","<P ALIGN=CENTER><FONT SIZE=1>",$buffer2);
					$buffer2 = str_replace("<H1>Cuentas Filiales</H1>","<H2><span style='font-size:13px;font-family:Arial;'>Cuentas Filiales</H2>",$buffer2); //Titulo de cuentas filiales
					$buffer2 = str_replace("<H1>Cuentas Agrupadas</H1>","<H2><span style='font-size:13px;font-family:Arial;'>Cuentas Agrupadas</H2>",$buffer2); //Titulo de cuentas filiales
					$buffer2 = str_replace("<TH BGCOLOR=#CCCCFF>","<TH BGCOLOR=#CCCCFF VALIGN='TOP'><span style='font-size:11px;font-family:Arial'>", $buffer2); //fuente Arial-8 para Fecha, Descripcion

					$buffer2 = str_replace("<TD BGCOLOR=#DDDDDD ALIGN=CENTER>","<TD BGCOLOR = ".chr(92).chr(34)."#DDDDDD".chr(92).chr(34)." ALIGN='CENTER'><span style='font-size:11px;font-family:Arial'>", $buffer2); //fuente Arial-8 para Fecha, Descripcion



					$buffer2 = str_replace("<TD   BGCOLOR = ".chr(92).chr(34)."#DDDDDD".chr(92).chr(34)." ALIGN = ".chr(92).chr(34)."RIGHT".chr(92).chr(34)." >","<TD   BGCOLOR = ".chr(92).chr(34)."#DDDDDD".chr(92).chr(34)." ALIGN = ".chr(92).chr(34)."LEFT".chr(92).chr(34)." VALIGN='TOP'><span style='font-size:11px;font-family:Arial'>", $buffer2); //fuente Arial-8 para monto. Ademas alineacion superior
					$buffer2 = str_replace("<TD BGCOLOR=".chr(92).chr(34)."#DDDDDD".chr(92).chr(34)." ALIGN=".chr(92).chr(34)."RIGHT".chr(92).chr(34).">","<TD   BGCOLOR = ".chr(92).chr(34)."#DDDDDD".chr(92).chr(34)." ALIGN = ".chr(92).chr(34)."RIGHT".chr(92).chr(34)." VALIGN='TOP'><span style='font-size:11px;font-family:Arial'>", $buffer2); //fuente Arial-8 para saldo. Ademas alineacion superior

					$buffer2 = str_replace("<BR/>","", $buffer2);
					//$buffer2 = str_replace("<h4 align=right>","<h4 align=right><span style='font-size:13px;font-family:Arial'>", $buffer2); //achica letra al cliente tipo y sub
					$buffer2 = str_replace("<FONT COLOR=WHITE>*No Aplica</FONT>","<span style='font-size:11px;font-family:Arial'><FONT COLOR=BLACK>*No Aplica</FONT>", $buffer2); //modifico a Arial-8 la leyenda *No Aplica
					$posIni  = strpos($buffer2, "<h4 align=right>") + 16;
					$posFin  = strpos($buffer2, "</h4>");
					//$cuenta  = substr($buffer2, $posIni, $posFin-$posIni);
					$buffer2 = str_replace($cuenta, "", $buffer2);
					//echo "valor del buffer2....[$buffer2]";
					$Pagina = "<SCRIPT LANGUAGE='JavaScript'> function ventana() { var windowW = 800; var windowH = 450; var windowX = 10; var windowY = 10; var title = 'Recibo'; s = 'width='+windowW+',height='+windowH; cadena=\"<HTML><HEAD><link REL='STYLESHEET' HREF='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/recibos.css' TYPE='text/css'>";
					$Pagina = $Pagina."<SCRIPT LANGUAGE='JavaScript'> function window_onbeforeprint() { Imprimir.style.visibility = 'hidden'; Salir.style.visibility = 'hidden';} function window_onafterprint() { Imprimir.style.visibility = 'visible'; Salir.style.";
					$Pagina = $Pagina."visibility = 'visible'; }\"+\"<\"+\"/\"+\"SCRIPT></HEAD><BODY LANGUAGE='javascript' onbeforeprint='return window_onbeforeprint()' onafterprint='return window_onafterprint()'>";
					$Pagina = $Pagina."<INPUT type='button' value='Imprimir' id=Imprimir name=Imprimir onclick='window.print();'>&nbsp;<INPUT type='button' value=' Salir  ' id=Salir name=Salir onclick='window.close();'>";
					//$Pagina = $Pagina."<BR>";	//se comenta para tener más espacio
					$Pagina = $Pagina."<P><CENTER>";
					$Pagina = $Pagina."<IMG SRC='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/LOGO_BB.GIF' WIDTH=100 HEIGHT=50 ALIGN= 'TOP' Banco del Bajio>";
					$Pagina = $Pagina."</P></CENTER>";
					$Pagina = $Pagina."<H2><span style='font-size:13px;font-family:Arial;'>Consulta de CLABE y Tarjeta de Débito ".$cuenta."</H2>";
					//$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />"; //se comenta para tener más espacio
					//$Pagina = $Pagina."<hr noshade='noshade' size='4' color='#5A419c' />";
					$Pagina = $Pagina."<P ALIGN='RIGHT'><B><FONT>$date $time</FONT></B></P>";
					$Pagina = $Pagina.$buffer2;
					//$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />"; //se comenta para tener más espacio
					$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
					$Pagina = $Pagina."<p><FONT SIZE=1>Av. Manuel J. Clouthier No. 508 Col. Jardines del Campestre CP 37128 León, Gto.</FONT> </p>";
					$Pagina = $Pagina."<p><FONT SIZE=1>Consultas y aclaraciones 01 (477) 710- 4600 o al 01 (800) 471-0400</FONT></p>";
					$Pagina = $Pagina."<p><FONT SIZE=1>EL ÚNICO COMPROBANTE OFICIAL ES EL ESTADO DE CUENTA QUE EMITE BANCO DEL BAJIO.</FONT></p>";
					$Pagina = $Pagina."</BODY></HTML>\"; NFW =";
					$Pagina = $Pagina." window.open(\"\",'Recibo',\"toolbar=no,menubar=no,scrollbars=2,\"+s); NFW.blur(); NFW.resizeTo(windowW,windowH); NFW.moveTo(windowX,windowY); NFW.";
					$Pagina = $Pagina."document.open(); NFW.document.write(cadena); NFW.document.close(); NFW.window.focus(); } </SCRIPT>";
					$t->set_var( "transaccion_buffer", $Pagina.$transaccion_buffer );

					//echo "Imprimiendo el buffer2.....[$transaccion_buffer]";

					$t->pparse( "output", "clabe_tpl" );
}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/clabe/" );

    $t->pparse( "output", "user_login_tpl" );
}

?>