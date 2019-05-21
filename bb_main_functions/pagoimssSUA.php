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
include_once( "ezreimp/classes/ezreimp.php" ); //DBA Reimpresion de Comprobantes
include_once( "eztransaccion/classes/eztransaccion.php" );
include_once( "ezsession/classes/ezsession.php" );

//echo "estoy en pagouimsssua $Accion"  ;
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

//print("Ya subi el archivo");
if ( $user )
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "pagoimssSUA.php" );

    $t->setAllStrings();

    $t->set_file( array("pagoimssSUA_tpl" => "pagoimssSUA.tpl") );


    $session =& eZSession::globalSession();

    if ( !$session->fetch() )   {  $session->store();    }

    $tr		= new eZTransaccion( );
    $usr	= $session->variable( "r_usr"  );
    $qki	= $session->variable( "r_qki"  );
    $priv	= $session->variable( "r_priv" );

	$particularFields = "";

  if($Access == "FrAc") { $Access = "Confirm";     }     //YEHO 161406 30Ago2010

	if(empty($Access)) {  $Access = "FrAc";    }

	$Accion = "ArchSUA";
	
	if(!empty($_POST['FrAccount']))
		$FrAccount = $_POST['FrAccount'];

    if ( !isset /*(HB AGL - Ajustes PHP5)*/( $FrAccount ) ) { $FrAccount	= "";		}
    if ( !isset /*(HB AGL - Ajustes PHP5)*/( $Parent1	) ) { $Parent1		= "";		}
    if ( !isset /*(HB AGL - Ajustes PHP5)*/( $ToAccount ) ) { $ToAccount	= "";		}

	$DiasPzo = "pagoimssSUA-";

//	echo("<hr><p>DGM <b>pagoimssSUA.php</b>  entrando a Access = $Access</p>");
//  echo("Comparando firstacces... $result, [$servicio-$QueAccess-$QueAccion-$Accion] ACCES  $Access [$FrAccount]");
    if ( $servicio  == "pagoimssSUA" and $QueAccess == "FrAc" and $QueAccion == "ArchSUA" and $result == "00" )
    {
      $Access =   "FrAc";
    }

	//lamch 180949 ini

