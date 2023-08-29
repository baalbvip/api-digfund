<?php

use Class\App\App;
use Class\Get\Get;
use Class\Upload\Upload;

require_once("./App/Class/autoload.php");



// GET 
App::Route("/api/get/test", [Get::class, "Test"]);
App::Route("/api/get/token", [Get::class, "Token"]);
App::Route("/api/get/myinfo", [Get::class, "MyInfo"]);
App::Route("/api/get/consolidated", [Get::class, "Consolidated"]);
App::Route("/api/get/detailorder", [Get::class, "DetailOrder"]);
App::Route("/api/get/statusaccount", [Get::class, "StatusAccount"]);



// UPLOAD

App::Route("/api/upload/orderprepare", [Upload::class, "OrderPrepare"]);
App::Route("/api/upload/order", [Upload::class, "Order"]);
App::Route("/api/upload/registeruser", [Upload::class, "RegisterNewUser"]);
App::Route("/api/upload/renewcontract", [Upload::class, "RenewContract"]);
