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

include("eztransaccion/user/include/jscalendar/calendar-blue.css");
include("eztransaccion/user/include/jscalendar/calendar.js");


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
                         "eztransaccion/user/intl/", $Language, "aportacionAforeProg.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "aportacionAforeProg_tpl" => "aportacionAforeProg.tpl"
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
	if(!empty($_POST['code']))
		$code = $_POST['code'];
		
	if(!empty($_POST['MonthF']))
		$MonthF = $_POST['MonthF'];
		
	if(!empty($_POST['Month']))
		$Month = $_POST['Month'];
		
	if(!empty($_POST['Cuenta']))
		$Cuenta = $_POST['Cuenta'];
		
	if(!empty($_POST['Cust']))
		$Cust = $_POST['Cust'];
		
	if(!empty($_POST['Sub']))
		$Sub = $_POST['Sub'];
		
	if(!empty($_POST['Button2']))
		$Button2 = $_POST['Button2'];
		
	if(!empty($_POST['Button']))
		$Button = $_POST['Button'];
	
	if(!empty($_POST['ImpEnvio']))
		$ImpEnvio = $_POST['ImpEnvio'];	
//HB
    
    if(empty($_POST["plaza3"])) {
        $_POST["plaza3"] = "";
    }
    if(empty($Button2)) {
        $Button2 = "";
    }
    if(empty($_POST["Access"])) {
    if ($Button2 == "Regresar")
    {
        $_POST["Listado"] = "Cambiar";
        $_POST["Access"] = "Process";
    }
    else
    {
        $_POST["Access"] = "FrAc";
    }
    }
    if(empty($_POST["Accion"])) {
        $_POST["Accion"] = "Listaavp";
    }

	if($_POST["Listado"]=="Cancelar")
	{
		$_POST["Accion"] = "Listaavp";
	}
    switch($_POST["Access"]) {
    case "FrAc":
        $particularFields = "&Accion=".($_POST["Accion"])."&Pos=".($_POST["Pos"]).
                                                "&Porcen1=".($_POST["Porcen1"]);
        $tr->blog($qki,"Aportaciones Voluntarias Programadas", $_POST["FrAccount"], $Cust, $Cuenta, $Sub, $_POST["Accion"], $_POST["RenCap"], $_POST["cbTipoAf"], $_POST["cbTipoAp"], $_POST["cbDedu"]);
        break;
    case "Confirm":
                if($_POST["Newavp"]=="Altas")
                {
                        $_POST["Accion"] = "Newavp";
                        $_POST["Access"] = "FrAc";
                }
                if($_POST["Canccta"]=="Bajas")
                {
                        if($_POST["Accion"] == "Listaavp")
                        {
                        $_POST["Accion"] = "Cancavp0";
                        }
                        else
                        {
                        $_POST["Accion"] = "Cancavp";
                        }
                }
        if($_POST["Listado"]=="Consultas" or $_POST["Listado"]=="Siguientes")
        {
          $_POST["Accion"] = "Listaavp";
        }
        switch($_POST["Access"])
        {
                case "FrAc":
                                $particularFields = "&Accion=".($_POST["Accion"]).
                                "&Pos=".($_POST["Pos"]).
                                        "&Porcen1=".($_POST["Porcen1"]).
                                "&Rownos=".$_POST["Borrar0"].$_POST["Borrar1"].$_POST["Borrar2"].$_POST["Borrar3"];
                                $tr->blog($qki,"Aportaciones Voluntarias Programadas", $_POST["FrAccount"], $Cust, $Cuenta, $Sub, $_POST["Accion"], $_POST["RenCap"], $_POST["cbTipoAf"], $_POST["cbTipoAp"], $_POST["cbDedu"]);
                                break;
                        case "Confirm":
                                if ($_POST["Accion"] == "Newavp" )
                                {
                                        $particularFields = "&Accion=".($_POST["Accion"]).
                                        "&Button=".($Button).
                                        "&FrAccount=".($_POST["FrAccount"]).
                                        "&Amount=".($_POST["Amount"]).
                                        "&RenInt=".($RenInt).
                                        "&Desc=".($_POST["Desc"]).
                                        "&Editar=".($_POST["Editar"]).
                                        "&Day=".($Day).
                                        "&Month=".($Month).
                                        "&Year=".($Year).
                                        "&Cq=".($_POST["Cq"]).
                                        "&Porcen1=".($_POST["Porcen1"]).
                                        "&DiasPzo=".($DiasPzo).
                                        "&DayF=".($_POST["DayF"]).
                                        "&MonthF=".($MonthF).
                                        "&RenCap=".($_POST["RenCap"]).
//                                        "&ImpEnvio=".($ImpEnvio).
                                        "&cbTipoAf=".($_POST["cbTipoAf"]).
                                        "&cbTipoAp=".($_POST["cbTipoAp"]).
                                        "&cbDedu=".($_POST["cbDedu"]).
                                        "&plaza3=".($_POST["plaza3"]);
                                }
                                else if ($_POST["Accion"] == "Cancavp0")
                                {
                                    $particularFields = "&Accion=".($_POST["Accion"])."&Porcen1=".($_POST["Porcen1"])."&Pos=".($_POST["Pos"])."&Rownos=".$_POST["Borrar0"].$_POST["Borrar1"].$_POST["Borrar2"].$_POST["Borrar3"];
                                }
                                else
                                {
                                        $Rownos = "";
                                        if ($_POST["Borrar0"] <> "") { $Rownos = $Rownos.$_POST["Borrar0"]."-"; }
                                        if ($_POST["Borrar1"] <> "") { $Rownos = $Rownos.$_POST["Borrar1"]."-"; }
                                        if ($_POST["Borrar2"] <> "") { $Rownos = $Rownos.$_POST["Borrar2"]."-"; }
                                        if ($_POST["Borrar3"] <> "") { $Rownos = $Rownos.$_POST["Borrar3"]."-"; }
                                        if ($_POST["Borrar4"] <> "") { $Rownos = $Rownos.$_POST["Borrar4"]."-"; }
                                        if ($_POST["Borrar5"] <> "") { $Rownos = $Rownos.$_POST["Borrar5"]."-"; }
                                        if ($_POST["Borrar6"] <> "") { $Rownos = $Rownos.$_POST["Borrar6"]."-"; }
                                        if ($_POST["Borrar7"] <> "") { $Rownos = $Rownos.$_POST["Borrar7"]."-"; }
                                        if ($_POST["Borrar8"] <> "") { $Rownos = $Rownos.$_POST["Borrar8"]."-"; }
                                        if ($_POST["Borrar9"] <> "") { $Rownos = $Rownos.$_POST["Borrar9"]."-"; }
                                        $particularFields = "&Accion=".($_POST["Accion"]).
                                        "&Button=".($Button)."&Pos=".($_POST["Pos"]).
                                        "&Porcen1=".($_POST["Porcen1"]).
                                        "&Desc=".($Rownos);
                                }
                        $tr->blog($qki,"Aportaciones Voluntarias Programadas", $_POST["FrAccount"], $_POST["Amount"], $ImpEnvio, $_POST["Accion"], $_POST["RenCap"], $_POST["cbTipoAf"], $_POST["cbTipoAp"], $_POST["cbDedu"]);
                                break;
                }
                break;
    case "Process":
		if($_POST["Listado"]=="Cambiar") 
		{
			$_POST["Accion"] = "Newavp";
      $_POST["Access"] = "FrAc";
		}
    if ($Button2 == "Regresar")
		{
			$_POST["Accion"] = "Newavp";
      $_POST["Access"] = "FrAc";
		}

        if($_POST["Listado"]=="Consultas")
        {
            $_POST["Accion"] = "Listaavp";
        }
        switch($_POST["Accion"])
        {
                        case "Newavp":
                                if($Button2 == "Regresar")
                                {
                                $particularFields = "&Cust=".($Cust)."&Accion=".($_POST["Accion"]).
                                "&Button=".($Button);
                                }
                                else
                                {
                                $particularFields = "&Passwd=".encrypt( $code, strtolower( $usr ) ).	//ALUNA 30Jul2007 agenda de pagos tokens
                								"&Accion=".($_POST["Accion"]).
                                "&Button=".($Button).
                                "&FrAccount=".($_POST["FrAccount"]).
                                "&Amount=".($_POST["Amount"]).
                                "&RenInt=".($RenInt).
                                "&Desc=".($_POST["Desc"]).
                                "&Editar=".($_POST["Editar"]).
                                "&Day=".($Day).
                                "&Month=".($Month).
                                "&Year=".($Year).
                                "&Cq=".($_POST["Cq"]).
                                "&Porcen1=".($_POST["Porcen1"]).
                                "&DiasPzo=".($DiasPzo).
                                "&DayF=".($_POST["DayF"]).
                                "&MonthF=".($MonthF).
                                "&RenCap=".($_POST["RenCap"]).
                                "&cbTipoAf=".($_POST["cbTipoAf"]).
                                "&cbTipoAp=".($_POST["cbTipoAp"]).
                                "&cbDedu=".($_POST["cbDedu"]).
                                "&plaza3=".($_POST["plaza3"]);
                                }
                                $tr->blog($qki,"Aportaciones Voluntarias Programadas", $_POST["FrAccount"], $_POST["Amount"], $ImpEnvio, $_POST["Accion"], $_POST["RenCap"], $_POST["cbTipoAf"], $_POST["cbTipoAp"], $_POST["cbDedu"]);
                                break;
                        case "Cancavp":
                                $particularFields = "&Passwd=".encrypt( $code, strtolower( $usr ) ).	
                                "&Cust=".($Cust)."&Accion=".($_POST["Accion"]).
                                "&Button=".($Button)."&Desc=".($_POST["Desc"]);
                                $tr->blog($qki,"Catalogo de Cuentas", $_POST["FrAccount"], $Cust, $Cuenta, $Sub, $_POST["Accion"], $_POST["RenCap"], $_POST["cbTipoAf"], $_POST["cbTipoAp"], $_POST["cbDedu"]);
                                break;
                        default:
                                $particularFields = "&Cust=".($Cust)."&Accion=".($_POST["Accion"]).
                                "&Button=".($Button);
                                $tr->blog($qki,"Catalogo de Cuentas", $_POST["FrAccount"], $Cust, $Cuenta, $Sub, $_POST["Accion"], $_POST["RenCap"], $_POST["cbTipoAf"], $_POST["cbTipoAp"], $_POST["cbDedu"]);
                }


        break;
    }
    $transaccion_buffer = "";
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=avp&Access=".($_POST["Access"])."&CustID=".($usr)."&Cadpriv=".($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer);

			if (strpos($transaccion_buffer,"Usted no tiene ninguna aportac") != false ) 
      {
        $transaccion_buffer = str_replace("<P>Bajas:   Oprima el Bot&oacute;n de <I>Bajas</I>, seleccione la(las) aportaci&oacute;n(es) que desea eliminar y oprima nuevamente el Bot&oacute;n de <I>Bajas</I></P>","",str_replace("<INPUT TYPE=SUBMIT NAME=".chr(34)."Canccta".chr(34)." VALUE=".chr(34)."Bajas".chr(34).">&nbsp;","",$transaccion_buffer));
        $transaccion_buffer = str_replace("<INPUT TYPE=SUBMIT NAME=".chr(34)."Listado".chr(34)." VALUE=".chr(34)."Consultas".chr(34).">","",$transaccion_buffer);      
      }
      if(strpos($transaccion_buffer,"<TD>  1-DEDUCIBLE DE IMPUESTOS </TD>") != false && strpos($transaccion_buffer,"<TD>  APORTACION VOLUNTARIA LARGO PLAZO (DEDUCIBLE DE IM </TD>") != false) 
      {
        $transaccion_buffer = str_replace("<TD>  1-DEDUCIBLE DE IMPUESTOS </TD>","<TD><font color=Red><i><b>  DEDUCIBLE DE IMPUESTOS </b></i></font></TD>", $transaccion_buffer);

        $transaccion_buffer = str_replace("<TD>  Afore AB retendrá el 20 por ciento del monto de la aportación en caso de retirar antes de los 65 años de edad o de su pensión. </TD>","<TD><I><B><font color=Red> Por disposición fiscal Afore Afirme Bajío retendrá el 20 por <BR/>ciento del monto en caso de realizar un retiro antes de los <BR/>65 años de edad o no contar con pensión por invalidez total</font></B></I></TD>", $transaccion_buffer);
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
        $transaccion_buffer = str_replace("<TD>  APORTACION VOLUNTARIA CORTO PLAZO (NO DEDUCIBLE DE </TD>","<TD>APORTACION VOLUNTARIA CORTO PLAZO.<BR/>Podrá ser retirada a los 6 meses del primer depósito o último retiro parcial. <BR/>Posterior a su disposición deberá esperar 6 meses para realizar un siguiente retiro del fondo elegido.</TD>", $transaccion_buffer);
      }
   
 if(strpos($transaccion_buffer,"<TD>  APORTACION VOLUNTARIA LARGO PLAZO (DEDUCIBLE DE IM </TD>") != false ) 
      {
        $transaccion_buffer = str_replace("<TD>  APORTACION VOLUNTARIA LARGO PLAZO (DEDUCIBLE DE IM </TD>","<TD>APORTACION VOLUNTARIA LARGO PLAZO.<BR/>Podrá ser retirada a los 6 meses del primer depósito o último retiro parcial. Posterior a su disposición deberá esperar 6 meses para realizar un siguiente retiro del fondo elegido.</TD>", $transaccion_buffer);
      }
 if(strpos($transaccion_buffer,"<TD>  APORTACION COMPLEMENTARIA PARA EL RETIRO </TD>") != false ) 
      {
        $transaccion_buffer = str_replace("<TD>  APORTACION COMPLEMENTARIA PARA EL RETIRO </TD>","<TD>APORTACION COMPLEMENTARIA PARA EL RETIRO. <BR/><I><B><font color=Red>Este tipo de aportación s&oacute;lo permite realizar<BR/>retiros hasta contar con  65 años de edad o con<BR/>una pensión por invalidez total.</font></B></I></TD>", $transaccion_buffer);
      

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
      
    $t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "aportacionAforeProg_tpl" );
}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/aportacionAforeProg/" );

    $t->pparse( "output", "user_login_tpl" );
}
?>

