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

include_once( "classes/ezhttptool.php" );

$ini =& INIFile::globalINI();
$GlobalSectionID = $ini->read_var( "eZTransaccionMain", "DefaultSection" );

if ( $GLOBALS["DEBUGA"] == true ) {
        eZLog::writeNotice( "En datasupplier de eztransaccion edit (url_array) ->" . print_r( $url_array, true ) . "|" );
}

// JAC JUN2012 INI
// JAC MAR2012 AUDITORIA INI
if ( $session->variable( "r_tkchk" ) != "True"  && $ini->read_var( "site", "TknObligatorio" ) == "si" ) 
{
	// echo "SALIDENDO";
	$url_array[2] = "garbage";
}
// JAC MAR2012 AUDITORIA FIN
// JAC JUN2012 INI

switch ( $url_array[2] ) {
	case "activateasb":
		include( "eztransaccion/user/activateasb.php" );
		break;
	case "synchronizeasb":
		include( "eztransaccion/user/synchronizeasb.php" );
		break;
	case "pinasb":
		include( "eztransaccion/user/pinasb.php" );
		break;
	case "testasb":
		include( "eztransaccion/user/testasb.php" );
		break;
	case "saldos" :
	{
		include( "eztransaccion/user/saldos.php" );
	}
	break;
	//aluna 13may2008 I Saldos Credito
	case "saldoscre" :
	{
		include( "eztransaccion/user/saldoscre.php" );
	}
	break;
	//aluna 13may2008 F Saldos Credito

	case "saldosMesa" :
	{
		include( "eztransaccion/user/saldosMesa.php" );
	}
	break;

	case "saldosFondos" :
	{
		include( "eztransaccion/user/saldosFondos.php" );
	}
    break;

//-- EVG-EDS Bajionet CNBV 01042005
    case "bitacoraIBNK" :
    {
        include( "eztransaccion/user/bitacoraIBNK.php" );
    }
    break;
//-- EVG-EDS Bajionet CNBV 01042005

    case "estadosdecuenta" :
    {
        include( "eztransaccion/user/estadosdecuenta.php" );
    }
    break;

    case "movimientos" :
    {
        include( "eztransaccion/user/movimientos.php" );
    }
    break;

    case "busquedacheques" :
    {
        include( "eztransaccion/user/busquedacheques.php" );
    }
    break;

    case "divisas" :
    {
        include( "eztransaccion/user/divisas.php" );
    }
    break;

    case "tasas" :
    {
        include( "eztransaccion/user/tasas.php" );
    }
    break;

    case "pagotarjetas" :
    {
        include( "eztransaccion/user/pagotarjetas.php" );
    }
    break;

    case "pagointerbancario" :
    {
        include( "eztransaccion/user/pagointerbancario.php" );
    }
    break;

    case "transferencia" :
    {
        include( "eztransaccion/user/transferencia.php" );
    }
    break;

    case "speua" :
    {
        include( "eztransaccion/user/speua.php" );
    }
    break;

//-- SPEI EVG-EDS ENERO 2005 I
    case "speuaTT" :
    {
        include( "eztransaccion/user/speuaTT.php" );
    }
    break;

    case "speuaTV" :
    {
        include( "eztransaccion/user/speuaTV.php" );
    }
    break;

    case "speuaTTV" :
    {
        include( "eztransaccion/user/speuaTTV.php" );
    }
    break;

    case "speuaTB" :
    {
        include( "eztransaccion/user/speuaTB.php" );
    }
    break;


//-- SPEI EVG-EDS ENERO 2005 F



    case "inversion" :
    {
        include( "eztransaccion/user/inversion.php" );
    }
    break;

    case "tarjeta" :
    {
        include( "eztransaccion/user/tarjeta.php" );
    }
    break;

    case "clave" :
    {
        include( "eztransaccion/user/clave.php" );
    }
    break;
//-- EVG-EDS Bajionet CNBV 01042005
    case "claveOper" :
    {
        include( "eztransaccion/user/claveOper.php" );
    }
    break;
//-- EVG-EDS Bajionet CNBV 01042005
    case "chequera" :
    {
        include( "eztransaccion/user/chequera.php" );
    }
    break;

	// *************************************************************************
	// DGM I 07Ago2006 Liberacion de Cheques
	case "liberacioncheques" :
    {
        include( "eztransaccion/user/liberacioncheques.php" );
    }
    break;
	// *************************************************************************

	// *************************************************************************
	// DGM I 26Feb2007 Aportaciones Voluntarias por BajioNet
	//REGA se comenta línea para no mostrar menú
	/*case "aportacionAforeAB" :
    {
        include( "eztransaccion/user/aportacionAforeAB.php" );
    }
    break;*/
	//REGA se comenta bloque para no mostrar menú
	case "ReintegroAforeAB" :
    {
        include( "eztransaccion/user/ReintegroAforeAB.php" );
    }
    break;
	// *************************************************************************
	//REGA se comenta línea para no mostrar menú
  	/*case "aportacionAforeProg" :
    {
        include( "eztransaccion/user/aportacionAforeProg.php" );
    }
    break;*/
	//REGA se comenta bloque para no mostrar menú
    case "robo" :
    {
        include( "eztransaccion/user/robo.php" );
    }
    break;

    case "pagoservicios" :
    {
        include( "eztransaccion/user/pagoservicios.php" );
    }
    break;

	//ACS -Inicio Pago de servicios nuevo
	case "pagodeserviciosnuevo" :
    {
        include( "eztransaccion/user/pagodeserviciosnuevo.php" );
    }
    break;

	//REF WMA-30dic2008, Inicio
	
	case "comprobantesmultipago":
	{
		include( "eztransaccion/user/comprobantesmultipago.php");
	}
	break;

	case "multipago" :
	{
		include( "eztransaccion/user/multipago.php" );
	}
	break;

	case "reportemultipago" :
	{
		include( "eztransaccion/user/reportemultipago.php" );
	}
	break;
	//ACS Fin Pago de servicios nuevo


	//aluna 22jun2009 SIPARE
	case "sipare" :
    {
        include( "eztransaccion/user/sipare.php" );
    }
    break;

	//aluna 22jun2009 SIPARE

    case "pagosprovisionales" :
    {
        include( "eztransaccion/user/pagosprovisionales.php" );
    }
    break;

	case "pagosanuales" :
    {
        include( "eztransaccion/user/pagosanuales.php" );
    }
    break;

    case "pagoimpuestos" :
    {
        include( "eztransaccion/user/pagoimpuestos.php" );
    }
    break;

	// *************************************************************************
	// DGM I 07Dic2005 Pago Referenciado
	case "pagoreferenciado" :
    {
        include( "eztransaccion/user/pagoreferenciado.php" );
    }
    break;
	// *************************************************************************
    //DBA Aut2
    case "editotrctas" :
    {
        include( "eztransaccion/user/editotrctas.php" );
    }
    break;
    //DBA Aut2
	case "pagoscoordinados" :
    {
        include( "eztransaccion/user/pagoscoordinados.php" );
    }
    break;
    case "PCFimpuestos" :
    {
        include( "eztransaccion/user/PCFimpuestos.php" );
    }
    break;
    case "PDAimpuestos" :
    {
        include( "eztransaccion/user/PDAimpuestos.php" );
    }
    break;
    case "impuestos" :
    {
        include( "eztransaccion/user/impuestos.php" );
    }
    break;

    case "autorizacion" :
    {
        include( "eztransaccion/user/autorizacion.php" );
    }
    case "autorizacionsua" :
	    {
	        include( "eztransaccion/user/autorizacionsua.php" );
    }
    break;

	//ATAR se agrega Autorizacion De Pagos SIPARE 11nov2013
	case "autorizacionsipare" :
	    {
	        include( "eztransaccion/user/autorizacionsipare.php" );
    }
    break;
	//ATAR se agrega Autorizacion De Pagos SIPARE 11nov2013
    case "concentracion" :
    {
        include( "eztransaccion/user/concentracion.php" );
    }
    break;

    case "dispersion" :
    {
        include( "eztransaccion/user/dispersion.php" );
    }
    break;
	//ACDP INI Junio 2014 Fondeo Enlace Financiero
	case "fondeo" :
    {
        include( "eztransaccion/user/fondeo.php" );
    }
    break;
	//ACDP INI Junio 2014 Fondeo Enlace Financiero

    case "ligas" :
    {
        include( "eztransaccion/user/ligas.php" );
    }
    break;

    case "verificacion" :
    {
        include( "eztransaccion/user/verificacion.php" );
    }
    break;

    case "catalogocuentas" :
    {
        include( "eztransaccion/user/catalogocuentas.php" );
    }
    break;

	/* DOMINGO A LA ORDEN */
	
	case "catalogocuentasDAO" :
    {
        include( "eztransaccion/user/catalogocuentasDAO.php" );
    }
    break;
	
	case "pagosprogramadosDAO" :
    {
        include( "eztransaccion/user/pagosprogramadosDAO.php" );
    }
    break;
	
	case "autorizacionPPDAO" :
    {
        include( "eztransaccion/user/autorizacionPPDAO.php" );
    }
    break;
	
	case "ligasDAO" :
    {
        //include( "eztransaccion/user/ligasDAO.php" );
		include( "eztransaccion/user/ligasnuevo_ajaxDAO.php" );
    }
    break;
	
	// ***************************************************************
	// DGM F 05Mar2007
	case "catalogocuentasotr" :
    {
        include( "eztransaccion/user/catalogocuentasotr.php" );
    }
    break;
	// DGM F 05Mar2007
	// ***************************************************************


    case "pagosprogramados" :
    {
        include( "eztransaccion/user/pagosprogramados.php" );
    }
    break;

    case "autorizacionPP" :
    {
        include( "eztransaccion/user/autorizacionPP.php" );
    }
    break;

    //Modificado por Nexions - HM

    case "proveedores" :
    {
        include( "eztransaccion/user/proveedores.php" );
    }
    break;

    case "cuentas" :
    {
        include( "eztransaccion/user/cuentasproveedores.php" );
    }
    break;

    case "capturapagos" :
    {
        include( "eztransaccion/user/capturapagos.php" );
    }
    break;

    case "autorizacionpagos" :
    {
        include( "eztransaccion/user/autorizacionpagos.php" );
    }
    break;


    case "autorizacionpagosprog" :
    {
        include( "eztransaccion/user/autorizacionpagosprog.php" );
    }
    break;

    case "pagosporaplicar" :
    {
        include( "eztransaccion/user/pagosporaplicar.php" );
    }
    break;

    case "consultasreportespagos" :
    {
        include( "eztransaccion/user/consultasreportespagos.php" );
        //include( "eztransaccion/user/include/comprobantes.php" );
    }
    break;

    //

    //case "saldosTC" :	//ALUNA Regreso a un solo php para TDC
	    //$TSysNew = $ini->read_var( "site", "TAccessNew" );
			  //  if ( $TSysNew == "si" )
			    //{
			      //  include( "eztransaccion/user/saldosTCNew.php" );
			    //}
			    //else
			    //{
			      //  include( "eztransaccion/user/saldosTC.php" );
	    //}
    //break;

//FAF Cambio Menu Nuevo
    case "saldosTC" :	//ALUNA Regreso a un solo php para TDC
    {
        include( "eztransaccion/user/saldosTC.php" );
    }
    break;


    case "edosctaActualTC" :
    {
        include( "eztransaccion/user/edosctaActualTC.php" );
    }
	break;

	case "edoscta1AtrasTC" :
    {
        include( "eztransaccion/user/edoscta1AtrasTC.php" );
    }
	break;

    case "edoscta2AtrasTC" :
    {
        include( "eztransaccion/user/edoscta2AtrasTC.php" );
    }
	break;
    case "movtosTC" :
    {
        include( "eztransaccion/user/movtosTC.php" );
    }
    break;
	case "salptosTC" :
    {
        include( "eztransaccion/user/salptosTC.php" );
    }
    break;

	case "repextravioTC" :
    {
        include( "eztransaccion/user/repextravioTC.php" );
    }
    break;

	case "reproboTC" :
    {
        include( "eztransaccion/user/reproboTC.php" );
    }
    break;

//FAF Cambio Menu Nuevo

    case "reporteTC" :
    {
        include( "eztransaccion/user/reporteTC.php" );
    }
    break;

	// *************************************************************************
	// DGM I 07Ago2006 Entidad receptora del IMSS
	case "pagoimssSUA" :
    {
		include( "eztransaccion/user/pagoimssSUA.php" );
    }
    break;
	// *************************************************************************

    case "ArchServ" :
    {
        include( "eztransaccion/user/ArchServ.php" );
    }
    break;

    case "DomiServ" :
    {
        include( "eztransaccion/user/DomiServ.php" );
    }
    break;

    case "myfileupload" :
    {
        $Action = "Insert";
        include( "eztransaccion/user/myfileupload.php" );
    }
    break;
	case "upload" ://Altas Nomina2
	    {
	        $Action = "Insert";
	        include( "eztransaccion/user/upload.php" );
	    }
    break;
    case "session" ://Altas Nomina2
	{
	       $Action = "Insert";
	       include( "eztransaccion/user/session.php" );
	}
    break;
    case "upload2" : //Dispersiones Nomina2
		{
			$Action = "Insert";
			include( "eztransaccion/user/upload2.php" );
		}
    break;

    case "myfileupload2" :
    {
        $Action = "Insert";
        include( "eztransaccion/user/myfileupload2.php" );
    }
    break;
    case "ConArchServ" :
    {
        include( "eztransaccion/user/ConArchServ.php" );
    }
    break;

	case "ConArchPmx" : //YEHO 08Mar2010 Consulta Archivos PEMEX-gas
    {
        include( "eztransaccion/user/ConArchPmx.php" );
    }
    break;


	case "ConDomiServ" :
    {
        include( "eztransaccion/user/ConDomiServ.php" );
    }
    break;
    case "ConArcPP" :
    {
        include( "eztransaccion/user/ConArcPP.php" );
    }
    break;

	case "ArcPP" :
    {
        include( "eztransaccion/user/ArcPP.php" );
    }
    break;

	case "LibPP" :
    {
        include( "eztransaccion/user/LibPP.php" );
    }
    break;

    case "DepArcNom" :
    {
        include( "eztransaccion/user/DepArcNom.php" );
    }
    break;
	
	//IRG04052011 - INICIO - Nómina IMSS
	case "DepArcNomImss" :
    {
        include( "eztransaccion/user/DepArcNomImss.php" );
    }
    break;
	//IRG04052011 - INICIO - Nómina IMSS
	
	case "EmpArcNom" :
    {
        include( "eztransaccion/user/EmpArcNom.php" );
    }
    break;

	case "LibNom" :
    {
        include( "eztransaccion/user/LibNom.php" );
    }
    break;

	case "ConNom" :
    {
        include( "eztransaccion/user/ConArchNomi.php" );
    }
    break;
    //DBA 153054 I
    case "ConNomLn" :
	    {
	        include( "eztransaccion/user/ConArchNomiLinea.php" );
	    }
    break;
    //DBA 153054 F

	case "AltEmpNom" :
    {
        include( "eztransaccion/user/AltEmpNom.php" );
    }
    break;

	case "ConEmpNom" :
    {
        include( "eztransaccion/user/ConEmpNom.php" );
    }
    break;

	case "ProEmpNom" :
    {
        include( "eztransaccion/user/ProEmpNom.php" );
    }
    break;

	case "TjdNom" :
    {
        include( "eztransaccion/user/TjdNom.php" );
    }
    break;

	case "CatEmpNom" :
    {
        include( "eztransaccion/user/CatEmpNom.php" );
    }
    break;

	case "AgpEmpNom" :
    {
        include( "eztransaccion/user/AgpEmpNom.php" );
    }
    break;

	case "NapEmpNom" :
    {
        include( "eztransaccion/user/NapEmpNom.php" );
    }
    break;

    case "PmxDocPag" :
    {
       include( "eztransaccion/user/PmxDocPag.php" );
    }
    break;

    case "PmxDocPPag" :
    {
       include( "eztransaccion/user/PmxDocPPag.php" );
    }
    break;

    case "PmxIntMor" :
    {
       include( "eztransaccion/user/PmxIntMor.php" );
    }
    break;
    //DBA Fichas por Pagar
    case "PmxFicPPag" :
	    {
	       include( "eztransaccion/user/PmxFicPPag.php" );
	    }
    break;
    //DBA Fichas por Pagar

    case "CatalogoReferencia":
    {
       include( "eztransaccion/user/CatalogoReferencia.php" );
    }
    break;

	case "PagoAutTarjeta":
    {
       include( "eztransaccion/user/PagoAutTarjeta.php" );
    }
    break;
    //**********PSM Sms para TDC dic 2011 Ini
    case "SMSTarjeta":
	    {
	       include( "eztransaccion/user/SMSTarjeta.php" );
	    }
    break;
    //**********PSM Sms para TDC dic 2011 Fin
     //**********MRG ect para TDC ENE2014 Ini
    case "ECTTarjeta":
	    {
	       include( "eztransaccion/user/ECTTarjeta.php" );
	    }
    break;
    //**********MRG ect para TDC ENE2014 Fin
    case "PromoTarjeta":
	    {
	       include( "eztransaccion/user/PromoTarjeta.php" );
	    }
    break;

	// **********************
	// SY-YEHO I 27Oct2008 Entero Recaudacion IDE de Instituciones No Auxiliares
	case "ide":
    {
       include( "eztransaccion/user/ide.php" );
    }
	break;
	// SY-YEHO F 27Oct2008 Entero Recaudacion IDE de Instituciones No Auxiliares
	// **********************

/*
    case "activacion":
    {
       include( "eztransaccion/user/activacion.php" );
    }
	break;
    case "traspaso":
    {
       include( "eztransaccion/user/traspaso.php" );
    }

    break;
JMRG 13/01/2010 ADIOS A LOS TRASPASOS ELECTRONICOS  */
    case "simulacred":
    {
       include( "eztransaccion/user/simulacred.php" );
    }
    break;

    case "simulacreo":
    {
       include( "eztransaccion/user/simulacreo.php" );
    }
    break;
    //DBA I Reimpresion de Comprobantes
    case "reimpresion":
	{
	   include( "eztransaccion/user/reimpresion.php" );
    }
      break;
    case "Imprecibo":
	{
	   include( "eztransaccion/user/Imprecibo.php" );
    }
      break;
    //DBA F Reimpresion de Comprobantes
    //DBA I Cuenta CLABE
    case "clabe":
		{
		   include( "eztransaccion/user/clabe.php" );
	    }
      break;
    //DBA f Cuenta CLABE
/*    case "registro":
    {
       include( "eztransaccion/user/registro.php" );
    }
	break;
JMRG 13/01/2010 ADIOS A LOS TRASPASOS ELECTRONICOS*/
	//ACS 22Jul2009 Privilegio para Captura pagos por archivo
	case "capturapagosarchivo" :
    {
        include( "eztransaccion/user/capturapagosarchivo.php" );
    }
    break;
	case "autorizacionpagosarchivo" :
    {
        include( "eztransaccion/user/autorizacionpagosarchivo.php" );
    }
    break;
	case "cancelacionpagosarchivo" :
    {
        include( "eztransaccion/user/cancelacionpagosarchivo.php" );
    }
    break;
	//ACS 22Jul2009 Privilegio para Captura pagos por archivo
// 18sep2009 ACS   	    Altas en Línea. Privilegio para Altas en Línea
	case "nomaltexp" :
    {
        include( "eztransaccion/user/nomaltexp.php" );
    }
    break;
// 18sep2009 ACS   	    Altas en Línea. Privilegio para Altas en Línea
	// DVC-SYEO 181275 I
    case "CompraVentaDiv":
		{
		   include( "eztransaccion/user/CompraVentaDiv.php" );
	    }
      break;
    // DVC-SYEO 181275 F
//NXN TarjetasPrepago 10Ago2010 I
	case "tarjprepab" :
	{
		include( "eztransaccion/user/tarjprepab.php" );
	}
	break;
	case "tarjprepab2" :
	{
		include( "eztransaccion/user/tarjprepab2.php" );
	}
	break;
	case "tarjprepdep" :
	{
		include( "eztransaccion/user/tarjprepdep.php" );
	}
	break;
//NXN TarjetasPrepago 10Ago2010 F
//04Oct2012 HHR  206104-Proyecto tarjetas prepago Ini
	case "tarjpenlib" :
	{
		include( "eztransaccion/user/tarjpenlib.php" );
	}
	break;
	case "altatarjhabiente" :
	{
		include( "eztransaccion/user/altatarjhabiente.php" );
	}
	break;
	case "devrectarjeta" :
	{
		include( "eztransaccion/user/devrectarjeta.php" );
	}
	break;
//04Oct2012 HHR  206104-Proyecto tarjetas prepago Fin
//SYEVG 27Feb2012 212241-Solicitud envio Estado de cuenta digital
	case "ecdsolenvio":
	{
		include( "eztransaccion/user/ecdsolenvio.php" );
	}
	break;
	case "ecdcambioclave":
	{
		include( "eztransaccion/user/ecdcambioclave.php" );
	}
	break;
	case "ecdcambioemail":
	{
		include( "eztransaccion/user/ecdcambioemail.php" );
	}
	break;	
//SYEVG 27Feb2012 212241-Solicitud envio Estado de cuenta digital
    case "error" :
    {
        $errorID = $url_array[3];
        include( "eztransaccion/user/error.php" );
    }
    break;
	//OVVC NOVEDADES CAMBIOS 05082013
	
	case "novedades" :
    {
        include( "eztransaccion/user/novedades.php" );
    }
    break;
	
	//OVVC NOVEDADES CAMBIOS 05082013
	default:
	{
		eZHTTPTool::header( "Location: https://" . $ini->read_var( "site", "SecureServer" ) . $ini->read_var( "site", "ServerNumber" ) . $ini->read_var( "site", "DomainPostfix" ) . "/userbb/login/logout/" );
		exit();
	}
	break;	
	
}
?>
