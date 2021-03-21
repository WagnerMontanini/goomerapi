<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://localhost/v1/restaurants/1/products',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array('category_id' =>1,'name' => 'Produto Goomer','price' => '20.5','image' => 'image/img.jpg','description' => 'Teste de criação de produtos','old_price' => '0.00'),
));

$response = curl_exec($curl);
$err     = curl_errno($curl);
$errmsg  = curl_error($curl) ;

curl_close($curl);

echo (!empty($err)) ? $errmsg : $response;
