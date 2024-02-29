<?php
	header('Access-Control-Allow-Origin: *');


	$day = "";

	if ( isset( $_GET[ "dia" ] ) )
	{
		$day = $_GET[ 'dia' ];
	}

	$dt = DateTime::createFromFormat("Y-m-d", $day);
	
	// This api no longer shows updated results since 2021, so I'll just show the data from the same day but from 2016 //
	if ( $dt !== false && is_array($dt->getLastErrors()) && !array_sum($dt->getLastErrors()) )
	{
		$day = date("2016-m-d");
	}
	else
	{
		$day = "2016-" . $dt->format('m') . "-" . $dt->format('d');
	}

	$file = $day.".json";


	if ( file_exists( $file ) )
	{
		echo( file_get_contents( $file ) );
	}
	else
	{
		$curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		$token = "xxx";
		$indicator = "1013"; // Indicador del PVPC (tarifa general)

		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => 'https://api.esios.ree.es/indicators/'.$indicator.'?start_date='.$day.'T00:00:00&end_date='.$day.'T23:50:00',
			CURLOPT_HTTPHEADER => array(
				'Accept: application/json; application/vnd.esios-api-v1+json',
				'Content-Type: application/json',
				'Host: api.esios.ree.es',
				'x-api-key:'.$token.'',
				'Cookie: ')
		));

		$resp = curl_exec($curl);
		curl_close($curl);
		
		echo $resp;

		if ( str_contains($resp, "indicator") )
		{
			file_put_contents( $file, $resp );
		}
	}
	
?>
