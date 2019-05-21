<?php
//
//
// Created on: <1-Nov-2001 17:37:53 smb>
//
// This source file is part of IVA.
//
// Copyright ( C ) 1997-2004 Internet de Alta Calidad, S.A. de C.V.  Todos los derechos reservados. All rights reserved.
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or ( at your option ) any later version.
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


//include("estilo.inc");
//include('controles_javascript.inc');
include("eztransaccion/user/include/jscalendar/calendar-blue.css");
// JAC MAR2012 - CORRECCION MODULO DE CREDITOS INI

//DBA Arreglar calendario 
if ( !isset($_SESSION['Calendario']) )
{	
	$_SESSION['Calendario'] = 1;
	include("eztransaccion/user/include/jscalendar/calendar.js");

}else{}
//DBA Arreglar calendario 

//include("eztransaccion/user/include/jscalendar/calendar.js");
//if($session->variable("calendario")==0)
//{
//	include("eztransaccion/user/include/jscalendar/calendar.js");
//}
//$session->setVariable("calendario",$session->variable("calendario")+1);
// JAC MAR2012 - CORRECCION MODULO DE CREDITOS FIN
include("eztransaccion/user/include/js/FuncionesReimpresion.js");
//include("eztransaccion/user/include/capturapagos_main_form.inc");


$session =& eZSession::globalSession( );

if( !$session->fetch( ) )
	$session->store( );

//18sep2009 ACS   	    Altas en Línea.  Nomina
//$_SESSION["calendario"]=$_SESSION["calendario"]+ 1;
$session->setVariable("calendario",$session->variable("caledario")+1);
//18sep2009 ACS   	    Altas en Línea.  Nomina
$ini =& $GLOBALS[ "GlobalSiteIni" ];

$Language = $ini->read_var( "eZTransaccionMain", "Language" );
// $backend = $ini->read_var( "eZTransaccionMain", "Backend" );
$ServerNumber = $ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );

$backend = "gw" . $ServerNumber. $DomainPostfix;

include_once( "ezuserbb/classes/ezuser.php" );
include_once( "ezreimp/classes/ezreimp.php" );

$user =& eZUserBB::currentUser( );

// DebugBreak( );

