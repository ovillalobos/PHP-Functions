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
include_once( "ezreimp/classes/ezreimp.php" ); //DBA Reimpresion de Comprobantes

$user =& eZUserBB::currentUser();

// DebugBreak();

if ( $user )
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "inversion.php" );

    $t->setAllStrings();

	// AGG I 25Nov2005 Para poner fecha y hora en los comprobantes
	$dateTime  = new eZDateTime( );
	$timeStamp = $dateTime->timeStamp();
	$time =& date('H:i:s', $timeStamp );
	$date =& date('jMY', $timeStamp );
	// AGG F 25Nov2005 Para poner fecha y hora en los comprobantes

    $t->set_file( array(
        "inversion_tpl" => "inversion.tpl"
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
    $Pagina = "";
//HB
	if(!empty($_POST['TipoPzo']))
		$TipoPzo = $_POST['TipoPzo'];
//HB
    if(empty($_POST['Access'])) {
        $_POST['Access'] = "FrAc";
    }
    switch($_POST['Access']) {
    case "FrAc":
        $particularFields = "";
        $tr->blog($qki,"Inversion", $_POST['TipoPzo'], $_POST['DiasPzo'], $_POST['Amount'], $_POST['RenCap'], $_POST['RenInt']);
        break;
    case "Confirm":
        $particularFields = "&TipoPzo=".urlencode($_POST['TipoPzo'])."&DiasPzo=".urlencode($_POST['DiasPzo'])."&Amount=".urlencode($_POST['Amount'])."&RenCap=".urlencode($_POST['RenCap'])."&RenInt=".urlencode($_POST['RenInt'])."&ToAccount=".urlencode($_POST['ToAccount'])."&FrAccount=".urlencode($_POST['FrAccount'])."&TrAccount=".urlencode($_POST['TrAccount'])."&Nomben1=".urlencode($_POST['Nomben1'])."&Nomben2=".urlencode($_POST['Nomben2'])."&Nomben3=".urlencode($_POST['Nomben3'])."&Parent1=".urlencode($_POST['Parent1'])."&Parent2=".urlencode($_POST['Parent2'])."&Parent3=".urlencode($_POST['Parent3'])."&Porcen1=".urlencode($_POST['Porcen1'])."&Porcen2=".urlencode($_POST['Porcen2'])."&Porcen3=".urlencode($_POST['Porcen3'])."&Button=".urlencode($_POST['Button']);
        $tr->blog($qki,"Inversion", $_POST['TipoPzo'], $_POST['DiasPzo'], $_POST['Amount'], $_POST['RenCap'], $_POST['RenInt']);
        break;
    case "Process":
        $particularFields = "&Passwd=".encrypt( $_POST['code'], strtolower( $usr ) )."&TipoPzo=".urlencode($_POST['TipoPzo'])."&DiasPzo=".urlencode($_POST['DiasPzo'])."&Amount=".urlencode($_POST['Amount'])."&RenCap=".urlencode($_POST['RenCap'])."&RenInt=".urlencode($_POST['RenInt'])."&ToAccount=".urlencode($_POST['ToAccount'])."&FrAccount=".urlencode($_POST['FrAccount'])."&TrAccount=".urlencode($_POST['TrAccount'])."&Nomben1=".urlencode($_POST['Nomben1'])."&Nomben2=".urlencode($_POST['Nomben2'])."&Nomben3=".urlencode($_POST['Nomben3'])."&Parent1=".urlencode($_POST['Parent1'])."&Parent2=".urlencode($_POST['Parent2'])."&Parent3=".urlencode($_POST['Parent3'])."&Porcen1=".urlencode($_POST['Porcen1'])."&Porcen2=".urlencode($_POST['Porcen2'])."&Porcen3=".urlencode($_POST['Porcen3'])."&Button=".urlencode($_POST['Button']);
        $tr->blog($qki,"Inversion", $_POST['TipoPzo'], $_POST['DiasPzo'], $_POST['Amount'], $_POST['RenCap'], $_POST['RenInt']);
        break;
    }
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=pzo&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // Inversion

	// JAC 17JUL2012 INI
	if($TipoPzo == "CDEPEURO") {
		$fund = "€";
	} else if($TipoPzo == "CDEPUSD") {
		$fund = "USD";
	} else {
		$fund = "$";
	}
	// JAC 17JUL2012 FIN
	// AGG I 25Nov2005 Generacion de comprobantes
	if ($_POST['Access'] == "Process" and strpos($transaccion_buffer,"No. de Autoriza") != false)
	{
		$buffer2 = str_replace(chr(34),chr(92).chr(34),$transaccion_buffer);
		$buffer2 = substr($buffer2,0,strpos($buffer2,"(Por favor anote el "));
		
		//JAG 20jun2011 inicio
		$buffer2 = str_replace( "La apertura ha sido procesada exitosamente.", "", $buffer2 );
		//$transaccion_buffer = str_replace( "La apertura ha sido procesada exitosamente.", "", $transaccion_buffer );
		//JAG 20jun2011 fin
		//var_dump ( $date);

		$ini =& INIFile::globalINI();
		$SecureServer = $ini->read_var( "site", "SecureServer" );
		$SecureServerType = $ini->read_var( "site", "SecureServerType" );
		$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );
		$ServerNumber = $ini->read_var( "site", "ServerNumber" );

		if ( $_POST['Comprobante'] == "Activo" )
		{
		$Pagina = "<SCRIPT LANGUAGE='JavaScript'> function ventana() { var windowW = 800; var windowH = 450; var windowX = 10; var windowY = 10; var title = 'Recibo'; s = 'width='+windowW+',height='+windowH; cadena=\"<HTML><HEAD><link REL='STYLESHEET' HREF='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/recibos.css' TYPE='text/css'><TITLE>Inversion</TITLE>";//ó
		$Pagina = $Pagina."<SCRIPT LANGUAGE='JavaScript'> function window_onbeforeprint() { Imprimir.style.visibility = 'hidden'; Salir.style.visibility = 'hidden';} function window_onafterprint() { Imprimir.style.visibility = 'visible'; Salir.style.";
		$Pagina = $Pagina."visibility = 'visible'; }\"+\"<\"+\"/\"+\"SCRIPT></HEAD><BODY LANGUAGE='javascript' onbeforeprint='return window_onbeforeprint()' onafterprint='return window_onafterprint()'>";
		$Pagina = $Pagina."<INPUT type='button' value='Imprimir' id=Imprimir name=Imprimir onclick='window.print();'>&nbsp;<INPUT type='button' value=' Salir  ' id=Salir name=Salir onclick='window.close();'>";
		$Pagina = $Pagina."<BR>";
		$Pagina = $Pagina."<P><CENTER>";
		$Pagina = $Pagina."<IMG SRC='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/LOGO_BB.GIF' ALIGN= 'TOP' Banco del Bajio>";
		$Pagina = $Pagina."</P></CENTER>";
		$Pagina = $Pagina."<H2>Inversion</H2>";//ó
		$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
		$Pagina = $Pagina."<P ALIGN='RIGHT'><B><FONT>$date $time</FONT></B></P>";
		//JAG 20jun2011 inicio
		if($_POST['RenCap'] == "Transferencia"){			
			$Pagina = $Pagina."<P>La apertura ha sido procesada exitosamente.</FONT></P>";
			$Pagina = $Pagina."<P>Con Transferencia de capital a su cuenta <B>".$_POST['FrAccount']."</B></P>";
			$Pagina = $Pagina."<P>Con Transferencia de intereses a su cuenta <B>".$_POST['ToAccount']."</B></P>";
			//$Pagina = $Pagina."<P>Por un monto de $<B>".$_POST['Amount']."</B></P>";
    			$Pagina = $Pagina."<P>Por un monto de $<B>".number_format($_POST['Amount'],2)."</B></P>";//DMOS formato de moneda
			$Pagina = $Pagina."<P>Los recursos se tomarán de su cuenta <B>".$_POST['TrAccount']."</B></P>";
		}
		else if($_POST['RenCap'] == "Renovacion"){
			$Pagina = $Pagina."<P>La apertura ha sido procesada exitosamente.</FONT></P>";
			$Pagina = $Pagina."<P>Con Transferencia de capital a su cuenta <B>".$_POST['FrAccount']."</B></P>";
			$Pagina = $Pagina."<P>Con Transferencia de intereses a su cuenta <B>".$_POST['ToAccount']."</B></P>";
			//$Pagina = $Pagina."<P>Por un monto de $<B>".$_POST['Amount']."</B></P>";
                        $Pagina = $Pagina."<P>Por un monto de $<B>".number_format($_POST['Amount'],2)."</B></P>";//DMOS formato de moneda
			$Pagina = $Pagina."<P>Los recursos se tomarán de su cuenta <B>".$_POST['TrAccount']."</B></P>";
		}
		//JAG 20jun2011 fin
		$Pagina = $Pagina.$buffer2;
		$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
		$Pagina = $Pagina."<p><FONT SIZE=1>Av. Manuel J. Clouthier No. 508 Col. Jardines del Campestre CP 37128 León, Gto.</FONT> </p>";
		$Pagina = $Pagina."<p><FONT SIZE=1>Consultas y aclaraciones 01 (477) 710- 4600 o al 01 (800) 471-0400</FONT></p>";
		$Pagina = $Pagina."<p><FONT SIZE=1>LA TRANSFERENCIA A LA QUE HACE REFERENCIA ESTE COMPROBANTE FUE REALIZADA CONFORME A LA INFORMACIÓN E INSTRUCCIONES QUE NUESTRO CLIENTE ALIMENTÓ EN EL SISTEMA DE BANCO DEL BAJÍO, POR LO TANTO EL CLIENTE ES EL ÚNICO RESPONSABLE PRESENTE Y FUTURO POR CUALQUIER ERROR U OMISIÓN EN ESTA OPERACIÓN.</FONT></P>";
		$Pagina = $Pagina."<p><FONT SIZE=1>EL ÚNICO COMPROBANTE OFICIAL DE ESTA TRANSACCIÓN ES EL ESTADO DE CUENTA QUE EMITE BANCO DEL BAJIO.</FONT></p>";
		//DBA I 161170
		$Pagina = $Pagina."</br></br><p><h5>REGISTRO NACIONAL DE VALORES:</h5></p>";
		$Pagina = $Pagina."<p><h5>PRLV: 2439-4.30-1995-001</h5></p>";
		$Pagina = $Pagina."<p><h5>CEDES: 2439-4.20-1995-001</h5></p>";
		//DBA F 161170
		$Pagina = $Pagina."</BODY></HTML>\"; NFW =";
		$Pagina = $Pagina." window.open(\"\",'Recibo',\"toolbar=no,menubar=no,scrollbars=2,\"+s); NFW.blur(); NFW.resizeTo(windowW,windowH); NFW.moveTo(windowX,windowY); NFW.";
		$Pagina = $Pagina."document.open(); NFW.document.write(cadena); NFW.document.close(); NFW.window.focus(); } ventana(); </SCRIPT>";
		}
		//DBA Reimpresion de Comprobantes
		/*$FechaHora = $date . " " . $time;
		$QryRIC = new eZReImp( );
		$QryRIC -> store($_POST['CustID'],$_POST['TrAccount'],$buffer2,"pzo","Inversi&oacute;n",$FechaHora);*/
		//DBA Reimpresion de Comprobantes
	}
	//JAG 20jun2011 inicio
	if ($_POST['Access'] != "Process"){
		$t->set_var( "transaccion_buffer", $transaccion_buffer.$Pagina );
	}
	else if($_POST['Access'] == "Process" and strpos($transaccion_buffer,"No. de Autoriza") != false){
		$Pagina2 = "";
		if($_POST['RenCap'] == "Transferencia"){			
			$Pagina2 = $Pagina2."<P>La apertura ha sido procesada exitosamente.</FONT></P>";
			$Pagina2 = $Pagina2."<P>Con Transferencia de capital a su cuenta <B>".$_POST['FrAccount']."</B></P>";
			$Pagina2 = $Pagina2."<P>Con Transferencia de intereses a su cuenta <B>".$_POST['ToAccount']."</B></P>";
			//$Pagina2 = $Pagina2."<P>Por un monto de $<B>".$_POST['Amount']."</B></P>";
                        $Pagina2 = $Pagina2."<P>Por un monto de $<B>".number_format($_POST['Amount'],2)."</B></P>"; //DMOS FROMATO DE MONTO
			$Pagina2 = $Pagina2."<P>Los recursos se tomarán de su cuenta <B>".$_POST['TrAccount']."</B></P>";
		}
		else if($_POST['RenCap'] == "Renovacion"){
			$Pagina2 = $Pagina2."<P>La apertura ha sido procesada exitosamente.</FONT></P>";
			$Pagina2 = $Pagina2."<P>Con Transferencia de capital a su cuenta <B>".$_POST['FrAccount']."</B></P>";
			$Pagina2 = $Pagina2."<P>Con Transferencia de intereses a su cuenta <B>".$_POST['ToAccount']."</B></P>";
			//$Pagina2 = $Pagina2."<P>Por un monto de $<B>".$_POST['Amount']."</B></P>";
                        $Pagina2 = $Pagina2."<P>Por un monto de $<B>".number_format($_POST['Amount'],2)."</B></P>"; //DMOS FROMATO DE MONTO
			$Pagina2 = $Pagina2."<P>Los recursos se tomarán de su cuenta <B>".$_POST['TrAccount']."</B></P>";
		}
		$transaccion_buffer = str_replace( "La apertura ha sido procesada exitosamente.", "", $transaccion_buffer );
		$t->set_var( "transaccion_buffer", $Pagina2.$transaccion_buffer.$Pagina );
	}
	//JAG 20jun2011 fin
	// JAC 31AGO2011 - Correccion INI
	else {
		$t->set_var("transaccion_buffer", $transaccion_buffer.$Pagina);
	}
	// JAC 31AGO2011 - Correccion FIN
	// AGG F 25Nov2005 Generacion de comprobantes

    //$t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "inversion_tpl" );

}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/inversion/" );

    $t->pparse( "output", "user_login_tpl" );
}
?>
