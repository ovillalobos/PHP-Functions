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
                         "eztransaccion/user/intl/", $Language, "sipare.php.ini" );

    $t->setAllStrings();

	// AGG I 25Nov2005 Para poner fecha y hora en los comprobantes
	$dateTime  = new eZDateTime( );
	$timeStamp = $dateTime->timeStamp();
	$time =& date('H:i:s', $timeStamp );
	$date =& date('jMY', $timeStamp );
	// AGG F 25Nov2005 Para poner fecha y hora en los comprobantes

    $t->set_file( array(
        "sipare_tpl" => "sipare.tpl"
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
	if(!empty($_POST['Amount']))
		$Amount = $_POST['Amount'];
	if(!empty($_POST['Access']))
		$Access = $_POST['Access'];
		
	if(!empty($_POST['Day']))
		$Day = $_POST['Day'];
		
	if(!empty($_POST['Month']))
		$Month = $_POST['Month'];
		
	if(!empty($_POST['Error']))
		$Error = $_POST['Error'];
		
	if(!empty($_POST['FrAccount']))
		$FrAccount = $_POST['FrAccount'];
		
	if(!empty($_POST['RenCap']))
		$RenCap = $_POST['RenCap'];
		
	if(!empty($_POST['Button']))
		$Button = $_POST['Button'];
		
	if(!empty($_POST['CadPriv']))
		$CadPriv = $_POST['CadPriv'];
		
	if(!empty($_POST['RFC']))
		$RFC = $_POST['RFC'];
		
	if(!empty($_POST['Parent1']))
		$Parent1 = $_POST['Parent1'];
		
	if(!empty($_POST['Parent2']))
		$Parent2 = $_POST['Parent2'];
		
	if(!empty($_POST['Parent3']))
		$Parent3 = $_POST['Parent3'];
		
	if(!empty($_POST['gene4']))
		$gene4 = $_POST['gene4'];
		
	if(!empty($_POST['gene5']))
		$gene5 = $_POST['gene5'];
		
	if(!empty($_POST['PAN']))
		$PAN = $_POST['PAN'];
			
	if(!empty($_POST['Porcen2']))
		$Porcen2 = $_POST['Porcen2'];
		
	if(!empty($_POST['Nomben3']))
		$Nomben3 = $_POST['Nomben3'];
		
	if(!empty($_POST['Nomben2']))
		$Nomben2 = $_POST['Nomben2'];
		
	if(!empty($_POST['code']))
		$code = $_POST['code'];
		
	if(!empty($_POST['Empresa']))
		$Empresa = $_POST['Empresa'];
		
	if(!empty($_POST['TeleB']))
		$TeleB = $_POST['TeleB'];
		
	if(!empty($_POST['PlazaB']))
		$PlazaB = $_POST['PlazaB'];
		
	if(!empty($_POST['ValEsp']))
		$ValEsp = $_POST['ValEsp'];
		
	if(!empty($_POST['Recargo']))
		$Recargo = $_POST['Recargo'];
			
	if(!empty($_POST['Descuento']))
		$Descuento = $_POST['Descuento'];
//HB 
   
    if(empty($Access)) {
        $Access = "FrAc";
    }
    // $Accion = "pagoservicios";
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $Amount ) ) {
    	$Amount = "";
    }
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $Day ) ) {
    	$Day = "";
    }
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $Empresa ) ) {
    	$Empresa = "";
    }
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $FrAccount ) ) {
    	$FrAccount = "";
    }
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $RenCap ) ) {
    	$RenCap = "";
    }
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $CadPriv ) ) {
    	$CadPriv = "";
    }
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $RFC ) ) {
    	$RFC = "";
    }

	if( !isset /*(HB AGL - Ajustes PHP5)*/( $Parent1 ) ) {
    	$Parent1 = "";
    }

	if( !isset /*(HB AGL - Ajustes PHP5)*/( $Parent2 ) ) {
    	$Parent2 = "";
    }

	if( !isset /*(HB AGL - Ajustes PHP5)*/( $Parent3 ) ) {
    	$Parent3 = "";
    }


	if( !isset /*(HB AGL - Ajustes PHP5)*/( $gene4 ) ) {
    	$gene4 = "";
    }


	if( !isset /*(HB AGL - Ajustes PHP5)*/( $gene5 ) ) {
    	$gene5 = "";
    }

	if( !isset /*(HB AGL - Ajustes PHP5)*/( $PAN ) ) {
    	$PAN = "";
    }

	if( !isset /*(HB AGL - Ajustes PHP5)*/( $Porcen2 ) ) {
    	$Porcen2 = "";
    }
