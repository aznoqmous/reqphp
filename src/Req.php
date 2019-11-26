<?php

namespace Aznoqmous;

class Req {

  public $url;

  public function __construct($config)
  {
    $this->config = array_merge([
      'url' => '',
      'method' => 'POST'
    ], $config);
    foreach($this->config as $key => $value) $this->{$key} = $value;
    $this->method = strtoupper($this->method);
  }

  public function do($params)
  {
    $curl = curl_init();
    
    if(is_array($params)){
      if($this->method == 'POST') {
        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
      }
      else {
        curl_setopt($curl, CURLOPT_URL, $this->url . $this->getQueryString($params));
        dump($this->getQueryString($params));
      }
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);

    $data = curl_exec($curl);

    $infos = curl_getinfo($curl);
    $time = intval($infos['total_time'] * 1000);

    $res = [
      'time' => $time,
      'info' => $infos,
      'data' => $data
    ];

    if($res) return $res;
    else return false;
  }

  public function getQueryString($params)
  {
    $strParams = [];
    foreach($params as $key => $value){
      $strParams []= urlencode($key) . '=' . urlencode($value);
    }
    return "?" . implode('&', $strParams);
  }

}
