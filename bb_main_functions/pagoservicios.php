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
include_once( "eztransaccion/classes/encrypt.php" );
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
include_once( "ezreimp/classes/ezreimp.php" ); //DBA Reimpresion de Comprobantes

$user =& eZUserBB::currentUser();

// DebugBreak();

if ( $user )
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "pagoservicios.php" );

    $t->setAllStrings();

	// AGG I 25Nov2005 Para poner fecha y hora en los comprobantes
	$dateTime  = new eZDateTime( );
	$timeStamp = $dateTime->timeStamp();
	$time =& date('H:i:s', $timeStamp );
	$date =& date('jMY', $timeStamp );
	// AGG F 25Nov2005 Para poner fecha y hora en los comprobantes

    $t->set_file( array(
        "pagoservicios_tpl" => "pagoservicios.tpl"
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
		
	if(!empty($_POST['Amount']))
		$Amount = $_POST['Amount'];
		
	if(!empty($_POST['Day']))
		$Day = $_POST['Day'];
		
	if(!empty($_POST['Empresa']))
		$Empresa = $_POST['Empresa'];
		
	if(!empty($_POST['FrAccount']))
		$FrAccount = $_POST['FrAccount'];
		
	if(!empty($_POST['RenCap']))
		$RenCap = $_POST['RenCap'];
		
	if(!empty($_POST['CadPriv']))
		$CadPriv = $_POST['CadPriv'];
		
	if(!empty($_POST['RFC']))
		$RFC = $_POST['RFC'];
//HB
    if(empty($Access)) {
        $Access = "FrAc";
    }
    // $Accion = "pagoservicios";
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $Amount ) ) {
    	$Amount = "";
    }
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $Day ) ) {
    	$Day = "";
    }
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $Empresa ) ) {
    	$Empresa = "";
    }
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $FrAccount ) ) {
    	$FrAccount = "";
    }
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $RenCap ) ) {
    	$RenCap = "";
    }
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $CadPriv ) ) {
    	$CadPriv = "";
    }
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $RFC ) ) {
    	$RFC = "";
    }
    switch($Access) {
    case "FrAc":
        $particularFields = "";
        $tr->blog($qki,"PagoServicios", $FrAccount, $Empresa, $Amount, $Day, $RenCap );
        break;
    case "Confirm":
		// ************************************************************
		// DGM I 09Mar2008 No validar monto en tenencias gto
		if ( $Empresa == "308")
		{
			$Amount = 1;
		}
		// DGM F 09Mar2008 No validar monto en tenencias gto
		// ************************************************************
        $particularFields = "&Day=".urlencode($Day)."&Month=".urlencode($Month)."&CadPriv=".urlencode($CadPriv)."&FrAccount=".urlencode($FrAccount)."&Empresa=".urlencode($Empresa)."&TeleB=".urlencode($TeleB)."&Amount=".urlencode($Amount)."&RenCap=".urlencode($RenCap)."&PlazaB=".urlencode($PlazaB)."&RFC=".urlencode($RFC);
        $tr->blog($qki,"PagoServicios", $FrAccount, $Empresa, $Amount, $Day, $RenCap );
        break;
    case "Process":
        $particularFields = "&Passwd=".encrypt( $code, strtolower( $usr ) )."&Day=".urlencode($Day)."&Month=".urlencode($Month)."&CadPriv=".urlencode($CadPriv)."&FrAccount=".urlencode($FrAccount)."&Empresa=".urlencode($Empresa)."&TeleB=".urlencode($TeleB)."&Amount=".urlencode($Amount)."&RenCap=".urlencode($RenCap)."&PlazaB=".urlencode($PlazaB)."&RFC=".urlencode($RFC)."&ValEsp=".urlencode($ValEsp)."&Recargo=".urlencode($Recargo)."&Descuento=".urlencode($Descuento);
        $tr->blog($qki,"PagoServicios", $FrAccount, $Empresa, $Amount, $Day, $RenCap);
        break;
    }
    $transaccion_buffer = "";
    // DebugBreak();
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=bpy&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // pago de servicios


	// ************************************************************************************************************************************************************
	// DGM I 16Mar2010 Aligerar la transacción  de pago de servicios, ahora se hace a través del combobpy.gp y se hace
	// una sola vez por la mañana.... la siguiente palabra escrita se reemplaza desde php

	$archivoServicios = "/var/www/data/colegiaturas/selectbill.txt";

	if (file_exists($archivoServicios))
	{
		$handle = fopen($archivoServicios, "r");
        $archivoLeido = fread($handle, filesize($archivoServicios));
    }
    else


	$archivoLeido = "<SELECT NAME='Empresa'>
