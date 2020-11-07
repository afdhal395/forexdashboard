<?php

/*

Documentations
https://client-api.instaforex.com/Home/GetClientsAPI
https://client-api.instaforex.com/Home/GetAPIUsageInfo

*/

class Api
{
  public $username;
  public $login;
  public $password;
  public $token;

  public function setUsername($username)
  {
    $this->username = $username; 
  }

  public function setLogin($login)
  {
    $this->login = $login;
  }

  public function setPassword($password)
  {
    $this->password = $password;
  }

  public function setToken($token)
  {
    $this->token = $token;
  }

  public function requestToken()
  {
    $Login = $this->login;
    $apiPassword = $this->password;
    $data = array("Login" => $Login, "Password" => $apiPassword);
    $data_string = json_encode($data);

    $ch = curl_init('https://client-api.instaforex.com/api/Authentication/RequestClientApiToken');

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data_string)));
    $token = curl_exec($ch);
    curl_close($ch);

    $this->token = $token;
  }

  public function getBalanceStatus()
  {
    $apiMethodUrl = 'client/RequestBalanceInformation/'; #possibly Must be Changed
    $parameters = $this->login; #possibly Must be Changed. Depends on the method param
    $ch = curl_init('https://client-api.instaforex.com/'.$apiMethodUrl.$parameters);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); # Turn it ON to get result to the variable
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('passkey: '.$this->token));
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
  }

  public function getOpenTrades()
  {
    $apiMethodUrl = 'client/RequestOpenTrades/'; #possibly Must be Changed
    $parameters = $this->login; #possibly Must be Changed. Depends on the method param
    $ch = curl_init('https://client-api.instaforex.com/'.$apiMethodUrl.$parameters);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); # Turn it ON to get result to the variable
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('passkey: '.$this->token));
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
  }
}
?>