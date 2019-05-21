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
include_once( "pear/SOAP/Client.php" );

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
$procesarWebservicesUser     =  $ini->read_var( "site", "procesarWebservicesUser" );
$procesarWebservicesPassword =  $ini->read_var( "site", "procesarWebservicesPassword" );
$procesarWebservicesBaseURLTra  =  $ini->read_var( "site", "procesarWebservicesBaseURLTra" );
$procesarWebservicesBaseURL  =  $ini->read_var( "site", "procesarWebservicesBaseURL" );
$BridgeWebServiceURL         =  $ini->read_var( "site", "BridgeWebServiceURL" );

$backend = "gw" . $ServerNumber. $DomainPostfix;

include_once( "ezuserbb/classes/ezuser.php" );

$user =& eZUserBB::currentUser();

// DebugBreak();

if ( $user )
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "traspaso.php" );

    $t->setAllStrings();

	$dateTime  = new eZDateTime( );
	$timeStamp = $dateTime->timeStamp();
	$time =& date('H:i:s', $timeStamp );
	$date =& date('jMY', $timeStamp );

    $t->set_file( array(
        "traspaso_tpl" => "traspaso.tpl"
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
    if(!empty($_POST['Access']))
        $Access = $_POST['Access'];
    if( !empty /*(HB AGL - Ajustes PHP5)*/( $_POST['ImpTipo']) ) { 	$ImpTipo	= $_POST['ImpTipo'];		}
    if( !empty /*(HB AGL - Ajustes PHP5)*/( $_POST['PlazaB']	) ) { 	$PlazaB	= $_POST['PlazaB'];		}//CALLE
    if( !empty /*(HB AGL - Ajustes PHP5)*/( $_POST['emailej']) ) { 	$emailej	= $_POST['emailej'];		}//DELEGACION
    if( !empty /*(HB AGL - Ajustes PHP5)*/( $_POST['gene4']	) ) { 	$gene4	= $_POST['gene4'];		}//NO EXT
    if( !empty /*(HB AGL - Ajustes PHP5)*/( $_POST['gene5']	) ) { 	$gene5	= $_POST['gene5'];		}//NO INT
    if( !empty /*(HB AGL - Ajustes PHP5)*/( $_POST['plaza2']	) ) { 	$plaza2	= $_POST['plaza2'];		}//COLONIA
    if( !empty /*(HB AGL - Ajustes PHP5)*/( $_POST['plaza3']	) ) { 	$plaza3	= $_POST['plaza3'];		}//CIUDAD
    if( !empty /*(HB AGL - Ajustes PHP5)*/( $_POST['dirbene']) ) { 	$dirbene	= $_POST['dirbene'];		}//ESTADO
    if( !empty /*(HB AGL - Ajustes PHP5)*/( $_POST['Nomben1']) ) { 	$Nomben1	= $_POST['Nomben1'];		}//CP
    if( !empty /*(HB AGL - Ajustes PHP5)*/( $_POST['Nomben2']) ) { 	$Nomben2	= $_POST['Nomben2'];		}//TEL CASA
    if( !empty /*(HB AGL - Ajustes PHP5)*/( $_POST['Nomben3']) ) { 	$Nomben3	= $_POST['Nomben3'];		}//TEL OFICINA
    if( !empty /*(HB AGL - Ajustes PHP5)*/( $_POST['RenInt'] ) ) { 	$RenInt	= $_POST['RenInt'];		}//Fecha
    if( !empty /*(HB AGL - Ajustes PHP5)*/( $_POST['RenCap'] ) ) { 	$RenCap	= $_POST['RenCap'];		}//ext, dia y hora casa y ofna
    if( !empty /*(HB AGL - Ajustes PHP5)*/( $_POST['Parent1']) ) { 	$Parent1	= $_POST['Parent1'];		}//celular


    if( !isset /*(HB AGL - Ajustes PHP5)*/( $ImpTipo) ) { 	$ImpTipo	= "";		}
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $PlazaB	) ) { 	$PlazaB	= "";		}//CALLE
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $emailej) ) { 	$emailej	= "";		}//DELEGACION
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $gene4	) ) { 	$gene4	= "";		}//NO EXT
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $gene5	) ) { 	$gene5	= "";		}//NO INT
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $plaza2	) ) { 	$plaza2	= "";		}//COLONIA
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $plaza3	) ) { 	$plaza3	= "";		}//CIUDAD
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $dirbene) ) { 	$dirbene	= "";		}//ESTADO
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $Nomben1) ) { 	$Nomben1	= "";		}//CP
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $Nomben2) ) { 	$Nomben2	= "";		}//TEL CASA
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $Nomben3) ) { 	$Nomben3	= "";		}//TEL OFICINA
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $RenInt ) ) { 	$RenInt	= "";		}//Fecha
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $RenCap ) ) { 	$RenCap	= "";		}//ext, dia y hora casa y ofna
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $Parent1) ) { 	$Parent1	= "";		}//celular



    if(empty($Access)) {
        $Access = "FrAc";
    }
    switch($Access) {
    case "FrAc":
        $particularFields = ""."&PlazaB=".($PlazaB) ."&plaza2=".$plaza2 ."&plaza3=".$plaza3 ."&dirbene=".$dirbene ."&Nomben1=".$Nomben1 ."&Nomben2=".$Nomben2 ."&Nomben3=".$Nomben3 . "&gene4=".$gene4."&gene5=".$gene5."&emailej=".$emailej."&Parent1=".$Parent1."&RenCap=".$RenCap;
        $tr->blog($qki,"Traspaso", $Access, $Accion);
        break;
    case "Confirm":
		$particularFields = "&desc1=".($desc1) ."&ImpTipo=".($ImpTipo)."&PlazaB=".($PlazaB) ."&plaza2=".$plaza2 ."&plaza3=".$plaza3 ."&dirbene=".$dirbene ."&Nomben1=".$Nomben1 ."&Nomben2=".$Nomben2 ."&Nomben3=".$Nomben3 . "&gene4=".$gene4."&gene5=".$gene5."&emailej=".$emailej."&RenCap=".$RenCap."&RenInt=".$fecha_probable_liquidacion."&Parent1=".$Parent1;
//    var_dump($particularFields);
/*
    $pag_sb  = "<script language='javascript'> ";
    $anios = $ImpTipo;
    if ($anios >=1 && $anios <= 26)
    {
     $sb = $ini->read_var( "site", "consarURLSB5" );
    }
    else
    {
     if ($anios >=27 && $anios <= 36)
      {
       $sb = $ini->read_var( "site", "consarURLSB4" );
      }
     else
      {
       if ($anios >=37 && $anios <= 45)
        {
         $sb = $ini->read_var( "site", "consarURLSB3" );
        }
       else
        {
         if ($anios >=46 && $anios <= 55)
          {
           $sb = $ini->read_var( "site", "consarURLSB2" );
          }
         else
          {
             $sb = $ini->read_var( "site", "consarURLSB1" );
          }
        }
      }
    }
  $pag_sb .= "  xpos=screen.width-380; ypos=0; window.open('". $sb . "','Cuadro_comparativo_de_Afores','fullscreen=no,scrollbars=no,resizable=no,status=no,menubar=no,width=370,height=285, top=' +ypos+ ',left=' +xpos+'')";
  $pag_sb .= "		</script> ";
  echo $pag_sb;
*/
		$tr->blog($qki,"Traspaso", $desc1,$Access, $anios, $Accion);
        break;
    case "Process":
		$url   = $procesarWebservicesServer .$procesarWebservicesBaseURLTra . 'ServiceTraspasosEV';

		if ( strlen( $Pos ) > 16 ) {
			$Pos = substr( $Pos,  0, 16 );
		} else {
			$Pos = str_pad( $Pos, 16, "0" );
		}

		$datos = new SOAP_Client( $BridgeWebServiceURL );
		$datos->setOpt( 'timeout', 240);
    $method = 'traspasar';

    $folio    = $desc1;
    $folioAct = $Pos;

    $extCasa = str_replace('*', '', substr( $RenCap, 0, 5));
    $diaCasa = str_replace('*', '', substr( $RenCap, 5, 1));
    $horaCasa = str_replace('*', '', substr( $RenCap, 6, 8));

    $extOfna = str_replace('*', '', substr( $RenCap, 14, 5));
    $diaOfna = str_replace('*', '', substr( $RenCap, 19, 1));
    $horaOfna = str_replace('*', '', substr( $RenCap, 20, 8));

$Nomben2 = trim($Nomben2);
$Nomben3 = trim($Nomben3);
$Parent1 = trim($Parent1);

$telCasa = $Nomben2;
$telOfna = $Nomben3;
		if(strlen($Parent1) > 0 && strlen($Nomben2) > 0 && strlen($Nomben3) > 0)// 3
		{
			$telCasa = $Parent1;
			$telOfna = $Nomben3;
		}
		else
		{
			if(strlen($Parent1) > 0)// tiene cel
			{
				$telCasa = $Parent1;
				if(strlen($Nomben3) > 0)// tiene ofna
					{
						$telOfna	  = $Nomben3;
					}
				else
					{
						$telOfna	  = $Nomben2;
					}
			}
			else//no tiene cel
			{
				$telCasa = $Nomben2;
        $telOfna = $Nomben3;
			}
		}
 		$params = array( 'user' => $procesarWebservicesUser, 'passwd' => $procesarWebservicesPassword, 'url' => $url, 'folio' => $folio, 'folioAct' => $folioAct, 'calle' => $PlazaB, 'numExt' => $gene4, 'numInt' => $gene5, 'colonia' => $plaza2, 'delegacion' => $plaza3, 'entidad' => $dirbene, 'codigoPostal' => $Nomben1, 'telCasa' => str_replace("-","",str_replace(")","",str_replace("(","",$telCasa))), 'telOficina' => str_replace("-","",str_replace(")","",str_replace("(","",$telOfna))), 'extCasa' => $extCasa, 'diaCasa' => $diaCasa, 'horaCasa' => $horaCasa, 'extOfna' => $extOfna, 'diaOfna' => $diaOfna, 'horaOfna' => $horaOfna);

		if ( $GLOBALS["DEBUGA"] == true ) {

				eZLog::writeNotice( "En traspaso de eztransaccion/user traspaso (endpoint) ->" . print_r( $url, true ) . "|" );
				eZLog::writeNotice( "En traspaso de eztransaccion/user traspaso (peticion) ->" . print_r( $params, true ) . "|" );
				eZLog::writeNotice( "En traspaso de eztransaccion/user traspaso (method) ->" . print_r( $method, true ) . "|" );
		}

		//AGG 27JUL2006 FUNCIONALIDAD PARA IMPRIMIR EN BITACORA LA HORA INICIAL Y FINAL DE LAS LLAMADAS A LOS WEB SERVICES
		$timeini		    = date( "H:i:s" );
		list( $usec, $sec )	= explode( " ", microtime() );
		$usini				= substr( $usec, 2);
		//AGG 27JUL2006 FUNCIONALIDAD PARA IMPRIMIR EN BITACORA LA HORA INICIAL Y FINAL DE LAS LLAMADAS A LOS WEB SERVICES

	$ans = $datos->call( $method, $params );

		//AGG 27JUL2006 FUNCIONALIDAD PARA IMPRIMIR EN BITACORA LA HORA INICIAL Y FINAL DE LAS LLAMADAS A LOS WEB SERVICES
		$timefin		    = date( "H:i:s" );
		list( $usec, $sec )	= explode( " ", microtime() );
		$usfin				= substr( $usec, 2);
		//AGG 27JUL2006 FUNCIONALIDAD PARA IMPRIMIR EN BITACORA LA HORA INICIAL Y FINAL DE LAS LLAMADAS A LOS WEB SERVICES

//		$ans = '911&01&9600008909&';
    if ( is_object($ans) )
      {
        //eZLog::writeNotice( "Error: PROCESAR NO Regreso Respuesta en TRASPASO|" . $folio . "|" . $ans->message . "|" . date( "H:i:s" ));	MAOS Oct2013 Quitar Notice
      }
		if ( strpos($ans, '01&')=== false  && strpos($ans, '02&') === false )
		{
			 if (strpos($ans, 'TIMED') === false and strpos($ans, 'TIMEOUT') === false )
			{
				$respuesta = "99"; //error de conexion
				sendmail( $ErrEMail1, "BajíoNET. Error Servidor ProceSAR de BajíoNET no está respondiendo.\r\n\r\nFavor de avisar a los responsables." );
				//eZLog::writeNotice( "En activacion de eztransaccion/user consulta datos no hay conexion con ProceSAR" );	MAOS Oct2013 Quitar Notice
			}
		   	else
			{
				$respuesta = "98"; //tiempo de respuesta excedido
			}
      str_replace('&', ' ', $ans->message);
			$particularFields ="&DiasPzo="."00"."&Porcen3=".($respuesta)."&RenInt=".($RenInt)."&desc1=".($desc1)."&Desc=".$Desc."&Pos=".($Pos)."&PAN=".($nss) ."&PlazaB=".($PlazaB) ."&plaza2=".$plaza2 ."&plaza3=".$plaza3 ."&dirbene=".$dirbene ."&Nomben1=".$Nomben1 ."&Nomben2=".$Nomben2 ."&Nomben3=".$Nomben3 . "&gene4=".$gene4."&gene5=".$gene5."&emailej=".$emailej."&RenCap=".$RenCap."&RenInt=".$fecha_probable_liquidacion."&Parent1=".$Parent1;
			$tr->blog($qki,"Consulta",$respuesta,$timeini.$usini,$timefin.$usfin, $ans->message,$Parent1, $RenInt, $desc1, $Desc,$Accion);
			break;
		}

		if ( $GLOBALS["DEBUGA"] == true ) {
				eZLog::writeNotice( "En activacion de eztransaccion/user activacion (ans) ->" . print_r( $ans, true ) . "|" );
		}
    	list( $motivo, $respuesta, $folio_traspaso, $fecha_probable_liquidacion ) = explode( "&", $ans );
		if ( $GLOBALS["DEBUGA"] == true ) {
				eZLog::writeNotice( "En traspaso de eztransaccion/user (ans) ->" . print_r( $ans, true ) . "|" );
				eZLog::writeNotice( "En traspaso de eztransaccion/user (respuesta) ->" . print_r( $respuesta, true ) . "|" );
				eZLog::writeNotice( "En traspaso de eztransaccion/user (motivo) ->" . print_r( $motivo, true ) ."|" );
				eZLog::writeNotice( "En traspaso de eztransaccion/user (folio_traspaso) ->" . print_r( $folio_traspaso, true ) ."|" );
				eZLog::writeNotice( "En traspaso de eztransaccion/user (fecha_probable_liquidacion) ->" . print_r( $fecha_probable_liquidacion, true ) ."|" );
		}
		$particularFields ="&DiasPzo=".($respuesta)."&ImpTipo=".($folio_traspaso)."&RenInt=".($fecha_probable_liquidacion)."&desc1=".($desc1)."&Desc=".($motivo)."&Pos=".$Pos."&PAN=".$nss."&PlazaB=".$PlazaB ."&plaza2=".$plaza2 ."&plaza3=".$plaza3 ."&dirbene=".$dirbene ."&Nomben1=".$Nomben1 ."&Nomben2=".$Nomben2 ."&Nomben3=".$Nomben3 . "&gene4=".$gene4."&gene5=".$gene5."&emailej=".$emailej."&RenCap=".$RenCap."&Parent1=".$Parent1;
		$tr->blog($qki,"Traspaso", $respuesta, $folio_traspaso, $fecha_probable_liquidacion, $desc1, $motivo,$Pos, $Accion);
		break;

    }
    $transaccion_buffer = "";

    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=tra&Access=".($Access)."&CustID=".($usr)."&Cadpriv=".($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // Traspaso

	if ($Access == "Process" and $respuesta == "01")
	{
		$buffer2 = str_replace(chr(34),chr(92).chr(34),$transaccion_buffer);
		$Pagina = "<SCRIPT LANGUAGE='JavaScript'> function ventana() { var windowW = 800; var windowH = 450; var windowX = 10; var windowY = 10; var title = 'Recibo'; s = 'width='+windowW+',height='+windowH; cadena=\"<HTML><HEAD><link REL='STYLESHEET' HREF='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/recibos.css' TYPE='text/css'><TITLE>Comprobante Traspaso AFORE AFIRME BAJIO.</TITLE>";
		$Pagina = $Pagina."<SCRIPT LANGUAGE='JavaScript'> function window_onbeforeprint() { Imprimir.style.visibility = 'hidden'; Salir.style.visibility = 'hidden';} function window_onafterprint() { Imprimir.style.visibility = 'visible'; Salir.style.";
		$Pagina = $Pagina."visibility = 'visible'; }\"+\"<\"+\"/\"+\"SCRIPT></HEAD><BODY LANGUAGE='javascript' onbeforeprint='return window_onbeforeprint()' onafterprint='return window_onafterprint()'>";
		$Pagina = $Pagina."<INPUT type='button' value='Imprimir' id=Imprimir name=Imprimir onclick='window.print();'>&nbsp;<INPUT type='button' value=' Salir  ' id=Salir name=Salir onclick='window.close();'>";
		$Pagina = $Pagina."<BR>";
		$Pagina = $Pagina."<P><CENTER>";
		$Pagina = $Pagina."<IMG SRC='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/LOGO_BB.GIF' ALIGN= 'TOP' Banco del Bajio>";
		$Pagina = $Pagina."</P></CENTER>";
		$Pagina = $Pagina."<H2>Comprobante Traspaso AFORE AFIRME BAJIO</H2>";
		$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
		$Pagina = $Pagina."<P ALIGN='RIGHT'><B><FONT>$date $time</FONT></B></P>";
		$Pagina = $Pagina.$buffer2;
		$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
		$Pagina = $Pagina."<p><FONT SIZE=1>Av. Manuel J. Clouthier No. 508 Col. Jardines del Campestre CP 37128 León, Gto.</FONT> </p>";
		$Pagina = $Pagina."<p><FONT SIZE=1>Consultas y aclaraciones 01 (477) 710- 4600 o al 01 (800) 471-0400</FONT></p>";
		$Pagina = $Pagina."<p><FONT SIZE=1>LA TRANSFERENCIA A LA QUE HACE REFERENCIA ESTE COMPROBANTE FUE REALIZADA CONFORME A LA INFORMACIÓN E INSTRUCCIONES QUE NUESTRO CLIENTE ALIMENTÓ EN EL SISTEMA DE BANCO DEL BAJÍO, POR LO TANTO EL CLIENTE ES EL ÚNICO RESPONSABLE PRESENTE Y FUTURO POR CUALQUIER ERROR U OMISIÓN EN ESTA OPERACIÓN.</FONT></P>";
		$Pagina = $Pagina."</BODY></HTML>\"; NFW =";
		$Pagina = $Pagina." window.open(\"\",'Recibo',\"toolbar=no,menubar=no,scrollbars=2,\"+s); NFW.blur(); NFW.resizeTo(windowW,windowH); NFW.moveTo(windowX,windowY); NFW.";
		$Pagina = $Pagina."document.open(); NFW.document.write(cadena); NFW.document.close(); NFW.window.focus(); } ventana(); </SCRIPT>";
	}
    $t->set_var( "transaccion_buffer", $transaccion_buffer.$Pagina );

    $t->pparse( "output", "traspaso_tpl" );
}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/traspaso/" );

    $t->pparse( "output", "user_login_tpl" );
}
?>