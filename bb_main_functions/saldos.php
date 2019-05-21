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
// DebugBreak();

// *************************************************************************************************************
// DGM - DBA I 04Abr2008  Esquema de validacion de seguridad informatica

// Cuando jboss sirve la 1a pagina para usuarios con agrupacion, pinta un <select> con las posibles cuentas a consultar
// entonces en saldos.php estas cuentas son extraidas para aplicarles un md5 y posteriormente estos md5 pintalos en campos hidden en el transaccion_buffer
// estos md5 calculados indican las unicas posibles cuentas de donde se podra consultar saldos.
// finalmente cuando el usuario hace click en aceptar el sistema busca la cuenta que seleccionó o tecleó el usuario, le aplica un md5
// y lo compara contra los md5 primeros permitidos, en caso de exito lo deja pasar, de lo contrario le marca un error generico.

// DGM - DBA F 04Abr2008  Esquema de validacion de seguridad informatica
// *************************************************************************************************************


include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezhttptool.php" );

include_once( "eztransaccion/classes/eztransaccion.php" );
include_once( "ezsession/classes/ezsession.php" );

$session =& eZSession::globalSession();
global $md5BB;

// *************************************************************************************************
// DGM-DBA I 04Abr2008 Observaciones seguridad informática
// Esta funci8on extrae las cuentas del transaccion_buffer y calcula su md5 de cada una de ellas
// y posteriormente esos md5 los pinta como hidden en el mismo transaccion_buffer

function MD5_Ctas($buffer,$galletita, $usr)
{

	$posini = strpos($buffer,"<SELECT NAME=\"FrAccount\">");
	$posfin = strpos($buffer,"</SELECT>");

	$ctas 		 = substr ($buffer,$posini,($posfin-($posini+25)));
	$md5hide 	 = "";
	$cuenta 	 = "";
	$posn 	 	 = 0;
	$a 		 	 = 1;
	$ctasArreglo = "";

	while ($a)
	{
		$a = strpos($ctas,"<OPTION VALUE=\"",$posn); //extrae cuenta por cuenta
		if ($a)
		{
			$cuenta = "";
			for ($i=$a+strlen("<OPTION VALUE=\""); $i<strlen($ctas); $i++)
			{
				$caracter = $ctas[$i];
				if ($caracter >= "0" and $caracter <= "9")  //solo toma los caracteres numericos del <option>
				{
					$cuenta .= $caracter;
				}
				else
				{
					$posn = $i;
					$i = strlen($ctas);
				}
			}

			$cuenta 		= md5($galletita.trim($cuenta).trim($usr)); //se calcula su md5
			$ctasArreglo   .= $cuenta."|";
		}
	}

	// finalmente se agrega un campo hidden con todos los md5 calculados separados por pipe
	return     substr($buffer, 0, $posfin + 9) .
			   "<INPUT TYPE=HIDDEN NAME=\"ctasArreglo\" VALUE=$ctasArreglo>" .
			   substr($buffer, $posfin + 9);

}


// ******************************************************************************************************
// Esta funcion compara la cuenta seleccionada o tecleada por el usuario contra el campo de md5s validos
function MD5_Valida($ctasPermitidas,$cuentaConsultando,$galletita, $usr)
{

	$ctaArreglo = explode("|", $ctasPermitidas); //extrae el campo md5 (pintado desde el fracc) en un arreglo

	foreach($ctaArreglo as $ctaPermitida)
	{
		$tmp =md5($galletita.trim($cuentaConsultando).trim($usr));	// calcula el md5 de la cuenta que quiere consultar

		if ($ctaPermitida == $tmp) // si es igual entonces es una cuenta permitida
		{
			return 0;
		}
	}
	return -1;  // es una cuenta que no es permitida (indica que el usuario modifico de alguna manera la cuenta destino y no es permitida para consulta)
}

// DGM-DBA F 04Abr2008 Observaciones seguridad informática
// *************************************************************************************************


if( !$session->fetch() )
    $session->store();

$ini =& $GLOBALS["GlobalSiteIni"];

$Language = $ini->read_var( "eZTransaccionMain", "Language" );
// $backend = $ini->read_var( "eZTransaccionMain", "Backend" );
$ServerNumber = $ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );

$backend = "gw" . $ServerNumber. $DomainPostfix;
if ( $GLOBALS["DEBUGA"] == true ) {
		$log = "En ".$_SERVER['SCRIPT_FILENAME']." que es ".$_SERVER['PHP_SELF']." (backend) ->" . print_r( $backend, true ) . "|";
        eZLog::writeNotice( $log );
		define_syslog_variables();
		openlog( "IVA", LOG_PID | LOG_PERROR, LOG_DAEMON );
		syslog( LOG_WARNING, "$log" );
		closelog();
}

