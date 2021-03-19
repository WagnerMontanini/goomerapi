<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://localhost/v1/1',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'PUT',
  CURLOPT_POSTFIELDS => 'name=Goomer&address=Rua%20Sorocaba%2C%20000%2C%20Sorocaba%2CS%C3%A3o%20Paulo&image=imagem%2Fimg.jpg&is_active=1&is_accepted=0&is_schedulable=1&schedule_data=%7B%22wednesday%22%3A%5B%7B%22open%22%20%3A%2219%3A00%22%2C%22close%22%20%3A%2223%3A59%22%7D%5D%7D',
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
