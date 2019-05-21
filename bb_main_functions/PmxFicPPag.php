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
                         "eztransaccion/user/intl/", $Language, "PmxFicPPag.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "PmxFicPPag_tpl" => "PmxFicPPag.tpl"
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
	if(!empty($_POST['FrAccount']))
        $FrAccount = $_POST['FrAccount'];
        
   if(!empty($_POST['DiasPzo']))
        $DiasPzo = $_POST['DiasPzo'];
        
   if(!empty($_POST['Cust']))
        $Cust = $_POST['Cust'];
        
   if(!empty($_POST['Cuenta']))
        $Cuenta = $_POST['Cuenta'];
        
   if(!empty($_POST['Sub']))
        $Sub = $_POST['Sub'];
        
   if(!empty($_POST['UbNo']))
        $UbNo = $_POST['UbNo'];
        
   if(!empty($_POST['Parent2']))
        $Parent2 = $_POST['Parent2'];
        
   if(!empty($_POST['RenInt']))
        $RenInt = $_POST['RenInt'];
        
   if(!empty($_POST['RFC']))
        $RFC = $_POST['RFC'];
        
   if(!empty($_POST['indice']))
        $indice = $_POST['indice'];
        
   if(!empty($_POST['gene']))
        $gene = $_POST['gene'];
        
   if(!empty($_POST['token']))
        $token = $_POST['token'];
        
   if(!empty($_POST['chkbox']))
        $chkbox = $_POST['chkbox'];
        
   if(!empty($_POST['txtbox']))
        $txtbox = $_POST['txtbox'];
//HB        
    
    if(empty($_POST['Access'])) {
        $_POST['Access'] = "FrAc";
    }

    if (($_POST['Accion']=="Pagar") and  ($_POST['Button']=="Ver"))
    {
    	$_POST['Access'] = "FrAc";
    	        //var_dump ($_POST['Pos']);
    }


    switch($_POST['Access']) {
    case "FrAc":
        $particularFields = "";
        $_POST['Accion'] = "ListFPP";
		// RAI 10Feb2012 - Inicio - Track 185966 - Agrupación pemex-gas
		if ($_POST['AgruPagIni'] == 1 )
		{
			if ($_POST['Button'] == "Ver" && $_POST['Agrupado'] != "")
			{
				$FrAccount = $_POST['Agrupado'];
			}
			$particularFields = "&Cust=".urlencode($Cust)."&Cuenta=".urlencode($Cuenta)."&Sub=".urlencode($Sub)."&Accion=".urlencode($_POST['Accion'])."&UbNo=".urlencode($UbNo)."&Pos=".urlencode($_POST['Pos'])."&Day=".urlencode($_POST['Day'])."&Teleb=".urlencode($_POST['Teleb'])."&Button=".$_POST['Button']."&AgruPagIni=".$_POST['AgruPagIni']."&FrAccount=".$FrAccount;
		}
		else
		{
			$particularFields = "&Cust=".urlencode($Cust)."&Cuenta=".urlencode($Cuenta)."&Sub=".urlencode($Sub)."&Accion=".urlencode($_POST['Accion'])."&UbNo=".urlencode($UbNo)."&Pos=".urlencode($_POST['Pos'])."&Day=".urlencode($_POST['Day'])."&Teleb=".urlencode($_POST['Teleb'])."&Button=".$_POST['Button'];
		}
        //$particularFields = "&Cust=".urlencode($Cust)."&Cuenta=".urlencode($Cuenta)."&Sub=".urlencode($Sub)."&Accion=".urlencode($_POST['Accion'])."&UbNo=".urlencode($UbNo)."&Pos=".urlencode($_POST['Pos'])."&Day=".urlencode($_POST['Day'])."&Teleb=".urlencode($_POST['Teleb'])."&Button=".$_POST['Button']."&AgruPagIni=".$_POST['AgruPagIni']."&FrAccount=".$FrAccount;
		// RAI 10Feb2012 - Fin - Track 185966 - Agrupación pemex-gas
        $tr->blog($qki,"Fichas por Pagar", $FrAccount, $Cust, $Cuenta, $Sub, $_POST['Accion']);
        break;
    case "Confirm":


       	 if($_POST['Accion']=="Pagar")
       	 {
            $DiasPzo = "";
            $Parent2 = "";

	    //var_dump($indice);

	    for ($i = 0; $i<=$indice; $i++)
	    {
            	if (is_Null($chkbox[$i]))
            	{
        	   $txtbox[$i] = "";
        	}
        	else
        	{
        	   //$DiasPzo = "a".$chkbox[$i].$DiasPzo;
        	   //$Parent2 = "a".$txtbox[$i].$Parent2;
        	   $DiasPzo = $DiasPzo."a".$chkbox[$i];
        	   $Parent2 = $Parent2."a".$txtbox[$i];

        	}
            }
        }

        $particularFields = "&Cust=".urlencode($Cust).
        "&Accion=".urlencode($_POST['Accion']).
        "&DiasPzo=".urlencode($DiasPzo).
        "&Pos=".urlencode($_POST['Pos']).
        "&Button=".urlencode($_POST['Button']).
        "&RFC=".urlencode($RFC).
        "&RenInt=".urlencode($RenInt).
        "&Parent2=".$Parent2.
        "&Apocope=".urlencode($_POST['Apocope']).
        "&FrAccount=".urlencode($FrAccount).
        "&indice=".$indice;

        $tr->blog($qki,"Catalogo de Facturas", $FrAccount, $Cust, $Cuenta, $Sub, $_POST['Accion']);
	break;

        case "Process":
	$particularFields = "&Cust=".urlencode($Cust).
        "&Accion=".urlencode($_POST['Accion']).
        "&DiasPzo=".urlencode($DiasPzo).
        "&Pos=".urlencode($_POST['Pos']).
        "&Button=".urlencode($_POST['Button']).
        "&RFC=".urlencode($RFC).
        "&RenInt=".urlencode($RenInt).
        "&Parent2=".$Parent2.
        "&Apocope=".urlencode($_POST['Apocope']).
        "&FrAccount=".urlencode($FrAccount)."&Day=".urlencode($_POST['Day']);
        $tr->blog($qki,"Catalogo de Facturas", $FrAccount, $Cust, $Cuenta, $Sub, $_POST['Accion']);
        break;
    }
      	//var_dump($_POST['Access']);
      	//var_dump($particularFields);
    $transaccion_buffer = "";
    // DebugBreak();

    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=pmx&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // ligas de cuentas de terceros

    $t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "PmxFicPPag_tpl" );
}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/PmxFicPPag/" );

    $t->pparse( "output", "user_login_tpl" );
}
?>
