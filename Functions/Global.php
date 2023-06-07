<?php

function http_get($url, $params = NULL)
{
    if (function_exists("curl_init")) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $exec = curl_exec($curl);
    }

    return $exec;
}
