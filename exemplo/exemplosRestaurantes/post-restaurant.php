<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://localhost/v1',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array('name' => 'Goomer','image' => 'image/img.jpg','address' => 'Rua Sorocaba, 000, Sorocaba-SP','is_active' => '1','is_accepted' => '1','is_schedulable' => '1','schedule_data' => '{"wednesday":[{"open" :"21:20","close" :"23:48"}]}'),
));

$response = curl_exec($curl);
$err     = curl_errno($curl);
$errmsg  = curl_error($curl) ;

curl_close($curl);

if ($err) {
  echo $errmsg; 
} else {
  echo $response;
}