//REF WMA-12sep2008, Fin
function ultimoDia( $mes, $anio )
{
	$ultimo_dia = 28;
	while (checkdate ($mes, $ultimo_dia, $anio) )
	{
		$ultimo_dia ++;
	}
	return ($ultimo_dia-1);
}
//REF WMA-12sep2008, Fin
//REF WMA-25jul2008, Inicio
function formatPage ( $HTML )
{

		$HTML = str_replace("<INPUT TYPE=\"BUTTON\" NAME=\"Button2\" VALUE=\"Regresar\" LANGUAGE=\"JavaScript\" ONCLICK=\"parent.history.back()\"><INPUT TYPE=\"SUBMIT\" NAME=\"Button\" VALUE=\"Ver más\" LANGUAGE=\"JavaScript\"><INPUT TYPE=\"BUTTON\" NAME=\"ButtonImprimir\" VALUE=\"Imprimir\" LANGUAGE=\"JavaScript\" ONCLICK=\"ventana()\">","",$HTML);
		$HTML = str_replace("<INPUT TYPE=\"BUTTON\" NAME=\"Button2\" VALUE=\"Regresar\" LANGUAGE=\"JavaScript\" ONCLICK=\"parent.history.back()\"><INPUT TYPE=\"BUTTON\" NAME=\"ButtonImprimir\" VALUE=\"Imprimir\" LANGUAGE=\"JavaScript\" ONCLICK=\"ventana()\">","", $HTML);
		//$HTML = ereg_replace("<INPUT TYPE\=\"SUBMIT\" NAME=\"Button\" VALUE=\"Ver más\" LANGUAGE=\"JavaScript\"> ([^>]+)","", $HTML );
		//$HTML = ereg_replace("","", $HTML );

		return ( $HTML );
}
//REF WMA-25Jul2008, Fin
if ( $user )
{

	$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "reimpresion.php" );
	$t->setAllStrings( );
	$t->set_file( array( "reimpresion_tpl" => "reimpresion.tpl" ) );
	$session =& eZSession::globalSession( );
	if ( !$session->fetch( ) )
	{
		$session->store( );
	}
	$tr = new eZTransaccion( );
	$usr = $session->variable( "r_usr" );
	$qki = $session->variable( "r_qki" );
	$priv = $session->variable( "r_priv" );
	$cliente = $session->variable( "r_cno" );//DBA
	$transaccion_buffer = "";

	if( empty( $_POST['Access'] ) )
	{
		$_POST['Access'] = "FrAc";
	}
	// $_POST['Accion'] = "movimientos";
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $_POST['Day'] ) )
	{
		$_POST['Day'] = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $_POST['FrAccount'] ) )
	{
		$_POST['FrAccount'] = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $_POST['Month'] ) )
	{
		$_POST['Month'] = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $_POST['Year'] ) )
	{
		$_POST['Year'] = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $_POST['FrAmount'] ) )
	{
		$_POST['FrAmount'] = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $_POST['top'] ) )
	{
		$_POST['top'] = "";
	}
	switch( $_POST['Access'] )
	{
		case "FrAc":
			//echo "NUNCA TRAIGO EL FIRST";
//			$particularFields = "";
			$tr->blog( $qki,"ReImpresion", $_POST['FrAccount'], $_POST['Day'], $_POST['Month'], $_POST['Year'], $_POST['Accion'] );
			//echo "Aqui ando..particulars..[$particularFields]";

        /*$transaccion_buffer = " <TABLE ALIGN=\"CENTER\" BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\" WIDTH=\"85%\">\r\n
           						<TR><TD COLSPAN=\"3\">
						        <OL><LI>Seleccione una cuenta.</LI>
								<LI>Seleccione el criterio deseado para la consulta de movimientos, por default son todos.</LI>
								<LI>Seleccione el rango de fechas para realizar la consulta, si se queda en blanco despliega todos los movimientos disponibles en el sistema.</LI>
								<LI>Seleccione el número de movimientos que desea consultar por pantalla.</LI>
								<LI>Para modificar las fechas presione <EM>Cambiar</EM>.</LI>
								<LI>Presione botón <EM>Aceptar</EM> para proceder.</LI></OL><hr noshade=\"noshade\" size=\"1\">
						        </TD></TR>\r\n";*/
			$prueba = urlencode( $f_date_c );
			//echo "prueba ....*$prueba*";
        $ret_code = $tr->PostToHost( $backend, "/IBnkIIS.dll", "Trxn=ric&Access=".urlencode( $_POST['Access'] )."&CustID=".urlencode( $usr )."&Cadpriv=".urlencode( $priv ).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer ); // movimientos
       // $particularFields = "&FrAccount=".urlencode( $_POST['FrAccount'] )."&Accion=".urlencode( $_POST['Accion'] )."&Day=".urlencode( $_POST['Day'] )."&Month=".urlencode( $_POST['Month'] )."&Year=".urlencode( $_POST['Year'] )."&DayF=".urlencode( $_POST['DayF'] )."&MonthF=".urlencode( $_POST['MonthF'] )."&YearF=".urlencode( $_POST['YearF'] )."&Pos=".urlencode( $_POST['Pos'] )."&FrAmount=".urlencode( $_POST['FrAmount'] )."&Dias=".urlencode( $_POST['Dias'] )."&FrNo=".urlencode( $_POST['FrNo'] )."&top=".urlencode( $_POST['top'] );
        break;
		case "Confirm":
		case "Process":
			//LVPR Movtos x JBoss 10Oct2007
			$MenosFin = 2;	//Es para Ov
			if ( in_array( "hst", ( preg_split( "/[,||(,\s)]+/", $ini->read_var( "site", "JBossTransactions" ) ) ) ) )
			{
				$MenosFin = 1;		//Para JBoss
			}
			//echo "Aqui ando....[$_POST['FrAccount']]";
			//LVPR Movtos x JBoss 10Oct2007
			//DMOS REIMPRESION DE COMPROBANTES PARA PLAZO DEPENDIENTE DE TIPO DE PLAZO DEL CLIENTE
			$FrAccount = $_POST['FrAccount'];
			$buscar = strpos ( $FrAccount, $cliente );
			if ($buscar === false )
			{
				//DMOS Plazos del cliente --INICIO--
				$esPzo =  substr($_POST['FrAccount'],0,5);
				//echo "0.-Aqui ando....[$esPzo]";
				if ($esPzo == "Plazo" )//Para plazos 
				{
					//echo "1.- que plazo traes....[$esPzo]";
					$FrAccount = $cliente.$_POST['FrAccount']." T";
				}
				else if ($esPzo == "Premi" ) //Premier
				{	
					$FrAccount = $_POST['FrAccount'];
					$esPzo1 =  substr($_POST['FrAccount'],0,9);
					//echo "2.- Aqui ando....[$FrAccount]";
					if ($esPzo1 == "Premier1d" )//DMOS Para plazos  Cete Premier 1 dia
					{
						$FrAccount = $cliente.$_POST['FrAccount']." T";
						$FrAccount=str_replace("Premier1d", "Plazo", $FrAccount);
						//echo "6.- Cete Premier 1dia ....[$FrAccount]";
					}
					else if ($esPzo1 == "Premier7d" ) //DMOS Para plazos  Cete Premier 7 dias
					{
						$FrAccount = $cliente.$_POST['FrAccount']." T";
						$FrAccount=str_replace("Premier7d", "Plazo", $FrAccount);
						//echo "7.- Cete Premier 7 dias ....[$FrAccount]";
					}
					else if ( $esPzo1 == "PremierB0" ) //DMOS Para plazos  Premier 28 dias
					{
						//echo "2.1- Aqui ando....[$FrAccount]";
						$FrAccount = $_POST['FrAccount'];
						$esPzo3 =  substr($_POST['FrAccount'],0,10);
						//echo "3.- Aqui ando....[$FrAccount]";
						if ($esPzo3 == "PremierB01")  //DMOS Para plazos  Premier 1 dia
						{
							$FrAccount = $cliente.$_POST['FrAccount']." T";
							$FrAccount=str_replace("PremierB01", "Plazo", $FrAccount);
							//echo "4.- Premier 1 dia- ....[$FrAccount]";
						}
						else if ($esPzo3== "PremierB07") //DMOS Para plazos  Premier 7 dias
						{	
							$FrAccount = $cliente.$_POST['FrAccount']." T";
							$FrAccount=str_replace("PremierB07", "Plazo", $FrAccount);
							//echo "5.- Premier  7 dias....[$FrAccount]";
						}						
					}
					else if ($esPzo1 == "Premier28" ) 
					{
						$FrAccount = $_POST['FrAccount'];
						$esPzo5 =  substr($_POST['FrAccount'],0,10);
						 if ($esPzo5 == "Premier281" ) 
						{
							$FrAccount = $cliente.$_POST['FrAccount']." T";
							$FrAccount=str_replace("Premier281", "Plazo", $FrAccount);//DMOS Para plazos  Premier 28 dias
							//echo "8.0 Premier 28 dias....[$FrAccount]";
						}
						else if ($esPzo5 == "Premier282" ) 
						{
							$FrAccount = $cliente.$_POST['FrAccount']." T";
							$FrAccount=str_replace("Premier282", "Plazo", $FrAccount);//DMOS Para plazos  Premier 28 dias
							//echo "8.1 Premier 28 dias....[$FrAccount]";
						}
						else if ($esPzo5 == "Premier283" ) 
						{
							$FrAccount = $cliente.$_POST['FrAccount']." T";
							$FrAccount=str_replace("Premier283", "Plazo", $FrAccount);//DMOS Para plazos  Premier 28 dias
							///echo "8.2 Premier 28 dias....[$FrAccount]";
						}
						else if ($esPzo5 == "Premier284" ) 
						{
							$FrAccount = $cliente.$_POST['FrAccount']." T";
							$FrAccount=str_replace("Premier284", "Plazo", $FrAccount);//DMOS Para plazos  Premier 28 dias
							//echo "8.3 Premier 28 dias....[$FrAccount]";
						}
						else if ($esPzo5 == "Premier28d" )//DMOS Para plazos  Cete Premier 28 dias
						{
							$FrAccount = $cliente.$_POST['FrAccount']." T";
							$FrAccount=str_replace("Premier28d", "Plazo", $FrAccount);
							//echo "8.- Cete Premier 28 dias ....[$FrAccount]";
						}
						//echo "8.4 Premier 28 dias....[$FrAccount]";
					}
						
				}					
				else if ($esPzo == "Activ" )//DMOS Plazos tipo Activo
				{
					$FrAccount = $_POST['FrAccount'];
					$esPzo4 =  substr($_POST['FrAccount'],0,9);
					//echo "9.- Voy por aqui....[$FrAccount]";
					if ($esPzo4 == "Activa07d" )//DMOS Para plazos  Activo 7 dias
						{
							$FrAccount = $cliente.$_POST['FrAccount']." T";
							$FrAccount=str_replace("Activa07d", "Plazo", $FrAccount);
							//echo "9.- Activa 7 dias....[$FrAccount]";
						}
					else if ($esPzo4 == "Activa28d" ) //DMOS Para plazos  Activo 28 dias
					{
						$FrAccount = $_POST['FrAccount'];
						$FrAccount = $cliente.$_POST['FrAccount']." T";
						$FrAccount=str_replace("Activa28d", "Plazo", $FrAccount);
						//echo "10.- Activa 28 dias....[$FrAccount]";
					}
				}//DMOS Plazos del cliente --FIN--
				else //DMOS Plazos del cliente Agrupado--INICIO--
				{
					//echo "Para Cuentas Agrupadas";echo "---antes de validacion ....[$FrAccount]";echo "---antes de validacion ....[$tipo]";
					$FrAccount = $_POST['FrAccount'];
					if ($buscar1 =(strpos ( $FrAccount, ($tipo="Plazo") ))!== false)
					{
						$FrAccount = $_POST['FrAccount']." T";
						//echo "Para cuentas Agrupadas....[$FrAccount]";
					}
					else if ($buscar1 =(strpos ( $FrAccount, ($tipo="Premier1d") ))!== false)
					{
						$FrAccount = $_POST['FrAccount']." T";
						$FrAccount=str_replace("Premier1d", "Plazo", $FrAccount);
						//echo "Para cuentas Agrupadas....[$FrAccount]";
					}
					else if ($buscar1 =(strpos ( $FrAccount, ($tipo="Premier7d") ))!== false)
					{
						$FrAccount = $_POST['FrAccount']." T";
						$FrAccount=str_replace("Premier7d", "Plazo", $FrAccount);
						//echo "Para cuentas Agrupadas....[$FrAccount]";
					}
					else if ($buscar1 =(strpos ( $FrAccount, ($tipo="Premier28d") ))!== false)
					{
						$FrAccount = $_POST['FrAccount']." T";
						$FrAccount=str_replace("Premier28d", "Plazo", $FrAccount);
						//echo "Para cuentas Agrupadas....[$FrAccount]";
					}
					else if ($buscar1 =(strpos ( $FrAccount, ($tipo="Activa07d") ))!== false)
					{
						$FrAccount = $_POST['FrAccount']." T";
						$FrAccount=str_replace("Activa07d", "Plazo", $FrAccount);
						//echo "Para cuentas Agrupadas....[$FrAccount]";
					}
					else if ($buscar1 =(strpos ( $FrAccount, ($tipo="Activa28d") ))!== false)
					{
						$FrAccount = $_POST['FrAccount']." T";
						$FrAccount=str_replace("Activa28d", "Plazo", $FrAccount);
						//echo "Para cuentas Agrupadas....[$FrAccount]";
					}
					else if ($buscar1 =(strpos ( $FrAccount, ($tipo="PremierB01") ))!== false)
					{
						$FrAccount = $_POST['FrAccount']." T";
						$FrAccount=str_replace("PremierB01", "Plazo", $FrAccount);
						//echo "Para cuentas Agrupadas....[$FrAccount]";
					}
					else if ($buscar1 =(strpos ( $FrAccount, ($tipo="PremierB07") ))!== false)
					{
						$FrAccount = $_POST['FrAccount']." T";
						$FrAccount=str_replace("PremierB07", "Plazo", $FrAccount);
						//echo "Para cuentas Agrupadas....[$FrAccount]";
					}
					else if ($buscar1 =(strpos ( $FrAccount, ($tipo="Premier281") ))!== false)
					{
						$FrAccount = $_POST['FrAccount']." T";
						$FrAccount=str_replace("Premier282", "Plazo", $FrAccount);
						//echo "Para cuentas Agrupadas....[$FrAccount]";
					}
					else if ($buscar1 =(strpos ( $FrAccount, ($tipo="Premier282") ))!== false)
					{
						$FrAccount = $_POST['FrAccount']." T";
						$FrAccount=str_replace("Premier282", "Plazo", $FrAccount);
						//echo "Para cuentas Agrupadas....[$FrAccount]";
					}
					else if ($buscar1 =(strpos ( $FrAccount, ($tipo="Premier283") ))!== false)
					{
						$FrAccount = $_POST['FrAccount']." T";
						$FrAccount=str_replace("Premier283", "Plazo", $FrAccount);
						//echo "Para cuentas Agrupadas....[$FrAccount]";
					}
					elseif ($buscar1 =(strpos ( $FrAccount, ($tipo="Premier284") ))!== false)
					{
						$FrAccount = $_POST['FrAccount']." T";
						$FrAccount=str_replace("Premier284", "Plazo", $FrAccount);
						//echo "Para cuentas Agrupadas....[$FrAccount]";
					}
					//DMOS Plazos del cliente Agrupado--FIN--
				}
				//echo "fin de ciclo....[$FrAccount]";
			}
			//DMOS REIMPRESION DE COMPROBANTES PARA PLAZO DEPENDIENTE DE TIPO DE PLAZO DEL CLIENTE
			/*
			switch( substr( $_POST['FrAccount'], strlen( $_POST['FrAccount'] )-1, 1 ) )
			{
				case "D":
					$m_s = "<h4 align=right>Cuenta de Vista: ".substr( $_POST['FrAccount'], 0, strlen( $_POST['FrAccount'] )-$MenosFin )."</h4>";
					break;
				case "T":
					$m_s = "<h4 align=right>Cuenta de Plazo: ".substr( $_POST['FrAccount'], 0, strlen( $_POST['FrAccount'] )-$MenosFin )."</h4>";
					break;
				case "L":
					$m_s = "<h4 align=right>Cuenta de Cr&eacute;dito: ".substr( $_POST['FrAccount'], 0, strlen( $_POST['FrAccount'] )-$MenosFin )."</h4>";
					break;
			}*/

		//REF WMA-12sep2009, Inicio
		//Para llenar las variables
		//
			if (trim($_POST['DayF']) == "" and trim($_POST['MonthF']) != "" and trim($_POST['YearF']) != "")
			{
				$_POST['DayF'] = ultimoDia(trim($_POST['MonthF']), trim($_POST['YearF']));
			}

			if (trim($_POST['Day']) != "" and trim($_POST['Month']) == "" and trim($_POST['Year']) == "")
			{
				$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha inicial 3 es inv&aacute;lida</b>";
				$t->set_var( "transaccion_buffer", $transaccion_buffer );
				$t->pparse( "output", "reimpresion_tpl" );
				return;
			}

			if (trim($_POST['Day']) == "" and trim($_POST['Month']) != "" and trim($_POST['Year']) == "")
			{
				$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha inicial 1 es inv&aacute;lida</b>";
				$t->set_var( "transaccion_buffer", $transaccion_buffer );
				$t->pparse( "output", "reimpresion_tpl" );
				return;
			}
			if (trim($_POST['Day']) == "" and trim($_POST['Month']) == "" and trim($_POST['Year']) != "")
			{
				$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha inicial 2 es inv&aacute;lida</b>";
				$t->set_var( "transaccion_buffer", $transaccion_buffer );
				$t->pparse( "output", "reimpresion_tpl" );
				return;
			}

			if (trim($_POST['DayF']) != "" and trim($_POST['MonthF']) == "" and trim($_POST['YearF']) == "")
			{
				$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha final es inv&aacute;lida</b>";
				$t->set_var( "transaccion_buffer", $transaccion_buffer );
				$t->pparse( "output", "reimpresion_tpl" );
				return;
			}

			if (trim($_POST['DayF']) == "" and trim($_POST['MonthF']) != "" and trim($_POST['YearF']) == "")
			{
				$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha final es inv&aacute;lida</b>";
				$t->set_var( "transaccion_buffer", $transaccion_buffer );
				$t->pparse( "output", "reimpresion_tpl" );
				return;
			}
			if (trim($_POST['DayF']) == "" and trim($_POST['MonthF']) == "" and trim($_POST['YearF']) != "")
			{
				$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha final es inv&aacute;lida</b>";
				$t->set_var( "transaccion_buffer", $transaccion_buffer );
				$t->pparse( "output", "reimpresion_tpl" );
				return;
			}
		//REF WMA-12sep2009, Fin
		//REF WMA-13aug2008, Inicio. Nueva validación para día bien formado

			if (trim($_POST['Day']) != "" and trim($_POST['Month']) != "" and trim($_POST['Year']) != "")
			{
				if (!(checkdate(trim($_POST['Month']),trim($_POST['Day']),trim($_POST['Year']))))
				{
					$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha inicial 4 es inv&aacute;lida</b>";
					$t->set_var( "transaccion_buffer", $transaccion_buffer );
					$t->pparse( "output", "reimpresion_tpl" );
					return;
				}
			}

			if (trim($_POST['DayF']) != "" and trim($_POST['MonthF']) != "" and trim($_POST['YearF']) != "")
			{
				if (!(checkdate(trim($_POST['MonthF']),trim($_POST['DayF']),trim($_POST['YearF']))))
				{
					$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha final es inv&aacute;lida</b>";
					$t->set_var( "transaccion_buffer", $transaccion_buffer );
					$t->pparse( "output", "reimpresion_tpl" );
					return;
				}
			}

			$_POST['comp'] = 1; //Para que Jboss me pinte un boton en lugar del saldo
			//this.top.compareTo("80")
			//DMOS REIMPRESION DE COMPROBANTES PLAZO
			//echo "Aqui ando..particulars..[$particularFields]";
			if ($buscar === false )
		//	if ( $esPzo == "Plazo")
			{
				if ( $esPzo == "Plazo" or $esPzo == "Premi" or $esPzo == "Activ" )//DMOS Plazos del cliente
				{		
					$particularFields = "&FrAccount=".urlencode( $FrAccount )."&Accion=".urlencode( $_POST['Accion'] )."&Day=".urlencode( $_POST['Day'] )."&Month=".urlencode( $_POST['Month'] )."&Year=".urlencode( $_POST['Year'] )."&DayF=".urlencode( $_POST['DayF'] )."&MonthF=".urlencode( $_POST['MonthF'] )."&YearF=".urlencode( $_POST['YearF'] )."&Pos=".urlencode( $_POST['Pos'] )."&FrAmount=".urlencode( $_POST['FrAmount'] )."&Dias=".urlencode( $_POST['Dias'] )."&FrNo=".urlencode( $_POST['FrNo'] )."&top=".urlencode( $_POST['top'] )."&comp=".urlencode( $_POST['comp'] );
				}
				else //DMOS Plazos del cliente Agrupado--
				{		
					$particularFields = "&FrAccount=".urlencode( $FrAccount )."&Accion=".urlencode( $_POST['Accion'] )."&Day=".urlencode( $_POST['Day'] )."&Month=".urlencode( $_POST['Month'] )."&Year=".urlencode( $_POST['Year'] )."&DayF=".urlencode( $_POST['DayF'] )."&MonthF=".urlencode( $_POST['MonthF'] )."&YearF=".urlencode( $_POST['YearF'] )."&Pos=".urlencode( $_POST['Pos'] )."&FrAmount=".urlencode( $_POST['FrAmount'] )."&Dias=".urlencode( $_POST['Dias'] )."&FrNo=".urlencode( $_POST['FrNo'] )."&top=".urlencode( $_POST['top'] )."&comp=".urlencode( $_POST['comp'] );
				}
			}	
			else
			{
				$particularFields = "&FrAccount=".urlencode( $_POST['FrAccount'] )."&Accion=".urlencode( $_POST['Accion'] )."&Day=".urlencode( $_POST['Day'] )."&Month=".urlencode( $_POST['Month'] )."&Year=".urlencode( $_POST['Year'] )."&DayF=".urlencode( $_POST['DayF'] )."&MonthF=".urlencode( $_POST['MonthF'] )."&YearF=".urlencode( $_POST['YearF'] )."&Pos=".urlencode( $_POST['Pos'] )."&FrAmount=".urlencode( $_POST['FrAmount'] )."&Dias=".urlencode( $_POST['Dias'] )."&FrNo=".urlencode( $_POST['FrNo'] )."&top=".urlencode( $_POST['top'] )."&comp=".urlencode( $_POST['comp'] );
			}//DMOS REIMPRESION DE COMPROBANTES PLAZO
		
			$tr->blog( $qki,"reimpresion", $_POST['FrAccount'], $_POST['Day'], $_POST['Month'], $_POST['Year'], $_POST['Accion'] );
			//echo "Aqui ando..particulars..[$particularFields]";
			//echo "Los particulars....+$particularFields+";
			$ret_code = $tr->PostToHost( $backend, "/IBnkIIS.dll", "Trxn=hst&Access=".urlencode( $_POST['Access'] )."&CustID=".urlencode( $usr )."&Cadpriv=".urlencode( $priv ).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer ); // movimientos
			break;

	}
	//$transaccion_buffer = "";
	$transaccion_buffer .= $m_s;

	//$ret_code = $tr->PostToHost( $backend, "/IBnkIIS.dll", "Trxn=ric&Access=".urlencode( $_POST['Access'] )."&CustID=".urlencode( $usr )."&Cadpriv=".urlencode( $priv ).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer ); // movimientos
	  //$ret_code = $tr->PostToHost( $backend, "/IBnkIIS.dll", "Trxn=ric&Access=".urlencode( $_POST['Access'] )."&CustID=".urlencode( $usr )."&Cadpriv=".urlencode( $priv ).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer ); // movimientos

	if ($_POST['Access'] == "Process")
	{
		$transaccion_buffer = str_replace("<FONT COLOR=\"WHITE\">SALDO</FONT>","<FONT COLOR=\"WHITE\">COMPROBANTE</FONT>",$transaccion_buffer);

		$t->set_var( "transaccion_buffer", $transaccion_buffer);
	}
	else
	{

		$t->set_var( "transaccion_buffer", $transaccion_buffer );

	}
	// WMA-25Jul2008, Fin Generacion de comprobantes
	$t->pparse( "output", "reimpresion_tpl" );
}
else
{
	$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "userlogin.php" );
	$t->setAllStrings( );
	$t->set_file( array ( "user_login_tpl" => "userlogin.tpl" ) );
	$t->set_var( "redirect_url", "/transaccion/reimpresion/" );
	$t->pparse( "output", "user_login_tpl" );
}
?>