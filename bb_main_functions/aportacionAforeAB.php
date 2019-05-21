<?php
//
//
// Created on: <26-Feb-2007 17:37:53 DGM>
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
include_once( "classes/INIFile.php"		);
include_once( "classes/eztemplate.php"	);
include_once( "classes/ezhttptool.php"	);

include_once( "eztransaccion/classes/eztransaccion.php" );
include_once( "ezsession/classes/ezsession.php"			);
include_once( "classes/ezdatetime.php"					);
include_once( "eztransaccion/classes/encrypt.php"		);

$session =& eZSession::globalSession();

if( !$session->fetch() )
    $session->store();

$ini =& $GLOBALS["GlobalSiteIni"];

$Language		= $ini->read_var( "eZTransaccionMain", "Language" );
$ServerNumber	= $ini->read_var( "site", "ServerNumber" );
$DomainPostfix	= $ini->read_var( "site", "DomainPostfix" );

$backend = "gw" . $ServerNumber. $DomainPostfix;

include_once( "ezuserbb/classes/ezuser.php" );
include_once( "ezreimp/classes/ezreimp.php" ); //DBA Reimpresion de Comprobantes

$user =& eZUserBB::currentUser();

if ( $user )
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "aportacionAforeAB.php" );

    $t->setAllStrings();

	$dateTime	= new eZDateTime( );
	$timeStamp	= $dateTime->timeStamp();
	$time		=& date('H:i:s', $timeStamp );
	$date		=& date('jMY', $timeStamp );

    $t->set_file( array("aportacionAforeAB_tpl" => "aportacionAforeAB.tpl" ) );

    $session =& eZSession::globalSession();

    if ( !$session->fetch() )
    {
        $session->store();
    }

    $tr					= new eZTransaccion( );
    $usr				= $session->variable( "r_usr" );
    $qki				= $session->variable( "r_qki" );
    $priv				= $session->variable( "r_priv" );
    $transaccion_buffer = "";

    if( empty ( $_POST["Access"]     ) ) {	$_POST["Access"]		= "FrAc";   }
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $_POST["FrAccount"]	) ) { 	$_POST["FrAccount"]	= "";		}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $_POST["RenCap"]		) ) { 	$_POST["RenCap"]		= "";		}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $_POST["Amount"]		) ) {	$_POST["Amount"]		= "";		}
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $_POST["CadPriv"]	) ) { 	$_POST["CadPriv"]	= "";		}

	if( !isset /*(HB AGL - Ajustes PHP5)*/( $_POST["Empresa"]	) ) {	$_POST["Empresa"]	= "Afore Afirme Bajio";	}

    if( !isset /*(HB AGL - Ajustes PHP5)*/( $_POST["cbTipoAf"]	) ) { 	$_POST["cbTipoAf"]	= "";		}
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $_POST["cbTipoAp"]	) ) { 	$_POST["cbTipoAp"]	= "";		}
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $_POST["cbDedu"]	) ) { 	$_POST["cbDedu"]	= "";		}
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $_POST["plaza3"]	) ) { 	$_POST["plaza3"]	= "";		}


    switch( $_POST["Access"] )
	{
		case "FrAc":
			$particularFields = "&Empresa=".($_POST["Empresa"]);
			$tr->blog($qki,"PagoServicios", $_POST["FrAccount"], $_POST["Empresa"]);
			break;
		case "Confirm":
			$particularFields = "&CadPriv=".urlencode($_POST["CadPriv"])."&FrAccount=".urlencode($_POST["FrAccount"])."&Empresa=".($_POST["Empresa"])."&Amount=".urlencode($_POST["Amount"])."&RenCap=".urlencode($_POST["RenCap"])."&cbTipoAf=".($_POST["cbTipoAf"])."&cbTipoAp=".($_POST["cbTipoAp"])."&cbDedu=".($_POST["cbDedu"])."&plaza3=".($_POST["plaza3"]);
			$tr->blog($qki,"PagoServicios", $_POST["FrAccount"], $_POST["Empresa"], $_POST["Amount"], $_POST["RenCap"], $_POST["cbTipoAf"], $_POST["cbTipoAp"], $_POST["cbDedu"]);
			break;
		case "Process":
			$particularFields = "&CadPriv=".urlencode($_POST["CadPriv"])."&FrAccount=".urlencode($_POST["FrAccount"])."&Empresa=".($_POST["Empresa"])."&Amount=".urlencode($_POST["Amount"])."&RenCap=".urlencode($_POST["RenCap"])."&Passwd=".encrypt( $_POST["code"], strtolower( $usr ) )."&cbTipoAf=".($_POST["cbTipoAf"])."&cbTipoAp=".($_POST["cbTipoAp"])."&cbDedu=".($_POST["cbDedu"])."&plaza3=".($_POST["plaza3"]);
			$tr->blog($qki,"PagoServicios", $_POST["FrAccount"], $_POST["Empresa"], $_POST["Amount"], $_POST["RenCap"], $_POST["cbTipoAf"], $_POST["cbTipoAp"], $_POST["cbDedu"]);
			break;
    }

    $transaccion_buffer = "";
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=afr&Access=".urlencode($_POST["Access"])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer);

   //PARA EL COMPROBANTE

       if(strpos($transaccion_buffer,"<TD> 1-DEDUCIBLE DE IMPUESTOS   		</TD>") != false && strpos($transaccion_buffer,"<TD>  APORTACION COMPLEMENTARIA PARA EL RETIRO  		</TD>") != false)
      {
        $transaccion_buffer = str_replace("<TD> 1-DEDUCIBLE DE IMPUESTOS   		</TD>","<TD>  DEDUCIBLE DE IMPUESTOS </TD>", $transaccion_buffer);
      }

       if(strpos($transaccion_buffer,"<TD> 2-NO DEDUCIBLE DE IMPUESTOS   		</TD>") != false)
      {
        $transaccion_buffer = str_replace("<TD> 2-NO DEDUCIBLE DE IMPUESTOS   		</TD>","<TD>  NO DEDUCIBLE DE IMPUESTOS </TD>", $transaccion_buffer);
      }

       if(strpos($transaccion_buffer,"<TD>  Afore AB retendrá el 20 por ciento del monto de la aportación en caso de retirar antes de los 65 años de edad o de su pensión.  		</TD>") != false && strpos($transaccion_buffer,"<TD>  APORTACION COMPLEMENTARIA PARA EL RETIRO </TD>") != false)
      {
        $transaccion_buffer = str_replace("<TD>  Afore AB retendrá el 20 por ciento del monto de la aportación en caso de retirar antes de los 65 años de edad o de su pensión.  		</TD>","<TD></TD>", $transaccion_buffer);
      }


       if(strpos($transaccion_buffer,"<TR> <TD> <B>Tipo Aportacion :						</B></TD>") != false)
      {
        $transaccion_buffer = str_replace("<TR> <TD> <B>Tipo Aportacion :						</B></TD>","<TR VALIGN=\"TOP\"> <TD> <B>Tipo Aportacion :						</B></TD>", $transaccion_buffer);
      }

       if(strpos($transaccion_buffer,"<TR> <TD> <B>Deducibilidad :					</B></TD>") != false)
      {
        $transaccion_buffer = str_replace("<TR> <TD> <B>Deducibilidad :					</B></TD>","<TR VALIGN=\"TOP\"> <TD> <B>Deducibilidad :					</B></TD>", $transaccion_buffer);
      }


    if(strpos($transaccion_buffer,"<TD>  APORTACION VOLUNTARIA CORTO PLAZO (NO DEDUCIBLE DE  		</TD>") != false )
      {
        $transaccion_buffer = str_replace("<TD>  APORTACION VOLUNTARIA CORTO PLAZO (NO DEDUCIBLE DE  		</TD>","<TD>APORTACI&Oacute;N VOLUNTARIA CORTO PLAZO.<BR/>Podrá ser retirada a los 6 meses del primer depósito o último retiro parcial. Posterior a su disposición deberá esperar 6 meses para realizar un siguiente retiro del fondo elegido.</TD>", $transaccion_buffer);
      }

      if(strpos($transaccion_buffer,"<TD>  APORTACION VOLUNTARIA LARGO PLAZO (DEDUCIBLE DE IM  		</TD>") != false )
      {
        $transaccion_buffer = str_replace("<TD>  APORTACION VOLUNTARIA LARGO PLAZO (DEDUCIBLE DE IM  		</TD>","<TD>APORTACI&Oacute;N VOLUNTARIA LARGO PLAZO.<BR/>Podrá ser retirada a los 6 meses del primer depósito o último retiro parcial. Posterior a su disposición deberá esperar 6 meses para realizar un siguiente retiro del fondo elegido.</TD>", $transaccion_buffer);

 $transaccion_buffer = str_replace("<TD> 1-DEDUCIBLE DE IMPUESTOS   		</TD>","<TD><font color=Red><i><b>  DEDUCIBLE DE IMPUESTOS <BR/>Por disposición fiscal Afore Afirme Bajío retendrá el 20 por <BR/>ciento del monto en caso de realizar un retiro antes de los <BR/>65 años de edad o no contar con pensión por invalidez total</b></i></font></TD>", $transaccion_buffer);

      }

 if(strpos($transaccion_buffer,"<TD>  APORTACION COMPLEMENTARIA PARA EL RETIRO  		</TD>") != false )
      {
        $transaccion_buffer = str_replace("<TD>  APORTACION COMPLEMENTARIA PARA EL RETIRO  		</TD>","<TD>APORTACI&Oacute;N COMPLEMENTARIA PARA EL RETIRO. <BR/><I><B><font color=Red>Este tipo de aportación s&oacute;lo permite realizar<BR/>retiros hasta contar con  65 años de edad o con<BR/>una pensión por invalidez total.</font></B></I></TD>", $transaccion_buffer);

      }


    if(strpos($transaccion_buffer,"<TD>  1-IMSS  		</TD>") != false )
      {
        $transaccion_buffer = str_replace("<TD>  1-IMSS  		</TD>","<TD>  IMSS </TD>", $transaccion_buffer);
      }

     if(strpos($transaccion_buffer,"<TD>  2-ISSSTE  		</TD>") != false )
      {
        $transaccion_buffer = str_replace("<TD>  2-ISSSTE  		</TD>","<TD>  ISSSTE </TD>", $transaccion_buffer);
      }

     if(strpos($transaccion_buffer,"<TD>  3-INDEPENDIENTE  		</TD>") != false )
      {
        $transaccion_buffer = str_replace("<TD>  3-INDEPENDIENTE  		</TD>","<TD>  INDEPENDIENTE </TD>", $transaccion_buffer);
      }

	// AGG I 25Nov2005 Generacion de comprobantes
	if ($_POST["Access"] == "Process" and strpos($transaccion_buffer,"No. de Autoriza") != false)
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
				$buffer4 = substr($transaccion_buffer,strpos($transaccion_buffer,"No. de Autoriza"));
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

		$ini =& INIFile::globalINI();
		$SecureServer		= $ini->read_var( "site", "SecureServer"	 );
		$SecureServerType	= $ini->read_var( "site", "SecureServerType" );
		$DomainPostfix		= $ini->read_var( "site", "DomainPostfix"	 );
		$ServerNumber		= $ini->read_var( "site", "ServerNumber"	 );





		if ( $_POST["Comprobante"] == "Activo" )
		{
		$Pagina = "<SCRIPT LANGUAGE='JavaScript'> function ventana() { var windowW = 800; var windowH = 450; var windowX = 10; var windowY = 10; var title = 'Recibo'; s = 'width='+windowW+',height='+windowH; cadena=\"<HTML><HEAD><link REL='STYLESHEET' HREF='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/recibos.css' TYPE='text/css'><TITLE>Aportaci&oacute;n Voluntaria</TITLE>";
		$Pagina = $Pagina."<SCRIPT LANGUAGE='JavaScript'> function window_onbeforeprint() { Imprimir.style.visibility = 'hidden'; Salir.style.visibility = 'hidden';} function window_onafterprint() { Imprimir.style.visibility = 'visible'; Salir.style.";
		$Pagina = $Pagina."visibility = 'visible'; }\"+\"<\"+\"/\"+\"SCRIPT></HEAD><BODY LANGUAGE='javascript' onbeforeprint='return window_onbeforeprint()' onafterprint='return window_onafterprint()'>";
		$Pagina = $Pagina."<INPUT type='button' value='Imprimir' id=Imprimir name=Imprimir onclick='window.print();'>&nbsp;<INPUT type='button' value=' Salir  ' id=Salir name=Salir onclick='window.close();'>";
		$Pagina = $Pagina."<BR>";
		$Pagina = $Pagina."<P><CENTER>";
		$Pagina = $Pagina."<IMG SRC='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/LOGO_BB.GIF' ALIGN= 'TOP' Banco del Bajio>";
		$Pagina = $Pagina."</P></CENTER>";
		$Pagina = $Pagina."<H3>Aportaci&oacute;n Voluntaria</H3>";
		$Pagina = $Pagina."<hr noshade='noshade' size='4' color='#5A419c' />";
		$Pagina = $Pagina."<P ALIGN='RIGHT'><B><FONT>$date $time</FONT></B></P>";
		$Pagina = $Pagina.$buffer2;
		$Pagina = $Pagina."<hr noshade='noshade' size='4' color='#5A419c' />";
		$Pagina = $Pagina."<p><FONT SIZE=1>Av. Manuel J. Clouthier No. 508 Col. Jardines del Campestre CP 37128 León, Gto.</FONT> </p>";
		$Pagina = $Pagina."<p><FONT SIZE=1>Consultas y aclaraciones 01 (477) 710- 4600 o al 01 (800) 471-0400</FONT></p>";
		$Pagina = $Pagina."<p><FONT SIZE=1>LA TRANSFERENCIA A LA QUE HACE REFERENCIA ESTE COMPROBANTE FUE REALIZADA CONFORME A LA INFORMACIÓN E INSTRUCCIONES QUE NUESTRO CLIENTE ALIMENTÓ EN EL SISTEMA DE BANCO DEL BAJÍO, POR LO TANTO EL CLIENTE ES EL ÚNICO RESPONSABLE PRESENTE Y FUTURO POR CUALQUIER ERROR U OMISIÓN EN ESTA OPERACIÓN.</FONT></P>";
		$Pagina = $Pagina."<p><FONT SIZE=1>EL ÚNICO COMPROBANTE OFICIAL DE ESTA TRANSACCIÓN ES EL ESTADO DE CUENTA QUE EMITE BANCO DEL BAJIO.</FONT></p>";
		$Pagina = $Pagina."</BODY></HTML>\"; NFW =";
		$Pagina = $Pagina." window.open(\"\",'Recibo',\"toolbar=no,menubar=no,scrollbars=2,\"+s); NFW.blur(); NFW.resizeTo(windowW,windowH); NFW.moveTo(windowX,windowY); NFW.";
		$Pagina = $Pagina."document.open(); NFW.document.write(cadena); NFW.document.close(); NFW.window.focus(); } ventana(); </SCRIPT>";
		}
		//DBA Reimpresion de Comprobantes
		/*$FechaHora = $date . " " . $time;
		$QryRIC = new eZReImp( );
		$QryRIC -> store($CustID,$FrAccount,$buffer2,"afo","Aportaci&oacute;n Voluntaria",$FechaHora);*/
		//DBA Reimpresion de Comprobantes
	}


    if(strpos($transaccion_buffer,"<TD>  1-DEDUCIBLE DE IMPUESTOS </TD>") != false && strpos($transaccion_buffer,"<TD>  APORTACION VOLUNTARIA LARGO PLAZO (DEDUCIBLE DE IM </TD>") != false)
      {
        $transaccion_buffer = str_replace("<TD>  1-DEDUCIBLE DE IMPUESTOS </TD>","<TD><font color=Red><i><b>  DEDUCIBLE DE IMPUESTOS <BR/>Por disposición fiscal Afore Afirme Bajío retendrá el 20 por <BR/>ciento del monto en caso de realizar un retiro antes de los <BR/>65 años de edad o no contar con pensión por invalidez total</b></i></font></TD>", $transaccion_buffer);

      }

       if(strpos($transaccion_buffer,"<TD>  1-DEDUCIBLE DE IMPUESTOS </TD>") != false && strpos($transaccion_buffer,"<TD>  APORTACION COMPLEMENTARIA PARA EL RETIRO </TD>") != false)
      {
        $transaccion_buffer = str_replace("<TD>  1-DEDUCIBLE DE IMPUESTOS </TD>","<TD>  DEDUCIBLE DE IMPUESTOS </TD>", $transaccion_buffer);
      }

       if(strpos($transaccion_buffer,"<TD>  2-NO DEDUCIBLE DE IMPUESTOS </TD>") != false)
      {
        $transaccion_buffer = str_replace("<TD>  2-NO DEDUCIBLE DE IMPUESTOS </TD>","<TD>  NO DEDUCIBLE DE IMPUESTOS </TD>", $transaccion_buffer);
      }

       if(strpos($transaccion_buffer,"<TD>  Afore AB retendrá el 20 por ciento del monto de la aportación en caso de retirar antes de los 65 años de edad o de su pensión. </TD>") != false && strpos($transaccion_buffer,"<TD>  APORTACION COMPLEMENTARIA PARA EL RETIRO </TD>") != false)
      {
        $transaccion_buffer = str_replace("<TD>  Afore AB retendrá el 20 por ciento del monto de la aportación en caso de retirar antes de los 65 años de edad o de su pensión. </TD>","<TD></TD>", $transaccion_buffer);
      }


    if(strpos($transaccion_buffer,"<TD>  APORTACION VOLUNTARIA CORTO PLAZO (NO DEDUCIBLE DE </TD>") != false )
      {
        $transaccion_buffer = str_replace("<TD>  APORTACION VOLUNTARIA CORTO PLAZO (NO DEDUCIBLE DE </TD>","<TD>APORTACI&Oacute;N VOLUNTARIA CORTO PLAZO.<BR/>Podrá ser retirada a los 6 meses del primer depósito o último retiro parcial. Posterior a su disposición deberá esperar 6 meses para realizar un siguiente retiro del fondo elegido.</TD>", $transaccion_buffer);
      }

 if(strpos($transaccion_buffer,"<TD>  APORTACION VOLUNTARIA LARGO PLAZO (DEDUCIBLE DE IM </TD>") != false )
      {
        $transaccion_buffer = str_replace("<TD>  APORTACION VOLUNTARIA LARGO PLAZO (DEDUCIBLE DE IM </TD>","<TD>APORTACI&Oacute;N VOLUNTARIA LARGO PLAZO.<BR/>Podrá ser retirada a los 6 meses del primer depósito o último retiro parcial. Posterior a su disposición deberá esperar 6 meses para realizar un siguiente retiro del fondo elegido.</TD>", $transaccion_buffer);
      }
 if(strpos($transaccion_buffer,"<TD>  APORTACION COMPLEMENTARIA PARA EL RETIRO </TD>") != false )
      {
        $transaccion_buffer = str_replace("<TD>  APORTACION COMPLEMENTARIA PARA EL RETIRO </TD>","<TD>APORTACI&Oacute;N COMPLEMENTARIA PARA EL RETIRO. <BR/><I><B><font color=Red>Este tipo de aportación s&oacute;lo permite realizar<BR/>retiros hasta contar con  65 años de edad o con<BR/>una pensión por invalidez total.</font></B></I></TD>", $transaccion_buffer);

      }

        if(strpos($transaccion_buffer,"<TR> <TD> <B>Deducibilidad:</B></TD>") != false)
      {
        $transaccion_buffer = str_replace("<TR> <TD> <B>Deducibilidad:</B></TD>","<TR VALIGN=\"TOP\"> <TD> <B>Deducibilidad :</B></TD>", $transaccion_buffer);
      }




    if(strpos($transaccion_buffer,"<TD>  1-IMSS </TD>") != false )
      {
        $transaccion_buffer = str_replace("<TD>  1-IMSS </TD>","<TD>  IMSS </TD>", $transaccion_buffer);
      }

     if(strpos($transaccion_buffer,"<TD>  2-ISSSTE </TD>") != false )
      {
        $transaccion_buffer = str_replace("<TD>  2-ISSSTE </TD>","<TD>  ISSSTE </TD>", $transaccion_buffer);
      }

     if(strpos($transaccion_buffer,"<TD>  3-INDEPENDIENTE </TD>") != false )
      {
        $transaccion_buffer = str_replace("<TD>  3-INDEPENDIENTE </TD>","<TD>  INDEPENDIENTE </TD>", $transaccion_buffer);
      }



    $t->set_var( "transaccion_buffer", $transaccion_buffer.$Pagina );

    $t->pparse( "output", "aportacionAforeAB_tpl" );
}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );
    $t->setAllStrings();

    $t->set_file( array("user_login_tpl" => "userlogin.tpl") );
    $t->set_var	( "redirect_url", "/transaccion/aportacionAforeAB/" );
    $t->pparse	( "output", "user_login_tpl" );
}
?>