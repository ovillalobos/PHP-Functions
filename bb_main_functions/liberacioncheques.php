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
                         "eztransaccion/user/intl/", $Language, "liberacioncheques.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "liberacioncheques_tpl" => "liberacioncheques.tpl"
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
    if(empty($_POST['Access'])) {
        $Access = "FrAc";
    }
	 else
	 	  $Access = $_POST['Access'];
//HB
	 if(!empty($_POST['Accion']))
        $Accion = $_POST['Accion'];
	 
	 if(!empty($_POST['Pos']))
        $Pos = $_POST['Pos'];
	 	  
	 if(!empty($_POST['CqNo']))
        $CqNo = $_POST['CqNo'];
	 	  
	 if(!empty($_POST['Cq']))
        $Cq = $_POST['Cq'];
	 
	 if(!empty($_POST['CqAll']))
        $CqAll = $_POST['CqAll'];
	 	  
	 if(!empty($_POST['Amount']))
        $Amount = $_POST['Amount'];
	 	  
	 if(!empty($_POST['FrAccount']))
        $FrAccount = $_POST['FrAccount'];
	 	  
	 if(!empty($_POST['Cq0']))
        $Cq0 = $_POST['Cq0'];
        
    if(!empty($_POST['Cq1']))
        $Cq1 = $_POST['Cq1'];
        
    if(!empty($_POST['Cq2']))
        $Cq2 = $_POST['Cq2'];
        
    if(!empty($_POST['Cq3']))
        $Cq3 = $_POST['Cq3'];
        
    if(!empty($_POST['Cq4']))
        $Cq4 = $_POST['Cq4'];
        
    if(!empty($_POST['Cq5']))
        $Cq5 = $_POST['Cq5'];
        
    if(!empty($_POST['Cq6']))
        $Cq6 = $_POST['Cq6'];
        
    if(!empty($_POST['Cq7']))
        $Cq7 = $_POST['Cq7'];
        
    if(!empty($_POST['Cq8']))
        $Cq8 = $_POST['Cq8'];
        
    if(!empty($_POST['Cq9']))
        $Cq9 = $_POST['Cq9'];


    // ********************************************************
    // DGM conservar numeros de cheque a modificar
    if(!empty($_POST['chk0'])) $chk0 = $_POST['chk0'];
    if(!empty($_POST['chk1'])) $chk1 = $_POST['chk1'];
    if(!empty($_POST['chk2'])) $chk2 = $_POST['chk2'];
    if(!empty($_POST['chk3'])) $chk3 = $_POST['chk3'];
    if(!empty($_POST['chk4'])) $chk4 = $_POST['chk4'];
    if(!empty($_POST['chk5'])) $chk5 = $_POST['chk5'];
    if(!empty($_POST['chk6'])) $chk6 = $_POST['chk6'];
    if(!empty($_POST['chk7'])) $chk7 = $_POST['chk7'];
    if(!empty($_POST['chk8'])) $chk8 = $_POST['chk8'];
    if(!empty($_POST['chk9'])) $chk9 = $_POST['chk9'];
	// ********************************************************

	 	 
	 if(!empty($_POST['Monto0']))
        $Monto0 = $_POST['Monto0'];
        
    if(!empty($_POST['Monto1']))
        $Monto1 = $_POST['Monto1'];
        
    if(!empty($_POST['Monto2']))
        $Monto2 = $_POST['Monto2'];
        
    if(!empty($_POST['Monto3']))
        $Monto3 = $_POST['Monto3'];
        
    if(!empty($_POST['Monto4']))
        $Monto4 = $_POST['Monto4'];
        
    if(!empty($_POST['Monto5']))
        $Monto5 = $_POST['Monto5'];
        
    if(!empty($_POST['Monto6']))
        $Monto6 = $_POST['Monto6'];
            
    if(!empty($_POST['Monto7']))
        $Monto7 = $_POST['Monto7'];
        
    if(!empty($_POST['Monto8']))
        $Monto8 = $_POST['Monto8'];
        
    if(!empty($_POST['Monto9']))
        $Monto9 = $_POST['Monto9'];