//	Nomben3

	if( !isset /*(HB AGL - Ajustes PHP5)*/( $Nomben3 ) ) {
    	$Nomben3 = "";
    }

	if( !isset /*(HB AGL - Ajustes PHP5)*/( $Nomben2 ) ) {
    	$Nomben2 = "";
    }

    switch($Access) {
    case "FrAc":
        $particularFields = "";
        $tr->blog($qki,"PagoServicios", $FrAccount, $Empresa, $Amount, $Day, $RenCap );
        break;
    case "Confirm":
		// ************************************************************
		// DGM I 09Mar2008 No validar monto en tenencias gto
		if ( $Empresa == "Gobierno-Tenencias-Guanajuato  Guanajuato.-")
		{
			$Amount = 1;
		}
		// DGM F 09Mar2008 No validar monto en tenencias gto
		// ************************************************************
//		print $RenCap;
//		print $Access;
        $particularFields = "&Day=".urlencode($Day)."&Month=".urlencode($Month)."&CadPriv=".urlencode($CadPriv)."&FrAccount=".urlencode($FrAccount)."&Empresa=".urlencode($Empresa)."&TeleB=".urlencode($TeleB)."&Amount=".urlencode($Amount)."&RenCap=".urlencode($RenCap)."&PlazaB=".urlencode($PlazaB)."&RFC=".urlencode($RFC);
        $tr->blog($qki,"PagoServicios", $FrAccount, $Empresa, $Amount, $Day, $RenCap );
        break;
    case "Process":
//			print $Access;
//			print $Amount;
			$Amount = preg_replace(("/$/"), "", $Amount);   /* HB AGL*/
	//		print $Amount;
			$Amount = preg_replace(("/,/"), "", $Amount);   /* HB AGL*/
//			print $gene4;
//			print $Empresa;
        $particularFields = "&Passwd=".encrypt( $code, strtolower( $usr ) )."&Day=".urlencode($Day)."&Month=".urlencode($Month)."&CadPriv=".urlencode($CadPriv)."&FrAccount=".urlencode($FrAccount)."&TeleB=".urlencode($TeleB)."&Amount=".urlencode($Amount)."&RenCap=".urlencode($RenCap)."&PlazaB=".urlencode($PlazaB)."&RFC=".urlencode($RFC)."&ValEsp=".urlencode($ValEsp)."&Recargo=".urlencode($Recargo)."&Descuento=".urlencode($Descuento)."&Parent1=".urlencode($Parent1)."&Parent2=".urlencode($Parent2). "&gene4=".$gene4. "&gene5=".$gene5. "&Parent3=".$Parent3."&Empresa=".$Empresa."&PAN=".urlencode($PAN)."&Porcen2=".urlencode($Porcen2)."&Nomben3=".urlencode($Nomben3)."&Nomben2=".urlencode($Nomben2);
        $tr->blog($qki,"PagoServicios", $FrAccount, $Empresa, $Amount, $Day, $RenCap);
        break;
    }
    $transaccion_buffer = "";
    // DebugBreak();
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=sii&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // pago de servicios

	//aluna 19oct09 COMPROBANTES DE RECHAZO



	if (
         strpos($transaccion_buffer,"B17") != false or strpos($transaccion_buffer,"A33") != false
     )
  {
//   print_r("Amonos a la ...");
   $error_SIPI = 1;
//   break;
  }
  else
  {
  $error_SIPI = 0;
  }