<OPTION VALUE=''></OPTION>
<OPTION VALUE='Agua-CAASIM-Pachuca  Hidalgo-'>Agua-CAASIM-Pachuca  Hidalgo</OPTION>
<OPTION VALUE='Agua-CAVESO-Queretaro  Qro.-'>Agua-CAVESO-Queretaro  Qro.</OPTION>
<OPTION VALUE='Agua-COMAPA-Tampico  Tamaulipas-'>Agua-COMAPA-Tampico  Tamaulipas</OPTION>
<OPTION VALUE='Agua-JAPAC-Culiacan  Sin-'>Agua-JAPAC-Culiacan  Sin</OPTION>
<OPTION VALUE='Agua-JMAS CHIHUAHUA-Chihuahua  Chihuahua-'>Agua-JMAS CHIHUAHUA-Chihuahua  Chihuahua</OPTION>
<OPTION VALUE='Agua-JUMAPA CELAYA-Celaya  Guanajuato-'>Agua-JUMAPA CELAYA-Celaya  Guanajuato</OPTION>
<OPTION VALUE='Agua-SAPAL LEON LINEA-Leon  Gto.-'>Agua-SAPAL LEON LINEA-Leon  Gto.</OPTION>
<OPTION VALUE='Cable-MVS MULTIVISION-Monterrey  N.L.-'>Cable-MVS MULTIVISION-Monterrey  N.L.</OPTION>
<OPTION VALUE='Cable-SERVICIOS TELUM-Monterrey  N. L.-'>Cable-SERVICIOS TELUM-Monterrey  N. L.</OPTION>
<OPTION VALUE='Colegio-anrod school a.c.-Puebla  Puebla-'>Colegio-anrod school a.c.-Puebla  Puebla</OPTION>
<OPTION VALUE='Colegio-C DES INFANTIL CERVA-Leon  Guanajuato-'>Colegio-C DES INFANTIL CERVA-Leon  Guanajuato</OPTION>
<OPTION VALUE='Colegio-C I I T ALUMNOS ITC-Celaya  Guanajuato-'>Colegio-C I I T ALUMNOS ITC-Celaya  Guanajuato</OPTION>
<OPTION VALUE='Colegio-C I I T EXTERNOS ITC-Celaya  Guanajuato-'>Colegio-C I I T EXTERNOS ITC-Celaya  Guanajuato</OPTION>
<OPTION VALUE='Colegio-CALLI-Leon  Gto.-'>Colegio-CALLI-Leon  Gto.</OPTION>
<OPTION VALUE='Colegio-CALLI 1-Leon  Gto.-'>Colegio-CALLI 1-Leon  Gto.</OPTION>
<OPTION VALUE='Colegio-CASA Y TALLER MONTES-San Luis Potosi  S. L. P.-'>Colegio-CASA Y TALLER MONTES-San Luis Potosi  S. L. P.</OPTION>
<OPTION VALUE='Colegio-CCP LEON-Leon  Gto.-'>Colegio-CCP LEON-Leon  Gto.</OPTION>
<OPTION VALUE='Colegio-CDEIE BOSQUES COLEG-Queretaro  Qro.-'>Colegio-CDEIE BOSQUES COLEG-Queretaro  Qro.</OPTION>
<OPTION VALUE='Colegio-CDEIE BOSQUES INSCRI-Queretaro  Qro.-'>Colegio-CDEIE BOSQUES INSCRI-Queretaro  Qro.</OPTION>
<OPTION VALUE='Colegio-CEIC-Celaya  Guanajuato.-'>Colegio-CEIC-Celaya  Guanajuato.</OPTION>
<OPTION VALUE='Colegio-CEN EDU PATRIA COLEG-Leon  Gto.-'>Colegio-CEN EDU PATRIA COLEG-Leon  Gto.</OPTION>
<OPTION VALUE='Colegio-CEN EDU PATRIA CUOTA-Leon  Gto.-'>Colegio-CEN EDU PATRIA CUOTA-Leon  Gto.</OPTION>
<OPTION VALUE='Colegio-CEN EDU PATRIA INSC-Leon  Gto.-'>Colegio-CEN EDU PATRIA INSC-Leon  Gto.</OPTION>
<OPTION VALUE='Colegio-CEPETOQUI-Leon  Gto.-'>Colegio-CEPETOQUI-Leon  Gto.</OPTION>
<OPTION VALUE='Colegio-COL BRITANICO PACHUC-Pachuca  Hidalgo-'>Colegio-COL BRITANICO PACHUC-Pachuca  Hidalgo</OPTION>
<OPTION VALUE='Colegio-COL MA MONTESORI COL-Leon  Gto.-'>Colegio-COL MA MONTESORI COL-Leon  Gto.</OPTION>
<OPTION VALUE='Colegio-COLEGIO ABC-Leon  Guanajuato-'>Colegio-COLEGIO ABC-Leon  Guanajuato</OPTION>
<OPTION VALUE='Colegio-COLEGIO CHAPULTEPEC-Morelia  Michoacan-'>Colegio-COLEGIO CHAPULTEPEC-Morelia  Michoacan</OPTION>
<OPTION VALUE='Colegio-COLEGIO D GAUDI A.C.-Ensenada  Baja California-'>Colegio-COLEGIO D GAUDI A.C.-Ensenada  Baja California</OPTION>
<OPTION VALUE='Colegio-COLEGIO ESPANOL-San Luis Potosi  S.L. P.-'>Colegio-COLEGIO ESPANOL-San Luis Potosi  S.L. P.</OPTION>
<OPTION VALUE='Colegio-COLEGIO EXCELLIS-Leon  Guanajuato-'>Colegio-COLEGIO EXCELLIS-Leon  Guanajuato</OPTION>
<OPTION VALUE='Colegio-COLEGIO HUMANE-Leon  Gto.-'>Colegio-COLEGIO HUMANE-Leon  Gto.</OPTION>
<OPTION VALUE='Colegio-COLEGIO LIBER-Morelia  Michoacan-'>Colegio-COLEGIO LIBER-Morelia  Michoacan</OPTION>
<OPTION VALUE='Colegio-COLEGIO NOVEL AC-Morelia  Michoacan-'>Colegio-COLEGIO NOVEL AC-Morelia  Michoacan</OPTION>
<OPTION VALUE='Colegio-COLEGIO PANAMERICANO-Celaya  Guanajuato-'>Colegio-COLEGIO PANAMERICANO-Celaya  Guanajuato</OPTION>
<OPTION VALUE='Colegio-COLEGIO PEARSON-Ensenada  Baja California-'>Colegio-COLEGIO PEARSON-Ensenada  Baja California</OPTION>
<OPTION VALUE='Colegio-COLEGIO ROSENBLUETH-Celaya  Guanajuato-'>Colegio-COLEGIO ROSENBLUETH-Celaya  Guanajuato</OPTION>
<OPTION VALUE='Colegio-COLEGIO SALTILLENSE-Boca Del Rio  Veracruz-'>Colegio-COLEGIO SALTILLENSE-Boca Del Rio  Veracruz</OPTION>
<OPTION VALUE='Colegio-COLEGIO TICALLI-Ensenada  Baja California-'>Colegio-COLEGIO TICALLI-Ensenada  Baja California</OPTION>
<OPTION VALUE='Colegio-COLLEGE PREESCOLAR-Celaya  Guanajuato.-'>Colegio-COLLEGE PREESCOLAR-Celaya  Guanajuato.</OPTION>
<OPTION VALUE='Colegio-COLLEGE PRIMARIA-Celaya  Guanajuato.-'>Colegio-COLLEGE PRIMARIA-Celaya  Guanajuato.</OPTION>
<OPTION VALUE='Colegio-DERECHO INTEG-Celaya  Guanajuato-'>Colegio-DERECHO INTEG-Celaya  Guanajuato</OPTION>
<OPTION VALUE='Colegio-EDUCACION SEGUNDA SC-Leon  Gto.-'>Colegio-EDUCACION SEGUNDA SC-Leon  Gto.</OPTION>
<OPTION VALUE='Colegio-EDUCACION TEMPRANA-Leon  Gto.-'>Colegio-EDUCACION TEMPRANA-Leon  Gto.</OPTION>
<OPTION VALUE='Colegio-ESCUELA LIBRE DE H.-Leon  Guanajuato-'>Colegio-ESCUELA LIBRE DE H.-Leon  Guanajuato</OPTION>
<OPTION VALUE='Colegio-ESTEFANIA CAS JARDIN-Culiacan  Sin-'>Colegio-ESTEFANIA CAS JARDIN-Culiacan  Sin</OPTION>
<OPTION VALUE='Colegio-F. AGNES GONXHA A.C.-San Luis Potosi  S. L. P.-'>Colegio-F. AGNES GONXHA A.C.-San Luis Potosi  S. L. P.</OPTION>
<OPTION VALUE='Colegio-I MOYOCOYANI PACHUCA-Pachuca  Hidalgo-'>Colegio-I MOYOCOYANI PACHUCA-Pachuca  Hidalgo</OPTION>
<OPTION VALUE='Colegio-ING ARTURO NIETO PIN-Celaya  Guanajuato.-'>Colegio-ING ARTURO NIETO PIN-Celaya  Guanajuato.</OPTION>
<OPTION VALUE='Colegio-INST B. JEAN PIAGET-Culiacan  Sin-'>Colegio-INST B. JEAN PIAGET-Culiacan  Sin</OPTION>
<OPTION VALUE='Colegio-INST CULT M J OTHON-San Luis Potosi  S. L. P.-'>Colegio-INST CULT M J OTHON-San Luis Potosi  S. L. P.</OPTION>
<OPTION VALUE='Colegio-INST CULTURAL D LEON-Leon  Gto.-'>Colegio-INST CULTURAL D LEON-Leon  Gto.</OPTION>
<OPTION VALUE='Colegio-INST JAVIER MINA-Leon  Guanajuato-'>Colegio-INST JAVIER MINA-Leon  Guanajuato</OPTION>
<OPTION VALUE='Colegio-INST SOR JUANA INES-Morelia  Michoacan-'>Colegio-INST SOR JUANA INES-Morelia  Michoacan</OPTION>
<OPTION VALUE='Colegio-INSTITUTO ANDERSEN-Celaya  Guanajuato.-'>Colegio-INSTITUTO ANDERSEN-Celaya  Guanajuato.</OPTION>
<OPTION VALUE='Colegio-INSTITUTO ASUNCION-San Luis Potosi  S. L. P.-'>Colegio-INSTITUTO ASUNCION-San Luis Potosi  S. L. P.</OPTION>
<OPTION VALUE='Colegio-INSTITUTO DEL VALLE-Leon  Guanajuato-'>Colegio-INSTITUTO DEL VALLE-Leon  Guanajuato</OPTION>
<OPTION VALUE='Colegio-INSTITUTO INGLES MEX-Mexico  D. F.-'>Colegio-INSTITUTO INGLES MEX-Mexico  D. F.</OPTION>
<OPTION VALUE='Colegio-INSTITUTO LUX COLEG-Leon  Guanajuato.-'>Colegio-INSTITUTO LUX COLEG-Leon  Guanajuato.</OPTION>
<OPTION VALUE='Colegio-INSTITUTO LUX INSCR-Leon  Guanajuato-'>Colegio-INSTITUTO LUX INSCR-Leon  Guanajuato</OPTION>
<OPTION VALUE='Colegio-INSTITUTO MONTESSORI-Culiacan  Sin-'>Colegio-INSTITUTO MONTESSORI-Culiacan  Sin</OPTION>
<OPTION VALUE='Colegio-INSTITUTO MONTRER SC-Morelia  Michoacan-'>Colegio-INSTITUTO MONTRER SC-Morelia  Michoacan</OPTION>
<OPTION VALUE='Colegio-INSTITUTO SIQUEIROS-Leon  Guanajuato-'>Colegio-INSTITUTO SIQUEIROS-Leon  Guanajuato</OPTION>
<OPTION VALUE='Colegio-INTEGRALES-Celaya  Guanajuato-'>Colegio-INTEGRALES-Celaya  Guanajuato</OPTION>
<OPTION VALUE='Colegio-JACOB KARL GRIMM-Celaya  Guanajuato-'>Colegio-JACOB KARL GRIMM-Celaya  Guanajuato</OPTION>
<OPTION VALUE='Colegio-JEAN PIAGET DEL RIO-Culiacan  Sin.-'>Colegio-JEAN PIAGET DEL RIO-Culiacan  Sin.</OPTION>
<OPTION VALUE='Colegio-KINDER GYM TOIS COL-Leon  Gto.-'>Colegio-KINDER GYM TOIS COL-Leon  Gto.</OPTION>
<OPTION VALUE='Colegio-KINDES GYM TOIS INSC-Leon  Gto.-'>Colegio-KINDES GYM TOIS INSC-Leon  Gto.</OPTION>
<OPTION VALUE='Colegio-LA SALLE PANORAM COL-Leon  Guanajuato-'>Colegio-LA SALLE PANORAM COL-Leon  Guanajuato</OPTION>
<OPTION VALUE='Colegio-LA SALLE PANORAM INS-Leon  Guanajuato-'>Colegio-LA SALLE PANORAM INS-Leon  Guanajuato</OPTION>
<OPTION VALUE='Colegio-MONT CHIARAVALLE COL-Leon  Guanajuato-'>Colegio-MONT CHIARAVALLE COL-Leon  Guanajuato</OPTION>
<OPTION VALUE='Colegio-MONT CHIARAVALLE INS-Leon  Guanajuato-'>Colegio-MONT CHIARAVALLE INS-Leon  Guanajuato</OPTION>
<OPTION VALUE='Colegio-O DECROLY JARDIN-Culiacan  Sin-'>Colegio-O DECROLY JARDIN-Culiacan  Sin</OPTION>
<OPTION VALUE='Colegio-O DECROLY PRIMARIA-Culiacan  Sin.-'>Colegio-O DECROLY PRIMARIA-Culiacan  Sin.</OPTION>
<OPTION VALUE='Colegio-O DECROLY SECUNDARIA-Culiacan  Sin-'>Colegio-O DECROLY SECUNDARIA-Culiacan  Sin</OPTION>
<OPTION VALUE='Colegio-OXFORD KINDER.-Celaya  Guanajuato.-'>Colegio-OXFORD KINDER.-Celaya  Guanajuato.</OPTION>
<OPTION VALUE='Colegio-OXFORD PRIMARIA  SEC-Celaya  Guanajuato.-'>Colegio-OXFORD PRIMARIA  SEC-Celaya  Guanajuato.</OPTION>
<OPTION VALUE='Colegio-POLITEC COLEG P UNIC-Leon  Gto.-'>Colegio-POLITEC COLEG P UNIC-Leon  Gto.</OPTION>
<OPTION VALUE='Colegio-POLITEC COLEGIATURAS-Leon  Gto.-'>Colegio-POLITEC COLEGIATURAS-Leon  Gto.</OPTION>
<OPTION VALUE='Colegio-POLITEC INSUMOS-Leon  Gto.-'>Colegio-POLITEC INSUMOS-Leon  Gto.</OPTION>
<OPTION VALUE='Colegio-POLITEC RE INSCRIPCI-Leon  Gto.-'>Colegio-POLITEC RE INSCRIPCI-Leon  Gto.</OPTION>
<OPTION VALUE='Colegio-PRIM TOMASA ESTEVEZ-San Luis Potosi  S.L. P.-'>Colegio-PRIM TOMASA ESTEVEZ-San Luis Potosi  S.L. P.</OPTION>
<OPTION VALUE='Colegio-ROCIO RODRIGUEZ B-Leon  Guanajuato-'>Colegio-ROCIO RODRIGUEZ B-Leon  Guanajuato</OPTION>
<OPTION VALUE='Colegio-SAN FRANCISCO JAVIER-San Luis Potosi  S. L. P.-'>Colegio-SAN FRANCISCO JAVIER-San Luis Potosi  S. L. P.</OPTION>
<OPTION VALUE='Colegio-SEBEC PREPARATORIA-Culiacan  Sin-'>Colegio-SEBEC PREPARATORIA-Culiacan  Sin</OPTION>
<OPTION VALUE='Colegio-SEBEC PRIMARIA-Culiacan  Sin-'>Colegio-SEBEC PRIMARIA-Culiacan  Sin</OPTION>
<OPTION VALUE='Colegio-SEBEC SECUNDARIA-Culiacan  Sin-'>Colegio-SEBEC SECUNDARIA-Culiacan  Sin</OPTION>
<OPTION VALUE='Colegio-SEMILLITAS DEL IIM-Mexico  D.F.-'>Colegio-SEMILLITAS DEL IIM-Mexico  D.F.</OPTION>
<OPTION VALUE='Colegio-SUPERIORES-Celaya  Guanajuato-'>Colegio-SUPERIORES-Celaya  Guanajuato</OPTION>
<OPTION VALUE='Colegio-TEC DE ROQUE-Celaya  Guanajuato.-'>Colegio-TEC DE ROQUE-Celaya  Guanajuato.</OPTION>
<OPTION VALUE='Colegio-TODAYS KIDS-Leon  Gto.-'>Colegio-TODAYS KIDS-Leon  Gto.</OPTION>
<OPTION VALUE='Colegio-U  DE LA SALLE COLEG-Leon  Guanajuato-'>Colegio-U  DE LA SALLE COLEG-Leon  Guanajuato</OPTION>
<OPTION VALUE='Colegio-U  DE LA SALLE INSC-Leon  Guanajuato.-'>Colegio-U  DE LA SALLE INSC-Leon  Guanajuato.</OPTION>
<OPTION VALUE='Colegio-U  DE LA SALLE REINS-Leon  Guanajuato-'>Colegio-U  DE LA SALLE REINS-Leon  Guanajuato</OPTION>
<OPTION VALUE='Colegio-U DE LA SALLE COLEG2-Leon  Gto.-'>Colegio-U DE LA SALLE COLEG2-Leon  Gto.</OPTION>
<OPTION VALUE='Colegio-U LATINA DE AMERICA-Morelia  Michoacan-'>Colegio-U LATINA DE AMERICA-Morelia  Michoacan</OPTION>
<OPTION VALUE='Colegio-UNIV CUAUHTEMOC GDL-Guadalajara  Jal.-'>Colegio-UNIV CUAUHTEMOC GDL-Guadalajara  Jal.</OPTION>
<OPTION VALUE='Colegio-UNIVBAJIO PREPARATOR-Leon  Gto.-'>Colegio-UNIVBAJIO PREPARATOR-Leon  Gto.</OPTION>
<OPTION VALUE='Colegio-UNIVBAJIO UNIVERSIDA-Leon  Gto.-'>Colegio-UNIVBAJIO UNIVERSIDA-Leon  Gto.</OPTION>
<OPTION VALUE='Colegio-UNIVERSIDAD IBERO-Leon  Guanajuato-'>Colegio-UNIVERSIDAD IBERO-Leon  Guanajuato</OPTION>
<OPTION VALUE='Colegio-VIRGINIA RANGEL V-Leon  Guanajuato-'>Colegio-VIRGINIA RANGEL V-Leon  Guanajuato</OPTION>
<OPTION VALUE='Gas-GAS NATURAL-Monterrey  N.L.-'>Gas-GAS NATURAL-Monterrey  N.L.</OPTION>
<OPTION VALUE='Gas-GAS URIBE SA DE CV-Mexico  D.F.-'>Gas-GAS URIBE SA DE CV-Mexico  D.F.</OPTION>
<OPTION VALUE='Gobierno-HRAEB/CURSO/TS-Leon  Guanajuato-'>Gobierno-HRAEB/CURSO/TS-Leon  Guanajuato</OPTION>
<OPTION VALUE='Gobierno-IEPS Michoacan-Morelia  Michoacan-'>Gobierno-IEPS Michoacan-Morelia  Michoacan</OPTION>
<OPTION VALUE='Gobierno-IMP. EST. CEDULAR 07-Guanajuato  Guanajuato.-'>Gobierno-IMP. EST. CEDULAR 07-Guanajuato  Guanajuato.</OPTION>
<OPTION VALUE='Gobierno-Impto. Estatal Qro-Queretaro  Qro.-'>Gobierno-Impto. Estatal Qro-Queretaro  Qro.</OPTION>
<OPTION VALUE='Gobierno-IMPTOS. PUEBLA-Puebla  Puebla-'>Gobierno-IMPTOS. PUEBLA-Puebla  Puebla</OPTION>
<OPTION VALUE='Gobierno-Impuestos Sinaloa-Culiacan  Sinaloa-'>Gobierno-Impuestos Sinaloa</OPTION>
<OPTION VALUE='Gobierno-MUNICIPIO AGS-Aguascalientes  Ags.-'>Gobierno-MUNICIPIO AGS-Aguascalientes  Ags.</OPTION>
<OPTION VALUE='Gobierno-Nom_Hotelero Morelia-Morelia  Michoacan-'>Gobierno-Nom_Hotelero Morelia-Morelia  Michoacan</OPTION>
<OPTION VALUE='Gobierno-NOMINA MORELIA-Morelia  Michoacan-'>Gobierno-NOMINA MORELIA-Morelia  Michoacan</OPTION>
<OPTION VALUE='Gobierno-PAGOS MPIO CULIACAN-Culiacan  Sin-'>Gobierno-PAGOS MPIO CULIACAN-Culiacan  Sin</OPTION>
<OPTION VALUE='Gobierno-pemex-gas-Mexico  D.F.-'>Gobierno-pemex-gas-Mexico  D.F.</OPTION>
<OPTION VALUE='Gobierno-PREDIAL CELAYA-Celaya  Guanajuato-'>Gobierno-PREDIAL CELAYA-Celaya  Guanajuato</OPTION>
<OPTION VALUE='Gobierno-PREDIAL IRAPUATO-Irapuato  Guanajuato.-'>Gobierno-PREDIAL IRAPUATO-Irapuato  Guanajuato.</OPTION>
<OPTION VALUE='Gobierno-PREDIAL MORELIA 1-Morelia  Michoacan-'>Gobierno-PREDIAL MORELIA 1-Morelia  Michoacan</OPTION>
<OPTION VALUE='Gobierno-Predial Pachuca-Pachuca  Hidalgo-'>Gobierno-Predial Pachuca-Pachuca  Hidalgo</OPTION>
<OPTION VALUE='Gobierno-PREDIAL S.L.P-San Luis Potosi  S. L. P.-'>Gobierno-PREDIAL S.L.P-San Luis Potosi  S. L. P.</OPTION>
<OPTION VALUE='Gobierno-PREDIAL SALAMANCA 10-Salamanca  Guanajuato.-'>Gobierno-PREDIAL SALAMANCA 10-Salamanca  Guanajuato.</OPTION>
<OPTION VALUE='Gobierno-PREDIAL SAN PEDRO-San Pedro Garza Garcia  N.L.-'>Gobierno-PREDIAL SAN PEDRO-San Pedro Garza Garcia  N.L.</OPTION>
<OPTION VALUE='Gobierno-PREDIAL SN MIGUEL AL-San Miguel De Allende  Gto.-'>Gobierno-PREDIAL SN MIGUEL AL-San Miguel De Allende  Gto.</OPTION>
<OPTION VALUE='Gobierno-PREDIAL TLAQUEPAQUE-Tlaquepaque  Jalisco-'>Gobierno-PREDIAL TLAQUEPAQUE-Tlaquepaque  Jalisco</OPTION>
<OPTION VALUE='Gobierno-PREDIAL ZAMORA-Zamora  Michoacan-'>Gobierno-PREDIAL ZAMORA-Zamora  Michoacan</OPTION>
<OPTION VALUE='Gobierno-Recaudacion Pachuca-Pachuca  Hidalgo-'>Gobierno-Recaudacion Pachuca-Pachuca  Hidalgo</OPTION>
<OPTION VALUE='Gobierno-Repecos Morelia-Morelia  Michoacan-'>Gobierno-Repecos Morelia-Morelia  Michoacan</OPTION>
<OPTION VALUE='Gobierno-Tenencias-Guanajuato  Guanajuato.-'>Gobierno-Tenencias-Guanajuato  Guanajuato.</OPTION>
<OPTION VALUE='Gobierno-Tenencias Michoacan-Morelia  Michoacan-'>Gobierno-Tenencias Michoacan-Morelia  Michoacan</OPTION>
<OPTION VALUE='Gobierno-TENENCIAS SAN LUIS P-San Luis Potosi  S.L.P.-'>Gobierno-TENENCIAS SAN LUIS P-San Luis Potosi  S.L.P.</OPTION>
<OPTION VALUE='Luz-C.F.E. NORTE-Monterrey  N.L.-'>Luz-C.F.E. NORTE-Monterrey  N.L.</OPTION>
<OPTION VALUE='Mantenimiento-ASOC DE DESARR LAGO-Leon  Guanajuato-'>Mantenimiento-ASOC DE DESARR LAGO-Leon  Guanajuato</OPTION>
<OPTION VALUE='Mantenimiento-ASOC QRO DE INMO-Queretaro  Qro.-'>Mantenimiento-ASOC QRO DE INMO-Queretaro  Qro.</OPTION>
<OPTION VALUE='Mantenimiento-BOSQUE AZUL-Leon  Gto.-'>Mantenimiento-BOSQUE AZUL-Leon  Gto.</OPTION>
<OPTION VALUE='Mantenimiento-C COMERCIAL ALTARIA-Leon  Gto.-'>Mantenimiento-C COMERCIAL ALTARIA-Leon  Gto.</OPTION>
<OPTION VALUE='Mantenimiento-C EN OFTALMOLOGIA SC-Leon  Guanajuato-'>Mantenimiento-C EN OFTALMOLOGIA SC-Leon  Guanajuato</OPTION>
<OPTION VALUE='Mantenimiento-COLONOS HDA PARQUE-San Luis Potosi  S.L.P.-'>Mantenimiento-COLONOS HDA PARQUE-San Luis Potosi  S.L.P.</OPTION>
<OPTION VALUE='Mantenimiento-COLONOS LAGO ONEGA-Leon  Guanajuato-'>Mantenimiento-COLONOS LAGO ONEGA-Leon  Guanajuato</OPTION>
<OPTION VALUE='Mantenimiento-CONDESA FINANCIERA-Mexico  D. F.-'>Mantenimiento-CONDESA FINANCIERA-Mexico  D. F.</OPTION>
<OPTION VALUE='Mantenimiento-Credicor Arrendadora-Irapuato  Guanajuato.-'>Mantenimiento-Credicor Arrendadora-Irapuato  Guanajuato.</OPTION>
<OPTION VALUE='Mantenimiento-Credicor Mexicano Sa-Irapuato  Guanajuato.-'>Mantenimiento-Credicor Mexicano Sa-Irapuato  Guanajuato.</OPTION>
<OPTION VALUE='Mantenimiento-I.GUERRERO DEL BAJIO-Leon  Guanajuato-'>Mantenimiento-I.GUERRERO DEL BAJIO-Leon  Guanajuato</OPTION>
<OPTION VALUE='Mantenimiento-ICARSA-Culiacan  Sin-'>Mantenimiento-ICARSA-Culiacan  Sin</OPTION>
<OPTION VALUE='Mantenimiento-JURIQUILLA SANTA FE-Queretaro  Qro.-'>Mantenimiento-JURIQUILLA SANTA FE-Queretaro  Qro.</OPTION>
<OPTION VALUE='Mantenimiento-LA PATINA II-Leon  Guanajuato-'>Mantenimiento-LA PATINA II-Leon  Guanajuato</OPTION>
<OPTION VALUE='Mantenimiento-LOMAS GRAN JARDIN-Leon  Guanajuato.-'>Mantenimiento-LOMAS GRAN JARDIN-Leon  Guanajuato.</OPTION>
<OPTION VALUE='Mantenimiento-LUIS A ALANIS LINREF-Leon  Guanajuato-'>Mantenimiento-LUIS A ALANIS LINREF-Leon  Guanajuato</OPTION>
<OPTION VALUE='Mantenimiento-MIRADOR GJARDIN CASA-Leon  Guanajuato.-'>Mantenimiento-MIRADOR GJARDIN CASA-Leon  Guanajuato.</OPTION>
<OPTION VALUE='Mantenimiento-MOTOS AMERICA SA-Leon  Guanajuato.-'>Mantenimiento-MOTOS AMERICA SA-Leon  Guanajuato.</OPTION>
<OPTION VALUE='Mantenimiento-PLASMA AUTOMATION-Leon  Guanajuato-'>Mantenimiento-PLASMA AUTOMATION-Leon  Guanajuato</OPTION>
<OPTION VALUE='Mantenimiento-PORTA FONTANA-Leon  Guanajuato.-'>Mantenimiento-PORTA FONTANA-Leon  Guanajuato.</OPTION>
<OPTION VALUE='Mantenimiento-QUINTAS DE MONTICELL-Aguascalientes  Ags.-'>Mantenimiento-QUINTAS DE MONTICELL-Aguascalientes  Ags.</OPTION>
<OPTION VALUE='Mantenimiento-SERVIACERO COMERCIAL-Leon  Guanajuato-'>Mantenimiento-SERVIACERO COMERCIAL-Leon  Guanajuato</OPTION>
<OPTION VALUE='Mantenimiento-VALLE DEL MORAL-Leon  Gto.-'>Mantenimiento-VALLE DEL MORAL-Leon  Gto.</OPTION>
<OPTION VALUE='Mantenimiento-VILLAS PACIFICO AZUL-Guadalajara  Jal.-'>Mantenimiento-VILLAS PACIFICO AZUL-Guadalajara  Jal.</OPTION>
<OPTION VALUE='Membresia-CLUB SPORTING CELAYA-Celaya  Guanajuato-'>Membresia-CLUB SPORTING CELAYA-Celaya  Guanajuato</OPTION>
<OPTION VALUE='Membresia-DTC-Guadalajara  Jal.-'>Membresia-DTC-Guadalajara  Jal.</OPTION>
<OPTION VALUE='Membresia-PASE-Guadalajara  Jal.-'>Membresia-PASE-Guadalajara  Jal.</OPTION>
<OPTION VALUE='Mensualidad-ADM LOS SAUCES-Guadalajara  Jalisco-'>Mensualidad-ADM LOS SAUCES-Guadalajara  Jalisco</OPTION>
<OPTION VALUE='Mensualidad-CANTIL CONSTRUCTORA-Queretaro  Qro.-'>Mensualidad-CANTIL CONSTRUCTORA-Queretaro  Qro.</OPTION>
<OPTION VALUE='Mensualidad-CAREVAJ AC-Morelia  Michoacan-'>Mensualidad-CAREVAJ AC-Morelia  Michoacan</OPTION>
<OPTION VALUE='Mensualidad-CITYTEL-Leon  Gto.-'>Mensualidad-CITYTEL-Leon  Gto.</OPTION>
<OPTION VALUE='Mensualidad-CM DESARROLLOS-San Luis Potosi  S.L. P.-'>Mensualidad-CM DESARROLLOS-San Luis Potosi  S.L. P.</OPTION>
<OPTION VALUE='Mensualidad-des inmob itzicuaro-Morelia  Michoacan-'>Mensualidad-des inmob itzicuaro-Morelia  Michoacan</OPTION>
<OPTION VALUE='Mensualidad-DIGICUENTA SA DE CV-Mexico  D. F.-'>Mensualidad-DIGICUENTA SA DE CV-Mexico  D. F.</OPTION>
<OPTION VALUE='Mensualidad-EMPACADORA CELAYA-Celaya  Guanajuato-'>Mensualidad-EMPACADORA CELAYA-Celaya  Guanajuato</OPTION>
<OPTION VALUE='Mensualidad-FERRETERA GUTIERREZ-Leon  Guanajuato-'>Mensualidad-FERRETERA GUTIERREZ-Leon  Guanajuato</OPTION>
<OPTION VALUE='Mensualidad-FRACC.  EL TIGRE-Guadalajara  Jal.-'>Mensualidad-FRACC.  EL TIGRE-Guadalajara  Jal.</OPTION>
<OPTION VALUE='Mensualidad-FUENTES DE LA LAJA-Leon  Gto.-'>Mensualidad-FUENTES DE LA LAJA-Leon  Gto.</OPTION>
<OPTION VALUE='Mensualidad-GI ARRENDARORA-Mexicali  Baja California-'>Mensualidad-GI ARRENDARORA-Mexicali  Baja California</OPTION>
<OPTION VALUE='Mensualidad-GRUPO CAISA-QUERETARO  QUERETARO-'>Mensualidad-GRUPO CAISA-QUERETARO  QUERETARO</OPTION>
<OPTION VALUE='Mensualidad-HACIENDA EUCALIPTOS-Leon  Gto.-'>Mensualidad-HACIENDA EUCALIPTOS-Leon  Gto.</OPTION>
<OPTION VALUE='Mensualidad-HOYSIFIO-Pachuca  Hidalgo-'>Mensualidad-HOYSIFIO-Pachuca  Hidalgo</OPTION>
<OPTION VALUE='Mensualidad-INDUSTRIAS SANPER-Leon  Gto.-'>Mensualidad-INDUSTRIAS SANPER-Leon  Gto.</OPTION>
<OPTION VALUE='Mensualidad-inmob casa inversion-Guadalajara  Jal-'>Mensualidad-inmob casa inversion-Guadalajara  Jal</OPTION>
<OPTION VALUE='Mensualidad-INMOBILIARIA LA PENA-Queretaro  Qro.-'>Mensualidad-INMOBILIARIA LA PENA-Queretaro  Qro.</OPTION>
<OPTION VALUE='Mensualidad-INMOBILIARIA LOM SA-Puerto Vallarta  Jal.-'>Mensualidad-INMOBILIARIA LOM SA-Puerto Vallarta  Jal.</OPTION>
<OPTION VALUE='Mensualidad-LA MARIPOSA DE LEON-Leon  Gto.-'>Mensualidad-LA MARIPOSA DE LEON-Leon  Gto.</OPTION>
<OPTION VALUE='Mensualidad-MERCA PAPEL-Leon  Gto.-'>Mensualidad-MERCA PAPEL-Leon  Gto.</OPTION>
<OPTION VALUE='Mensualidad-NAUTICA LEONESA AC-Leon  Gto.-'>Mensualidad-NAUTICA LEONESA AC-Leon  Gto.</OPTION>
<OPTION VALUE='Mensualidad-OZ AUTOMOTRIZ-Guadalajara  Jal-'>Mensualidad-OZ AUTOMOTRIZ-Guadalajara  Jal</OPTION>
<OPTION VALUE='Mensualidad-PADRES FAM CUMBRES-Leon  Guanajuato-'>Mensualidad-PADRES FAM CUMBRES-Leon  Guanajuato</OPTION>
<OPTION VALUE='Mensualidad-PRIV DEL PEDREGAL-San Luis Potosi  S. L. P.-'>Mensualidad-PRIV DEL PEDREGAL-San Luis Potosi  S. L. P.</OPTION>
<OPTION VALUE='Mensualidad-PUNTA DEL ESTE-Leon  Gto.-'>Mensualidad-PUNTA DEL ESTE-Leon  Gto.</OPTION>
<OPTION VALUE='Mensualidad-REDPACK SA DE CV-Mexico  D.F.-'>Mensualidad-REDPACK SA DE CV-Mexico  D.F.</OPTION>
<OPTION VALUE='Mensualidad-RINC SAN ISIDRO-Guadalajara  Jalisco-'>Mensualidad-RINC SAN ISIDRO-Guadalajara  Jalisco</OPTION>
<OPTION VALUE='Mensualidad-SIERRA AZUL SLP-San Luis Potosi  S. L. P.-'>Mensualidad-SIERRA AZUL SLP-San Luis Potosi  S. L. P.</OPTION>
<OPTION VALUE='Mensualidad-SONTERRA GRUPO-Queretaro  Qro.-'>Mensualidad-SONTERRA GRUPO-Queretaro  Qro.</OPTION>
<OPTION VALUE='Mensualidad-WITTE-Guadalajara  Jal.-'>Mensualidad-WITTE-Guadalajara  Jal.</OPTION>
<OPTION VALUE='Seguros-ROYAL SUNALLIANCE-Mexico  D. F.-'>Seguros-ROYAL SUNALLIANCE-Mexico  D. F.</OPTION>
<OPTION VALUE='Suscripciones-ALEJANDRO MAGNO CONQ-Mazatlan  Sinaloa-'>Suscripciones-ALEJANDRO MAGNO CONQ-Mazatlan  Sinaloa</OPTION>
<OPTION VALUE='Suscripciones-CONSTRUCTORA MORELIA-Morelia  Michoacan-'>Suscripciones-CONSTRUCTORA MORELIA-Morelia  Michoacan</OPTION>
<OPTION VALUE='Suscripciones-INGLES SIN BARRERAS-Mazatlan  Sinaloa-'>Suscripciones-INGLES SIN BARRERAS-Mazatlan  Sinaloa</OPTION>
<OPTION VALUE='Suscripciones-REZZA EDITORES-Leon  Guanajuato-'>Suscripciones-REZZA EDITORES-Leon  Guanajuato</OPTION>
<OPTION VALUE='Suscripciones-SERV CORP DE EMPRESA-Mazatlan  Sinaloa-'>Suscripciones-SERV CORP DE EMPRESA-Mazatlan  Sinaloa</OPTION>
<OPTION VALUE='Suscripciones-sollc_gpoindsaltillo-Monterrey  N. L.-'>Suscripciones-sollc_gpoindsaltillo-Monterrey  N. L.</OPTION>
<OPTION VALUE='Suscripciones-tarjetas marcatel-Monterrey  N. L.-'>Suscripciones-tarjetas marcatel-Monterrey  N. L.</OPTION>
<OPTION VALUE='Suscripciones-TREEMARKET-Mazatlan  Sinaloa-'>Suscripciones-TREEMARKET-Mazatlan  Sinaloa</OPTION>
<OPTION VALUE='Tel.Celular-TELCEL-Mexico  D. F.-'>Tel.Celular-TELCEL-Mexico  D. F.</OPTION>
</SELECT>";
	$transaccion_buffer = str_replace("reemplazaesto_con_selectbill",$archivoLeido,$transaccion_buffer);

	// DGM I 16Mar2010 Aligerar la transacción  de pago de servicios, ahora se hace a través del combobpy.gp y se hace
	// una sola vez por la mañana.... la siguiente palabra escrita se reemplaza desde php
	// ************************************************************************************************************************************************************
	// AGG I 25Nov2005 Generacion de comprobantes
	if ($Access == "Process" and strpos($transaccion_buffer,"No. de Autoriza") != false)
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

		//var_dump ( $date);

		$ini =& INIFile::globalINI();
		$SecureServer = $ini->read_var( "site", "SecureServer" );
		$SecureServerType = $ini->read_var( "site", "SecureServerType" );
		$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );
		$ServerNumber = $ini->read_var( "site", "ServerNumber" );

		if ( $Comprobante == "Activo" )
		{
		$Pagina = "<SCRIPT LANGUAGE='JavaScript'> function ventana() { var windowW = 800; var windowH = 450; var windowX = 10; var windowY = 10; var title = 'Recibo'; s = 'width='+windowW+',height='+windowH; cadena=\"<HTML><HEAD><link REL='STYLESHEET' HREF='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/recibos.css' TYPE='text/css'><TITLE>Pago de Servicios</TITLE>";
		$Pagina = $Pagina."<SCRIPT LANGUAGE='JavaScript'> function window_onbeforeprint() { Imprimir.style.visibility = 'hidden'; Salir.style.visibility = 'hidden';} function window_onafterprint() { Imprimir.style.visibility = 'visible'; Salir.style.";
		$Pagina = $Pagina."visibility = 'visible'; }\"+\"<\"+\"/\"+\"SCRIPT></HEAD><BODY LANGUAGE='javascript' onbeforeprint='return window_onbeforeprint()' onafterprint='return window_onafterprint()'>";
		$Pagina = $Pagina."<INPUT type='button' value='Imprimir' id=Imprimir name=Imprimir onclick='window.print();'>&nbsp;<INPUT type='button' value=' Salir  ' id=Salir name=Salir onclick='window.close();'>";
		$Pagina = $Pagina."<BR>";
		$Pagina = $Pagina."<P><CENTER>";
		$Pagina = $Pagina."<IMG SRC='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/LOGO_BB.GIF' ALIGN= 'TOP' Banco del Bajio>";
		$Pagina = $Pagina."</P></CENTER>";
		$Pagina = $Pagina."<H2>Pago de Servicios</H2>";
		$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
		$Pagina = $Pagina."<P ALIGN='RIGHT'><B><FONT>$date $time</FONT></B></P>";
		$Pagina = $Pagina.$buffer2;
		$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
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
		$QryRIC -> store($CustID,$FrAccount,$buffer2,"bpy","Pago de Servicios",$FechaHora);*/
		//DBA Reimpresion de Comprobantes
	}
    $t->set_var( "transaccion_buffer", $transaccion_buffer.$Pagina );
	// AGG F 25Nov2005 Generacion de comprobantes

    //$t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "pagoservicios_tpl" );
}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/pagoservicios/" );

    $t->pparse( "output", "user_login_tpl" );
}
?>