include_once( "ezuserbb/classes/ezuser.php" );

$user =& eZUserBB::currentUser();

if ( $GLOBALS["DEBUGA"] == true ) {
        eZLog::writeNotice( "En saldos (user) ->" . print_r( $user, true ) . "|" );
}

// DebugBreak();

if ( $user )
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "saldos.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "saldos_tpl" => "saldos.tpl"
        ) );

    $session =& eZSession::globalSession();

    if ( !$session->fetch() )
    {
        $session->store();
    }

    if( !isset /*(HB AGL - Ajustes PHP5)*/( $FrAccount ) ) {
    	$FrAccount = "0";
    }
	//JAC 138815 I Saldos Consolidados ctes sin agrupación

	$perfil = $session->variable("r_perfil");
	$view = $session->variable("r_view");
	//JAC 138815 F Saldos Consolidados ctes sin agrupación

    $tr = new eZTransaccion( );
    $usr = $session->variable( "r_usr" );
    $qki = $session->variable( "r_qki" );
    $priv = $session->variable( "r_priv" );

    $result = 0;	// DBA-DBM 04Abr2008 observaciones seguridad informatica


	// ***********************************************************
	// DBA-DGM I 04Abr2008 observaciones seguridad informatica
	if($FrAccount != "0")
    {
    	// Aqui solo entra a validar la cuenta que selcciono contra las cuentas md5 calculadas previamente
		$result = MD5_Valida($ctasArreglo,$FrAccount,$session->variable( "r_qki" ),$usr);
		if ($result != 0)
		{
			$transaccion_buffer = "<p>No existe una cuenta que pueda procesar esta transacci&oacute;n.</p><br>";
		}

	}
	// DBA-DGM F 04Abr2008 observaciones seguridad informatica
	// ***********************************************************

	if( !isset /*(HB AGL - Ajustes PHP5)*/( $transaccion_buffer ) ) {
	    	$transaccion_buffer = "";
    }
	//JAC 138815 I Saldos Consolidados ctes sin agrupación
	//
	 if($result == 0)
	{
		if($perfil == "permor" || $perfil == "gobierno")
		{
			if($view != "saldo")
			{
				$FrAccount = "999999999";
			}
		}
	}
	//JAC 138815 F Saldos Consolidados ctes sin agrupación

	if ($result == 0) // DBA-DBM F 04Abr2008 observaciones seguridad informatica
	{
		//REF WMA-15001, Saldos Consolidados
		if ($FrAccount == "999999999")
		{
			$xml = "<mensaje><trxn value=\"pro\" /><accion value=\"consolidado\" /><access value=\"Process\" /><tipomsj value=\"rqs\" /><format value =\"xml\" /><CustID value=\"".urlencode(trim($usr))."\" /><cadpriv value=\"".urlencode($priv)."\" /></mensaje>";
			$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=pro&Access=FrAc&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv)."&FrAccount=".urlencode($FrAccount)."&AgruPagIni=".urlencode($AgruPagIni)."&xml=".urlencode($xml), $qki, $usr, $qki, $priv, $transaccion_buffer); // Saldos
		}
		else
		{
			$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=pro&Access=FrAc&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv)."&FrAccount=".urlencode($FrAccount)."&AgruPagIni=".urlencode($AgruPagIni), $qki, $usr, $qki, $priv, $transaccion_buffer); // Saldos
		}
		//REF WMA-15001, Saldos Consolidados
		$tr->blog($qki,"Saldos", $FrAccount, $Day, $Month, $Year, $Accion);


		// DBA-DGM F 04Abr2008 observaciones seguridad informatica
		if ($FrAccount == "0" and strpos($transaccion_buffer,"Seleccione el Cliente:")>0 ) // aqui solo entra para el 1st access de los saldos de clientes agrupados
		{
			$transaccion_buffer = MD5_Ctas($transaccion_buffer,$session->variable( "r_qki" ),$usr); // aqui agrega en la pagina los md5 que seran validos, para cada cliente seleccionado
		}
	}
	//REF WMA-15001, Saldo Consolidados
	if ($FrAccount == "999999999")
	{
		include("eztransaccion/user/include/saldosconsolidados_main_form.inc");
	$t->set_var( "transaccion_buffer", $transaccion_buffer );
	$t->pparse( "output", "saldos_consolidado_tpl" );
	}
	else
	{
	$t->set_var( "transaccion_buffer", $transaccion_buffer );
	$t->pparse( "output", "saldos_tpl" );
	}
	//REF WMA-15001, Saldo Consolidado

}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/saldos/" );

    $t->pparse( "output", "user_login_tpl" );
}

?>