//	print " error sipi [".$error_SIPI;
	//aluna 19oct09 COMPROBANTES DE RECHAZO


	if ($Access == "Confirm" and $error_SIPI == 1 )
	{
				$ini =& INIFile::globalINI();
		$SecureServer = $ini->read_var( "site", "SecureServer" );
		$SecureServerType = $ini->read_var( "site", "SecureServerType" );
		$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );
		$ServerNumber = $ini->read_var( "site", "ServerNumber" );

		$buffer2 = $transaccion_buffer;

		$Pagina = "<SCRIPT LANGUAGE='JavaScript'> function ventana() { var windowW = 1200; var windowH = 500; var windowX = 10; var windowY = 10; var title = 'Recibo'; s = 'width='+windowW+',height='+windowH; cadena=\"<HTML><HEAD><link REL='STYLESHEET' HREF='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/recibos.css' TYPE='text/css'><TITLE>Pago de IMSS Sipare</TITLE>";
		$Pagina = $Pagina."<SCRIPT LANGUAGE='JavaScript'> function window_onbeforeprint() { Imprimir.style.visibility = 'hidden'; Salir.style.visibility = 'hidden';} function window_onafterprint() { Imprimir.style.visibility = 'visible'; Salir.style.";
		$Pagina = $Pagina."visibility = 'visible'; }\"+\"<\"+\"/\"+\"SCRIPT></HEAD><BODY LANGUAGE='javascript' onbeforeprint='return window_onbeforeprint()' onafterprint='return window_onafterprint()'>";
		$Pagina = $Pagina."<INPUT type='button' value='Imprimir' id=Imprimir name=Imprimir onclick='window.print();'>&nbsp;<INPUT type='button' value=' Salir  ' id=Salir name=Salir onclick='window.close();'>";
		$Pagina = $Pagina."<BR>";
		$Pagina = $Pagina."<P><CENTER>";
		$Pagina = $Pagina."<IMG SRC='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/logos.bmp' ALIGN= 'TOP' Banco del Bajio>";
		$Pagina = $Pagina."</P></CENTER>";
		$Pagina = $Pagina."<H2>Línea de captura Inválida</H2>";
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


