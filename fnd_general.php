<?php
/********************************************************************************************************************************
AUTHOR: Omar Villalobos
 EMAIL: omar_villalobos@outlook.com
*********************************************************************************************************************************/
function fnd_print_option($type, $data){
	$option_value = "";
	
	switch($type){
		case "normal":
			$max = sizeof($data);
			for( $i=0 ; $i<$max ; $i++ ){
				$option_value .= '<option value="'.$data[$i].'" >'.$data[$i].'</option>';
			}
		break;
		case "double":
			$max = sizeof($data);
			for( $i=0 ; $i<$max ; $i++ ){
				$data_explode 	= explode(";", $data[$i]);
				$data_value		= $data_explode[0];
				$data_text		= $data_explode[1];
				
				$option_value .= '<option value="'.$data_value.'" >'.$data_text.'</option>';
			}
		break;
		case "double_value":
			$max = sizeof($data);
			for( $i=0 ; $i<$max ; $i++ ){
				$data_explode 	= explode(";", $data[$i]);
				$data_value		= $data_explode[0];
				$data_text		= $data_explode[1];
				
				$option_value .= '<option value="'.$data_value.'" >'.$data_value.' - '.$data_text.'</option>';
			}
		break;
	}
	
	echo $option_value;
}

function fnd_get_traking( $str_value ){
	$str_company_name = "";
	
	$len_company_name = strlen( strtoupper( trim( str_replace( ' ', '', $str_value ) ) ) );
	
	if( $len_company_name < 5 ){
		$lenEncry_1 		= 5;	
		$characters 		= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength 	= strlen($characters);
		
		for($i = 0; $i < $lenEncry_1; $i++) { $str_company_name .= $characters[rand(0, $charactersLength - 1)]; }
	} else {
		$str_company_name = substr( strtoupper( trim( str_replace( ' ', '', $str_value ) ) ), 0, 5 );
	}

	$str_company_name = $str_company_name.date("mdYHis")."US";
	
	return $str_company_name;
}

function fnd_get_time( $mode ){ /* 0/+5/+10/... */
	date_default_timezone_set('America/Los_Angeles');
	
	$return_time = "";

	if( $mode == "0" ){
		$return_time = date( 'H:i:s' );
	} else {
		$return_time = date( 'H:i:s', strtotime( $mode.' minute', strtotime( date( 'H:i:s' ) ) ) );
	}

	return $return_time;
}

/****************************************************************************
 * USED TO REVERSE ALL THE MESSAGE (STRING) WITHOUT ARRAY FUNCTIONS
 ****************************************************************************/
$message = 'cake pound steal';
$message = reverseMSG( $message );

function reversetMSG(){
	function reverseMSG( $message ){
		$new_msg = "";
		$size_msg = strlen($message);
		$arr_msg = array();
		$aux_msg = "";
		$j = 0;
		
		for( $i=0 ; $i<$size_msg ; $i++ ){
			if( $message[$i] == " " ){
				$arr_msg[$j] = $aux_msg;
				$j++;
				$aux_msg = "";
			} else {
				$aux_msg .= $message[$i];
			}
		}
		$arr_msg[$j] = $aux_msg;
		$max = sizeof($arr_msg)-1;
		$aux_msg = "";

		for( $i=$max ; $i>=0 ; $i-- ){
			$aux_msg .= $arr_msg[$i] . " ";
		}

		return $aux_msg;
	}
}

print( $message );
?>