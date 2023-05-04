<?php

use Class\App\App;
use Class\Get\Get;
use Class\Upload\Upload;

require_once("./App/Class/autoload.php");


App::Route("/api/get/test", [Get::class, "Test"]);




// UPLOAD

App::Route("/api/upload/orderprepare", [Upload::class, "OrderPrepare"]);
App::Route("/api/upload/order", [Upload::class, "Order"]);