if (
         strpos($transaccion_buffer,"857") != false or strpos($transaccion_buffer,"780") != false
     )
  {
//   print_r("Amonos a la ...");
   $error_SIPI = 2;
//   break;
  }
  else
  {
  $error_SIPI = 0;
  }


	if ($Access == "Confirm" and $error_SIPI == 2 )
	{
				$ini =& INIFile::globalINI();
		$SecureServer = $ini->read_var( "site", "SecureServer" );
		$SecureServerType = $ini->read_var( "site", "SecureServerType" );
		$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );
		$ServerNumber = $ini->read_var( "site", "ServerNumber" );

		$buffer2 = $transaccion_buffer;

		$Pagina = "<SCRIPT LANGUAGE='JavaScript'> function ventana() { var windowW = 1200; var windowH = 500; var windowX = 10; var windowY = 10; var title = 'Recibo'; s = 'width='+windowW+',height='+windowH; cadena=\"<HTML><HEAD><link REL='STYLESHEET' HREF='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/recibos.css' TYPE='text/css'><TITLE>Pago de IMSS Sipare</TITLE>";
		$Pagina = $Pagina."<SCRIPT LANGUAGE='JavaScript'> function window_onbeforeprint() { Imprimir.style.visibility = 'hidden'; Salir.style.visibility = 'hidden';} function window_onafterprint() { Imprimir.style.visibility = 'visible'; Salir.style.";
		$Pagina = $Pagina."visibility = 'visible'; }\"+\"<\"+\"/\"+\"SCRIPT></HEAD><BODY LANGUAGE='javascript' onbeforeprint='return window_onbeforeprint()' onafterprint='return window_onafterprint()'>";
		$Pagina = $Pagina."<INPUT type='button' value='Imprimir' id=Imprimir name=Imprimir onclick='window.print();'>&nbsp;<INPUT type='button' value=' Salir  ' id=Salir name=Salir onclick='window.close();'>";
		$Pagina = $Pagina."<BR>";
		$Pagina = $Pagina."<P><CENTER>";
		$Pagina = $Pagina."<IMG SRC='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/logos.bmp' ALIGN= 'TOP' Banco del Bajio>";
		$Pagina = $Pagina."</P></CENTER>";
		$Pagina = $Pagina."<H2>Línea de captura Vencida</H2>";
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




	// AGG I 25Nov2005 Generacion de comprobantes
	if ($Access == "Process" and strpos($transaccion_buffer,"Folio de Pago") != false ) //and $Comprobante == "Activo" and strpos($transaccion_buffer,"Folio de pago") != false)
	{
//		print $transaccion_buffer;
		$buffer2 = $transaccion_buffer;
//				$buffer2 = "<Table><TR><td>SIPARE</td></TR></Table>";

		//var_dump ( $date);

		$ini =& INIFile::globalINI();
		$SecureServer = $ini->read_var( "site", "SecureServer" );
		$SecureServerType = $ini->read_var( "site", "SecureServerType" );
		$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );
		$ServerNumber = $ini->read_var( "site", "ServerNumber" );

		$Pagina = "<SCRIPT LANGUAGE='JavaScript'> function ventana() { var windowW = 1200; var windowH = 500; var windowX = 10; var windowY = 10; var title = 'Recibo'; s = 'width='+windowW+',height='+windowH; cadena=\"<HTML><HEAD><link REL='STYLESHEET' HREF='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/recibos.css' TYPE='text/css'><TITLE>Pago de IMSS Sipare</TITLE>";
		$Pagina = $Pagina."<SCRIPT LANGUAGE='JavaScript'> function window_onbeforeprint() { Imprimir.style.visibility = 'hidden'; Salir.style.visibility = 'hidden';} function window_onafterprint() { Imprimir.style.visibility = 'visible'; Salir.style.";
		$Pagina = $Pagina."visibility = 'visible'; }\"+\"<\"+\"/\"+\"SCRIPT></HEAD><BODY LANGUAGE='javascript' onbeforeprint='return window_onbeforeprint()' onafterprint='return window_onafterprint()'>";
		$Pagina = $Pagina."<INPUT type='button' value='Imprimir' id=Imprimir name=Imprimir onclick='window.print();'>&nbsp;<INPUT type='button' value=' Salir  ' id=Salir name=Salir onclick='window.close();'>";
		$Pagina = $Pagina."<BR>";
		$Pagina = $Pagina."<P><CENTER>";
		$Pagina = $Pagina."<IMG SRC='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/logos.bmp' ALIGN= 'TOP' Banco del Bajio>";
		$Pagina = $Pagina."</P></CENTER>";
		$Pagina = $Pagina."<H2>PAGOS SIPARE/IMSS-INFONAVIT</H2>";
		$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
		$Pagina = $Pagina."<P ALIGN='RIGHT'><B><FONT>$date $time</FONT></B></P>";
		$Pagina = $Pagina.$buffer2;
		$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
		$Pagina = $Pagina."<p><FONT SIZE=1>Av. Manuel J. Clouthier No. 508 Jardines del Campestre CP 37128 León, Gto.</FONT> </p>";
		$Pagina = $Pagina."<p><FONT SIZE=1>Consultas y aclaraciones 01 (477) 710- 4600 </FONT></p>";
		$Pagina = $Pagina."<p><FONT SIZE=1>EL PAGO AL QUE HACE REFERENCIA ESTE COMPROBANTE FUE REALIZADO CONFORME A LAS  INSTRUCCIONES QUE NUESTRO CLIENTE ALIMENTÓ EN EL SISTEMA DE BANCO DEL BAJÍO, POR LO TANTO EL CLIENTE ES EL ÚNICO RESPONSABLE PRESENTE Y FUTURO POR CUALQUIER ERROR U OMISIÓN EN ESTA OPERACIÓN.</FONT></P>";
		$Pagina = $Pagina."<p><FONT SIZE=1>EL ÚNICO COMPROBANTE OFICIAL DE ESTA TRANSACCIÓN ES EL ESTADO DE CUENTA QUE EMITE BANCO DEL BAJIO.</FONT></p>";
		$Pagina = $Pagina."</BODY></HTML>\"; NFW =";
		$Pagina = $Pagina." window.open(\"\",'Recibo',\"toolbar=no,menubar=no,scrollbars=2,\"+s); NFW.blur(); NFW.resizeTo(windowW,windowH); NFW.moveTo(windowX,windowY); NFW.";
		$Pagina = $Pagina."document.open(); NFW.document.write(cadena); NFW.document.close(); NFW.window.focus(); } ventana(); </SCRIPT>";
	}
    $t->set_var( "transaccion_buffer", $transaccion_buffer.$Pagina );
	// AGG F 25Nov2005 Generacion de comprobantes

    //$t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "sipare_tpl" );
}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/sipare/" );

    $t->pparse( "output", "user_login_tpl" );
}
?>
