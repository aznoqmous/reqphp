<?php

require './vendor/autoload.php';

use Aznoqmous\Req;

$req = new Req([
  'url' => "{$_SERVER['SERVER_NAME']}/dump.php"
]);
$res = $req->do([
  'foo' => 'bar'
]);
dump($res);