//HB

    if( !isset /*(HB AGL - Ajustes PHP5)*/( $CqNo ) ) {
    	$CqNo = "";
    }
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $Cq ) ) {
    	$Cq = "";
    }

	if( !isset /*(HB AGL - Ajustes PHP5)*/( $CqAll ) ) {
    	$CqAll = "";
    }

	if ( !isset /*(HB AGL - Ajustes PHP5)*/ ($Amount ) ) {
		$Amount = "0";
	}


	switch($Access) {
    case "FrAc":
        $particularFields = "";
		$tr->blog($qki,"liberacioncheques", $FrAccount, $Accion);
        break;

    case "Confirm":
	case "Process":

		switch($Accion){
			case  "libnum":
			case  "pglibnum":
				// se concatenan los 20 campos separados por - y ; ejemplo
				// 1-1000.00;3-4500.54;
				// significa el chque 1 por 1000 y el cheque 3 por 4500.54
				$CqAll = "";
				$CqAll = $CqAll.urlencode($Cq0)."-".urlencode(ltrim(rtrim($Monto0))).";";
				$CqAll = $CqAll.urlencode($Cq1)."-".urlencode(ltrim(rtrim($Monto1))).";";
				$CqAll = $CqAll.urlencode($Cq2)."-".urlencode(ltrim(rtrim($Monto2))).";";
				$CqAll = $CqAll.urlencode($Cq3)."-".urlencode(ltrim(rtrim($Monto3))).";";
				$CqAll = $CqAll.urlencode($Cq4)."-".urlencode(ltrim(rtrim($Monto4))).";";
				$CqAll = $CqAll.urlencode($Cq5)."-".urlencode(ltrim(rtrim($Monto5))).";";
				$CqAll = $CqAll.urlencode($Cq6)."-".urlencode(ltrim(rtrim($Monto6))).";";
				$CqAll = $CqAll.urlencode($Cq7)."-".urlencode(ltrim(rtrim($Monto7))).";";
				$CqAll = $CqAll.urlencode($Cq8)."-".urlencode(ltrim(rtrim($Monto8))).";";
				$CqAll = $CqAll.urlencode($Cq9)."-".urlencode(ltrim(rtrim($Monto9))).";";


				$particularFields = "&FrAccount=".urlencode($FrAccount)."&Accion=".urlencode($Accion)."&gene=".$CqAll;

				$tr->blog($qki,"liberacioncheques", $FrAccount, $Accion, $particularFields);
				break;

			case "libran":
			case "pglibran":

				$particularFields = "&FrAccount=".urlencode($FrAccount)."&Accion=".urlencode($Accion)."&CqNo=".urlencode($CqNo)."&Cq=".urlencode($Cq)."&Amount=".urlencode($Amount);
				$tr->blog($qki,"liberacioncheques", $FrAccount, $CqNo, $Cq);
				break;

			case "verpag":
			case "vernopag":
			case "verred":
				$Access = "Process";

				$particularFields = "&FrAccount=".urlencode($FrAccount)."&Accion=".urlencode($Accion)."&Pos=".urlencode($Pos);
				//$tr->blog($qki,"liberacioncheques", $FrAccount, $CqNo, $Cq);
				$tr->blog($qki,"liberacioncheques", $particularFields);
				break;

			case "modnum":
			case "cannum":

				$CqAll = "";

				if ( $Access == "Confirm" and $Accion == "modnum" )
				{
					if (urlencode($chk0) != "") {$CqAll = $CqAll.urlencode($chk0)."-".urlencode(ltrim(rtrim($Monto0))).";"; }
					if (urlencode($chk1) != "") {$CqAll = $CqAll.urlencode($chk1)."-".urlencode(ltrim(rtrim($Monto1))).";";	}
					if (urlencode($chk2) != "") {$CqAll = $CqAll.urlencode($chk2)."-".urlencode(ltrim(rtrim($Monto2))).";"; }
					if (urlencode($chk3) != "") {$CqAll = $CqAll.urlencode($chk3)."-".urlencode(ltrim(rtrim($Monto3))).";"; }
					if (urlencode($chk4) != "") {$CqAll = $CqAll.urlencode($chk4)."-".urlencode(ltrim(rtrim($Monto4))).";"; }
					if (urlencode($chk5) != "") {$CqAll = $CqAll.urlencode($chk5)."-".urlencode(ltrim(rtrim($Monto5))).";"; }
					if (urlencode($chk6) != "") {$CqAll = $CqAll.urlencode($chk6)."-".urlencode(ltrim(rtrim($Monto6))).";"; }
					if (urlencode($chk7) != "") {$CqAll = $CqAll.urlencode($chk7)."-".urlencode(ltrim(rtrim($Monto7))).";"; }
					if (urlencode($chk8) != "") {$CqAll = $CqAll.urlencode($chk8)."-".urlencode(ltrim(rtrim($Monto8))).";"; }
					if (urlencode($chk9) != "") {$CqAll = $CqAll.urlencode($chk9)."-".urlencode(ltrim(rtrim($Monto9))).";"; }
				}
				else if ( $Access == "Confirm" and $Accion == "cannum" )
				{
					if (urlencode($chk0) != "") {$CqAll = $CqAll.urlencode($chk0)."-;"; }
					if (urlencode($chk1) != "") {$CqAll = $CqAll.urlencode($chk1)."-;";	}
					if (urlencode($chk2) != "") {$CqAll = $CqAll.urlencode($chk2)."-;"; }
					if (urlencode($chk3) != "") {$CqAll = $CqAll.urlencode($chk3)."-;"; }
					if (urlencode($chk4) != "") {$CqAll = $CqAll.urlencode($chk4)."-;"; }
					if (urlencode($chk5) != "") {$CqAll = $CqAll.urlencode($chk5)."-;"; }
					if (urlencode($chk6) != "") {$CqAll = $CqAll.urlencode($chk6)."-;"; }
					if (urlencode($chk7) != "") {$CqAll = $CqAll.urlencode($chk7)."-;"; }
					if (urlencode($chk8) != "") {$CqAll = $CqAll.urlencode($chk8)."-;"; }
					if (urlencode($chk9) != "") {$CqAll = $CqAll.urlencode($chk9)."-;"; }
				}
				else if ($Access == "Process" and $Accion == "cannum")
				{
					$CqAll = $CqAll.urlencode($Cq0)."-;";
					$CqAll = $CqAll.urlencode($Cq1)."-;";
					$CqAll = $CqAll.urlencode($Cq2)."-;";
					$CqAll = $CqAll.urlencode($Cq3)."-;";
					$CqAll = $CqAll.urlencode($Cq4)."-;";
					$CqAll = $CqAll.urlencode($Cq5)."-;";
					$CqAll = $CqAll.urlencode($Cq6)."-;";
					$CqAll = $CqAll.urlencode($Cq7)."-;";
					$CqAll = $CqAll.urlencode($Cq8)."-;";
					$CqAll = $CqAll.urlencode($Cq9)."-;";
				}
				else if ($Access == "Process" and $Accion == "modnum")
				{
					$CqAll = $CqAll.urlencode($Cq0)."-".urlencode(ltrim(rtrim($Monto0))).";";
					$CqAll = $CqAll.urlencode($Cq1)."-".urlencode(ltrim(rtrim($Monto1))).";";
					$CqAll = $CqAll.urlencode($Cq2)."-".urlencode(ltrim(rtrim($Monto2))).";";
					$CqAll = $CqAll.urlencode($Cq3)."-".urlencode(ltrim(rtrim($Monto3))).";";
					$CqAll = $CqAll.urlencode($Cq4)."-".urlencode(ltrim(rtrim($Monto4))).";";
					$CqAll = $CqAll.urlencode($Cq5)."-".urlencode(ltrim(rtrim($Monto5))).";";
					$CqAll = $CqAll.urlencode($Cq6)."-".urlencode(ltrim(rtrim($Monto6))).";";
					$CqAll = $CqAll.urlencode($Cq7)."-".urlencode(ltrim(rtrim($Monto7))).";";
					$CqAll = $CqAll.urlencode($Cq8)."-".urlencode(ltrim(rtrim($Monto8))).";";
					$CqAll = $CqAll.urlencode($Cq9)."-".urlencode(ltrim(rtrim($Monto9))).";";
				}

				$particularFields = "&FrAccount=".urlencode($FrAccount)."&Accion=".urlencode($Accion)."&gene=".$CqAll;
				$tr->blog($qki,"liberacioncheques", $FrAccount, $Accion, $particularFields);
				break;

			default:
				$particularFields = "&Pos=".urlencode($Pos)."&FrAccount=".urlencode($FrAccount)."&Accion=".urlencode($Accion);
				$tr->blog($qki,"liberacioncheques", $FrAccount, $Accion);

		}
		 break;

    }

	$transaccion_buffer = "";
    // DebugBreak();
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=lch&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer);


	/*if ( strpos($transaccion_buffer,"##") == true )
	{*/
		// estos son reemplazos de errores, ya que en ibnk.msg solo tiene 200 caracteres
		$transaccion_buffer = str_replace("##","<BR><B>Cheque No.</B>",														$transaccion_buffer);
		$transaccion_buffer = str_replace("**1"," Introducir valores numericos.",											$transaccion_buffer);
		$transaccion_buffer = str_replace("**2"," debe tener máximo 2 decimales.",											$transaccion_buffer);
		$transaccion_buffer = str_replace("**3"," no está dado de alta en el sistema, favor de contactar a su Ejecutivo.",	$transaccion_buffer);
		$transaccion_buffer = str_replace("**4"," ya ha sido pagado,",														$transaccion_buffer);
		$transaccion_buffer = str_replace("**5"," está reportado como robado o extraviado.",								$transaccion_buffer);
		$transaccion_buffer = str_replace("**6"," ya ha sido liberado anteriormente.",										$transaccion_buffer);
		$transaccion_buffer = str_replace("**7"," No está protegido.",														$transaccion_buffer);
		//$transaccion_buffer = str_replace("**8"," el monto debe ser mayor a: ",												$transaccion_buffer);	DGM 23Ene2007
		$transaccion_buffer = str_replace("**8"," el monto debe ser mayor o igual a: ",												$transaccion_buffer);
		$transaccion_buffer = str_replace("**9"," Falta el número de cheque para el monto:",								$transaccion_buffer);
	//}
	// ********************************************************************

    $t->set_var( "transaccion_buffer", $transaccion_buffer);
    $t->pparse( "output", "liberacioncheques_tpl" );
}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/liberacioncheques/" );

    $t->pparse( "output", "user_login_tpl" );
}
?>