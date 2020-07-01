<?php

namespace Aznoqmous;

class Req {

  public $config;
  // public $url;
  // public $method;
  // public $verbose;

  public function __construct($config=[])
  {
    $this->config = array_merge([
      'url' => '',
      'method' => 'POST',
      'verbose' => false,
      'proxy' => false,
      'proxy_port' => false,
      'proxy_type' => 'HTTP'
    ], $config);
    foreach($this->config as $key => $value) $this->{$key} = $value;

    $this->method = strtoupper($this->method);
    $this->proxy_type = strtoupper($this->proxy_type);

    if($this->proxy && preg_match("/\:/", $this->proxy)) {
        $this->proxy = explode(':', $this->proxy)[0];
        $this->proxy_port = explode(':', $this->proxy)[1];
    }

  }

  public function do($params=null, $cookies=[])
  {
    $ch = curl_init();

    if(is_array($params)){
      if($this->method == 'POST') {
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
      }
      else {
        curl_setopt($ch, CURLOPT_URL, $this->url . $this->getQueryString($params));
      }
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, TRUE);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_VERBOSE, $this->verbose);
    curl_setopt($ch, CURLOPT_COOKIE, implode(";", $cookies));

    if($this->proxy) {
        curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
        curl_setopt($ch, CURLOPT_PROXYTYPE, $this->proxy_type);
    }
    if($this->proxy_port) curl_setopt($ch, CURLOPT_PROXYPORT, $this->proxy_port);

    global $responseCookies;
    curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($ch, $headerLine){
        global $responseCookies;
        if (preg_match('/^Set-Cookie:\s*([^;]*)/mi', $headerLine, $responseCookie) == 1) {
            $responseCookies[] = $responseCookie[1];
        }
        return strlen($headerLine);
    });


    $data = curl_exec($ch);

    $infos = curl_getinfo($ch);
    $time = intval($infos['total_time'] * 1000);

    $res = [
      'time' => $time,
      'info' => $infos,
      'data' => $data,
      'cookies' => $responseCookies
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