//    if (( $QueAccess == "Confirm" and $Accion == "ArchSUA" and $Access == "Confirm"  and $result == "0"  ) and ( $FrAccount == "" or $FrAccount == "~f"))
    if (( $Accion == "ArchSUA" and $Access == "Confirm"  and $result == "0"  ) and ( $FrAccount == "" or $FrAccount == "~f"))
    {
      $Access =   "Process";
    }

	//lamch 180949 fin

    switch($Access) {
    case "FrAc":
			/*	echo ("Ejecutando comando ");

				$retorno = -1;
				//$command = "/var/www/html/bajio/eztransaccion/user/./HolaMundo";
				$command = "/var/www/html/bajio/eztransaccion/classes/./suvC";
				passthru($command,$retorno);

				echo ("retorno = $retorno ");*/
//				echo("Comparando firstacces... $result, [$servicio-$QueAccess-$QueAccion] ACCES  $Access");
        if ( $servicio  == "pagoimssSUA" and $QueAccess == "FrAc" and $QueAccion == "ArchSUA" and $result == "00" )
        {
          $Access =   "FrAc";
          $Extension = "pagoimssSUA-".$Regpatronal.$Perpago.$FolioSUA;
		   $_SESSION['folioVal'] = $_POST['folioVal']; //YEHO 181415
		$folioVal = $_SESSION['folioVal'];
		$RenCap = $folioVal;
		  $Extension = $Extension. $RenCap;  //YEHO 181415
    //      ECHO "<br><b>YEHO FRACCSextension</b> $Extension";
   		    $particularFields = "&Passwd=".encrypt( $_POST['code'], strtolower( $usr ) )."&Accion=".urlencode($Accion)."&Parent1=0&DiasPzo=".urlencode($Extension)."&gene=2first";
   		   			//	$particularFields = "&Day=".urlencode($Day)."&Month=".urlencode($Month)."&CadPriv=".urlencode($CadPriv)."&FrAccount=".urlencode($FrAccount)."&Empresa=".urlencode($Empresa)."&TeleB=".urlencode($TeleB)."&Amount=".urlencode($Amount)."&RenCap=".urlencode($RenCap)."&PlazaB=".urlencode($PlazaB)."&RFC=".urlencode($RFC);
          $tr->blog($qki,"pagoimsssua", $FrAccount, $Empresa, $Amount, $Day, $RenCap );
          $Access =   "Confirm";
//          ECHO "Ya le puse confirm en pagoimsssua";
        }
        else
        {
		 $_SESSION['folioVal'] = $_POST['folioVal'];  //YEHO 181415
		$folioVal = $_SESSION['folioVal'];
		$RenCap = $folioVal;
		  $Extension = $Extension. $RenCap;  //YEHO 181415
         include_once( "eztransaccion/user/myfileupload2.php" );
//         echo ("<br>YEHOX pagoimssSUA.php estoy en selecciona archivo p validar11");
        }


				return;
				break;
		case "Confirm":
		 $_SESSION['folioVal'] = $_POST['folioVal'];  //YEHO 181415
		$folioVal = $_SESSION['folioVal'];
		$_SESSION['rowno'] = $_POST['rowno'];  //YEHO 181415
		$rowno = $_SESSION['rowno'];
		$_SESSION['fraccount'] = $_POST['FrAccount']; //YEHO 181415
		$fraccount = $_SESSION['fraccount']; //YEHO 189716
		//echo "rowno guiardando el  [$rowno]";
//      ECHO "<br>YEHO FROMACCT [$FrAccount]";
		$RenCap = $folioVal;
		$Extension = $Extension. $RenCap;
       $particularFields = "&Passwd=".encrypt( $_POST['code'], strtolower( $usr ) )."&Accion=".urlencode($Accion)."&Parent1=0&DiasPzo=".urlencode($Extension)."&FrAccount=".urlencode($FrAccount)."&RenCap=".urlencode($RenCap);   //YEHO 181415
   		   //$particularFields = "&Passwd=".encrypt( $_POST['code'], strtolower( $usr ) )."&Accion=".urlencode($Accion)."&Parent1=0&DiasPzo=".urlencode($Extension);    //YEHO este no muestra la cuenta origen
   		   			//	$particularFields = "&Day=".urlencode($Day)."&Month=".urlencode($Month)."&CadPriv=".urlencode($CadPriv)."&FrAccount=".urlencode($FrAccount)."&Empresa=".urlencode($Empresa)."&TeleB=".urlencode($TeleB)."&Amount=".urlencode($Amount)."&RenCap=".urlencode($RenCap)."&PlazaB=".urlencode($PlazaB)."&RFC=".urlencode($RFC);
        $tr->blog($qki,"pagoimsssua", $FrAccount, $Empresa, $Amount, $Day, $RenCap, $PlazaB );


        break;
    case "Process":
		$Parent1 = 0;	//resultado de la validación
		$RenCap = $folioVal; //YEHO 181415
	//	ECHO "RenCapque mando [$RenCap] folioVal[$folioVal]";
		$fraccount = $_SESSION['fraccount'];
		$num_folio = $_SESSION['num_folio'];
		$rowno = $_SESSION['rowno'];
//    echo ("<br>YEHO pagoimssSUA.php estoy en PROCESS");
		//$Extension = "pagoimssSUA-".$ArchivoSUA.$Regpatronal.$RFC.$Perpago.$FolioSUA.$Nombre.$Monto;
		//$Extension = "pagoimssSUA-".$ArchivoSUA.$Regpatronal.$RFC.$Perpago.$FolioSUA.$Nombre.$Monto.$num_folio;
  	//$particularFields = "&Passwd=".encrypt( $_POST['code'], strtolower( $usr ) )."&Accion=".urlencode($Accion)."&Parent1=0&DiasPzo=".urlencode($Extension);
		$Extension = "pagoimssSUA-".$ArchivoSUA.$Regpatronal.$RFC.$Perpago.$FolioSUA.$Nombre.$Monto.$RenCap;
		$Extension = $Extension. "#" . $rowno . "$";
		//$particularFields = "&Passwd=".encrypt( $_POST['code'], strtolower( $usr ) )."&Accion=".urlencode($Accion)."&Parent1=0&DiasPzo=".urlencode($Extension)."&RenCap=".urlencode($RenCap); //YEHO 181415
		$particularFields = "&Passwd=".encrypt( $_POST['code'], strtolower( $usr ) )."&Accion=".urlencode($Accion)."&Parent1=0&DiasPzo=".urlencode($Extension)."&RenCap=".urlencode($RenCap)."&FrAccount=".urlencode($fraccount); //YEHO 189716
		//$tr->blog($qki,"Transferencia", $FrAccount, $ToAccount, $DiasPzo, $Amount, $Accion);

		$transaccion_buffer = "";

		//echo ("HACIENDO POSTTO HOST");
		//$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=arc&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // intercambio de archivos de nómina electrónica


		$t->set_var( "transaccion_buffer", $transaccion_buffer);

		break;
    }

    //YEHO IMSS 161406 I
    $transaccion_buffer = "";
