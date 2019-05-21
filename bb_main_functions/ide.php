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

$user =& eZUserBB::currentUser();

// DebugBreak();

if ( $user )
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "ide.php" );

    $t->setAllStrings();

	// AGG I 25Nov2005 Para poner fecha y hora en los comprobantes
	$dateTime  = new eZDateTime( );
	$timeStamp = $dateTime->timeStamp();
	$time =& date('H:i:s', $timeStamp );
	$date =& date('jMY', $timeStamp );
	// AGG F 25Nov2005 Para poner fecha y hora en los comprobantes

    $t->set_file( array(
        "IDE_tpl" => "IDE.tpl"
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
		
	if(!empty($_POST['Amount']))
		$Amount = $_POST['Amount'];
		
	if(!empty($_POST['DiasPzo']))
		$DiasPzo = $_POST['DiasPzo'];
		
	if(!empty($_POST['FrAccount']))
		$FrAccount = $_POST['FrAccount'];
		
	if(!empty($_POST['ToAccount']))
		$ToAccount = $_POST['ToAccount'];
		
	if(!empty($_POST['RFC']))
		$RFC = $_POST['RFC'];
		
	if(!empty($_POST['RenInt']))
		$RenInt = $_POST['RenInt'];
		
	if(!empty($_POST['Desc']))
		$Desc = $_POST['Desc'];
		
	if(!empty($_POST['Accion']))
		$Accion = $_POST['Accion'];
		
	if(!empty($_POST['Day']))
		$Day = $_POST['Day'];
		
	if(!empty($_POST['Month']))
		$Month = $_POST['Month'];
		
	if(!empty($_POST['code']))
		$code = $_POST['code'];
		
	if(!empty($_POST['Comprobante']))
		$Comprobante = $_POST['Comprobante'];
//HB
    if(empty($Access)) {
        $Access = "FrAc";
    }
    // $Accion = "transferencia";
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $Amount ) ) {
    	$Amount = "";
    }
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $DiasPzo ) ) {
    	$DiasPzo = "";
    }
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $FrAccount ) ) {
    	$FrAccount = "";
    }
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $ToAccount ) ) {
    	$ToAccount = "";
    }
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $RFC ) ) {
    	$RFC = "";
    }
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $RenInt ) ) {
    	$RenInt = "";
    }
    if( isset /*(HB AGL - Ajustes PHP5)*/( $Desc ) ) {
       $Desc = preg_replace("/</", " ", $Desc );	 /* HB AGL*/
       $Desc = preg_replace("/>/", " ", $Desc );	 /* HB AGL*/
       $Desc = preg_replace("/&/", "-", $Desc );	 /* HB AGL*/
    }
     switch($Access) {
    case "FrAc":
		$particularFields = "&ToAccount=".urlencode("IDE"); //YEHO
        $tr->blog($qki,"Transferencia", $FrAccount, $ToAccount, $DiasPzo, $Amount, $Accion);
        break;
    case "Confirm":

$pos = strpos($Amount, ".00");
if (!is_integer($pos))
{
    	$pos = strpos($Amount, ".");
    	if ( is_integer($pos) )
    		$Amount = "0";
    		//$Amount = substr($Amount,0,$pos);
}
    	$pos = strpos($Amount, "-");
		if ( is_integer($pos) )
			$Amount = "0";
    		//$Amount = str_replace("-", "", $Amount);

		if (!is_numeric($Amount))
			$Amount = "0";

			//$Amount = (int) preg_replace("/[^0-9]/", "", $Amount);


		$particularFields = "&Day=".urlencode($Day)."&Month=".urlencode($Month)."&FrAccount=".urlencode($FrAccount)."&ToAccount=".urlencode("IDE")."&Amount=".urlencode($Amount)."&RFC=".urlencode($RFC)."&RenInt=".urlencode($RenInt);
        $tr->blog($qki,"Transferencia", $FrAccount, $ToAccount, $DiasPzo, $Amount, $Accion);
        break;
    case "Process":
		$particularFields = "&Passwd=".encrypt( $code, strtolower( $usr ) )."&Day=".urlencode($Day)."&Month=".urlencode($Month)."&FrAccount=".urlencode($FrAccount)."&ToAccount=".urlencode("IDE")."&Amount=".urlencode($Amount)."&RFC=".urlencode($RFC)."&RenInt=".urlencode($RenInt);
        $tr->blog($qki,"Transferencia", $FrAccount, $ToAccount, $DiasPzo, $Amount, $Accion);
        break;
    }
    $transaccion_buffer = "";
    // DebugBreak();

    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=trn&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // transferencia

	// AGG I 25Nov2005 Generacion de comprobantes
	if ($Access == "Process" and $Comprobante == "Activo" and strpos($transaccion_buffer,"No. de Autoriza") != false)
	{
		$buffer2 = str_replace(chr(34),chr(92).chr(34),$transaccion_buffer);

		$buffer2 = substr($buffer2,0,strpos($buffer2,"(Por favor Anote el "));
		//var_dump ( $date);

		$ini =& INIFile::globalINI();
		$SecureServer = $ini->read_var( "site", "SecureServer" );
		$SecureServerType = $ini->read_var( "site", "SecureServerType" );
		$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );
		$ServerNumber = $ini->read_var( "site", "ServerNumber" );

		$Pagina = "<SCRIPT LANGUAGE='JavaScript'> function ventana() { var windowW = 800; var windowH = 450; var windowX = 10; var windowY = 10; var title = 'Recibo'; s = 'width='+windowW+',height='+windowH; cadena=\"<HTML><HEAD><link REL='STYLESHEET' HREF='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/recibos.css' TYPE='text/css'><TITLE>Transferencias</TITLE>";
		$Pagina = $Pagina."<SCRIPT LANGUAGE='JavaScript'> function window_onbeforeprint() { Imprimir.style.visibility = 'hidden'; Salir.style.visibility = 'hidden';} function window_onafterprint() { Imprimir.style.visibility = 'visible'; Salir.style.";
		$Pagina = $Pagina."visibility = 'visible'; }\"+\"<\"+\"/\"+\"SCRIPT></HEAD><BODY LANGUAGE='javascript' onbeforeprint='return window_onbeforeprint()' onafterprint='return window_onafterprint()'>";
		$Pagina = $Pagina."<INPUT type='button' value='Imprimir' id=Imprimir name=Imprimir onclick='window.print();'>&nbsp;<INPUT type='button' value=' Salir  ' id=Salir name=Salir onclick='window.close();'>";
		$Pagina = $Pagina."<BR>";
		$Pagina = $Pagina."<P><CENTER>";
		$Pagina = $Pagina."<IMG SRC='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/LOGO_BB.GIF' ALIGN= 'TOP' Banco del Bajio>";
		$Pagina = $Pagina."</P></CENTER>";
		$Pagina = $Pagina."<H2>Transferencia</H2>";
		$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
		$Pagina = $Pagina."<P ALIGN='RIGHT'><B><FONT>$date $time</FONT></B></P>";
		$Pagina = $Pagina.$buffer2;
		$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
		$Pagina = $Pagina."<p><FONT SIZE=1>Av. Manuel J. Clouthier No. 508 Col. Jardines del Campestre CP 37128 León, Gto.</FONT> </p>";
		$Pagina = $Pagina."<p><FONT SIZE=1>Consultas y aclaraciones 01 (477) 710- 4600 o al 01 (800) 471-0400</FONT></p>";
		$Pagina = $Pagina."<p><FONT SIZE=1>LA TRANSFERENCIA A LA QUE HACE REFERENCIA ESTE COMPROBANTE FUE REALIZADA CONFORME A LA INFORMACIÓN E INSTRUCCIONES QUE NUESTRO CLIENTE ALIMENTÓ EN EL SISTEMA DE BANCO DEL BAJÍO, POR LO TANTO EL CLIENTE ES EL ÚNICO RESPONSABLE PRESENTE Y FUTURO POR CUALQUIER ERROR U OMISIÓN EN ESTA OPERACIÓN.</FONT></P>";
		$Pagina = $Pagina."<p><FONT SIZE=1>EL ÚNICO COMPROBANTE OFICIAL DE ESTA TRANSACCIÓN ES EL ESTADO DE CUENTA QUE EMITE BANCO DEL BAJIO.</FONT></p>";
		$Pagina = $Pagina."</BODY></HTML>\"; NFW =";
		$Pagina = $Pagina." window.open(\"\",'Recibo',\"toolbar=no,menubar=no,scrollbars=2,\"+s); NFW.blur(); NFW.resizeTo(windowW,windowH); NFW.moveTo(windowX,windowY); NFW.";
		$Pagina = $Pagina."document.open(); NFW.document.write(cadena); NFW.document.close(); NFW.window.focus(); } ventana(); </SCRIPT>";
	}
    $t->set_var( "transaccion_buffer", $transaccion_buffer.$Pagina );
	// AGG F 25Nov2005 Generacion de comprobantes


    //$t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "IDE_tpl" );

}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    //$t->set_var( "redirect_url", "/transaccion/transferencia/" );
	$t->set_var( "redirect_url", "/transaccion/ide/" );

    $t->pparse( "output", "user_login_tpl" );
}
?>