//    ECHO "<br>-----enviando  [$Access]------";
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=arc&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer);

   // ECHO "YEHO TRANSACCION BUFFER [$transaccion_buffer]";
//   echo "IMPRIMIENDO";
    //YEHO IMSS 161406 F
          /////////////////////////////////  comprobante pop up
  /*  if ( $ret_code == 0 and $Access == "Process" )
		{

			$ini =& INIFile::globalINI();

			$transaccion_buffer= str_replace("ponFecha",date("d.m.Y"),$transaccion_buffer);
			$transaccion_buffer= str_replace("ponHora",date("H:i:s"),$transaccion_buffer);

			$posInicio  = strpos ( $transaccion_buffer, "<!-- INICIO COMPROBANTE -->");
			$posFin		= strpos ( $transaccion_buffer, "<!-- FIN COMPROBANTE -->"   ) + strlen("<!-- FIN COMPROBANTE -->");
			$Pagina		= "";

				$buffer2 = substr($transaccion_buffer, $posInicio, ($posFin - $posInicio));

				$SecureServer	  = $ini->read_var( "site", "SecureServer" );
				$SecureServerType = $ini->read_var( "site", "SecureServerType" );
				$DomainPostfix	  = $ini->read_var( "site", "DomainPostfix" );
				$ServerNumber	  = $ini->read_var( "site", "ServerNumber" );

				$Pagina = "<SCRIPT LANGUAGE='JavaScript'> function ventana() { var windowW = 800; var windowH = 450; var windowX = 10; var windowY = 10; var title = 'Recibo'; s = 'width='+windowW+',height='+windowH; cadena=\"<HTML><HEAD><link REL='STYLESHEET' HREF='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/recibos.css' TYPE='text/css'><TITLE>Carátula Resúmen de Validación</TITLE>";
				$Pagina = $Pagina."<SCRIPT LANGUAGE='JavaScript'> function window_onbeforeprint() { Imprimir.style.visibility = 'hidden'; Salir.style.visibility = 'hidden';} function window_onafterprint() { Imprimir.style.visibility = 'visible'; Salir.style.";
				$Pagina = $Pagina."visibility = 'visible'; }\"+\"<\"+\"/\"+\"SCRIPT></HEAD><BODY LANGUAGE='javascript' onbeforeprint='return window_onbeforeprint()' onafterprint='return window_onafterprint()'>";
				$Pagina = $Pagina."<INPUT type='button' value='Imprimir' id=Imprimir name=Imprimir onclick='window.print();'>&nbsp;<INPUT type='button' value=' Salir  ' id=Salir name=Salir onclick='window.close();'>";
				$Pagina = $Pagina."<BR>";
				$Pagina = $Pagina."<P><CENTER>";
				$Pagina = $Pagina."<IMG SRC='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/sitedesign/bajionet/images/logobb.gif' ALIGN= 'TOP' Banco del Bajio>";
				$Pagina = $Pagina."</P></CENTER>";
				$Pagina = $Pagina."<h5>Pago Cuota obrero Patronal</h5>"; //YEHO 27sep10
				$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
				$Pagina = $Pagina."<P ALIGN='RIGHT'><B><FONT>$date $time</FONT></B></P>";
				$Pagina = $Pagina.$buffer2;
				$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
				$Pagina = $Pagina."</BODY></HTML>\"; NFW =";
				$Pagina = $Pagina." window.open(\"\",'Recibo',\"toolbar=no,menubar=no,scrollbars=2,\"+s); NFW.blur(); NFW.resizeTo(windowW,windowH); NFW.moveTo(windowX,windowY); NFW.";
				$Pagina = $Pagina."document.open(); NFW.document.write(cadena); NFW.document.close(); NFW.window.focus(); } ventana(); </SCRIPT>";

    //YEHO 161406 Reimpresion de comprobantes
	//$posInicio = strpos ($transaccion_buffer, "Cuenta Origen:</FONT> </B> </TD><TD VALIGN = CENTER ALIGN = LEFT   > <FONT SIZE = 2 FACE='verdana, arial, helvetica, sans-serif'>");
	$posInicio = strpos ($transaccion_buffer, "Cuenta Origen:</FONT> </B> </TD>					<TD VALIGN = CENTER ALIGN = LEFT   > <FONT SIZE = 2 FACE='verdana, arial, helvetica, sans-serif'>"); //YEHO 182299 15Feb2011
	//$str_len = strlen("Cuenta Origen:</FONT> </B> </TD><TD VALIGN = CENTER ALIGN = LEFT   > <FONT SIZE = 2 FACE='verdana, arial, helvetica, sans-serif'>");
	$str_len = strlen("Cuenta Origen:</FONT> </B> </TD>					<TD VALIGN = CENTER ALIGN = LEFT   > <FONT SIZE = 2 FACE='verdana, arial, helvetica, sans-serif'>"); //YEHO 182299 15Feb2011

	$cadena = substr ($transaccion_buffer, $posInicio + $str_len);
	//$posFin = strpos($cadena, "</FONT> </TD></TR><TR><TD ALIGN  = RIGHT> <B> <FONT SIZE = 2 FACE='verdana, arial, helvetica, sans-serif'>Monto Pagado:");
	$posFin = strpos($cadena, "</FONT> </TD>				</TR>				<TR>					<TD ALIGN  = RIGHT> <B> <FONT SIZE = 2 FACE='verdana, arial, helvetica, sans-serif'>Monto Pagado:"); //YEHO 182299 15Feb2011
	$datoscuen = substr ($cadena,0, $posFin );
	$FrAccount = $datoscuen;


        list($cust,$type, $sub) = explode(" - ", $FrAccount);	//YEHO 182299 15Feb2011
    $FechaHora = $date . " " . $time;
		$QryRIC = new eZReImp( );

		//$QryRIC -> store($cust,$FrAccount,$buffer2,"arc","Pago IMSS SUA",$FechaHora);
		$QryRIC -> storeSUA($cust,$type,$sub,$transaccion_buffer,"SUA","Pago IMSS SUA",$FechaHora);

			$t->set_var( "transaccion_buffer", $transaccion_buffer.$Pagina );
		$t->pparse( "output", "ArchServ_tpl" );
		}
		*/
          ///////////////////////////////////
    $t->set_var( "transaccion_buffer", $transaccion_buffer.$Pagina );
    $t->pparse( "output", "pagoimssSUA_tpl" );
    //YEHO IMSS 161406 F


    if ( $GLOBALS["DEBUGI"] == true ) {
		$log = "Included files in " . realpath(__FILE__) . "  ->" . print_r( get_included_files(), true ) . "|";
		eZLog::writeNotice( $log );
		define_syslog_variables();
		openlog( "IVA", LOG_PID | LOG_PERROR, LOG_DAEMON );
		syslog( LOG_WARNING, "$log" );
		closelog();
	}

}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array("user_login_tpl" => "userlogin.tpl" ) );

    $t->set_var( "redirect_url", "/transaccion/pagoimssSUA/" );
    $t->pparse( "output", "user_login_tpl" );
    if ( $GLOBALS["DEBUGI"] == true ) {
		$log = "Included files in " . realpath(__FILE__) . "  ->" . print_r( get_included_files(), true ) . "|";
		eZLog::writeNotice( $log );
		define_syslog_variables();
		openlog( "IVA", LOG_PID | LOG_PERROR, LOG_DAEMON );
		syslog( LOG_WARNING, "$log" );
		closelog();
	}
}

?>