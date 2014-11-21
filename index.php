<?php

// Configuration variable starts here

// Mobile Number/Msisdn

$vservMsisdn = "";

// Response format either xhtml, wml

$vservMarkup = "xhtml";

// Configuration variable ends here

if (!function_exists("vservADRequest")){

   function vservADRequest($vservZoneId,$vservTestMode)

   {

       global $vservContext,$vservMsisdn,$vservMarkup,$vservParams;

       if (!$vservParams) {

           $vservParams = array();

           $vservParams[] = "vr=".rawurlencode("1.1.0-phpcurl-20100726");

           $vservParams[] = "tm=".rawurlencode($vservTestMode);

           $vservParams[] = "ml=".rawurlencode($vservMarkup);

           $vservParams[] = "si=".rawurlencode(session_id());

           $vservParams[] = "mo=".rawurlencode($vservMsisdn);            

           $vservParams[] = "ip=".rawurlencode($_SERVER["REMOTE_ADDR"]);

           if(isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && !empty($_SERVER["HTTP_X_FORWARDED_FOR"]))

               $vservParams[] = "ff=".rawurlencode($_SERVER["HTTP_X_FORWARDED_FOR"]);

               

           if(isset($_SERVER["HTTP_VIA"]) && !empty($_SERVER["HTTP_VIA"]))    

               $vservParams[] = "hv=".rawurlencode($_SERVER["HTTP_VIA"]);

               

           $vservParams[] = "ht=".rawurlencode($_SERVER["HTTP_HOST"]);

           $vservParams[] = "ru=".rawurlencode($_SERVER["REQUEST_URI"]);

           $vservParams[] = "ua=".rawurlencode($_SERVER["HTTP_USER_AGENT"]);

           

           if(isset($_SERVER["HTTP_X_OPERAMINI_PHONE_UA"]) && !empty($_SERVER["HTTP_X_OPERAMINI_PHONE_UA"]))

               $vservParams[] = "ou=".rawurlencode($_SERVER["HTTP_X_OPERAMINI_PHONE_UA"]);

               

           if(isset($_SERVER["HTTP_X_WAP_PROFILE"]) && !empty($_SERVER["HTTP_X_WAP_PROFILE"]))    

               $vservParams[] = "up=".rawurlencode($_SERVER["HTTP_X_WAP_PROFILE"]);

           $vservNotToLog=array("HTTP_PRAGMA","HTTP_CACHE_CONTROL","HTTP_CONNECTION","HTTP_KEEP_ALIVE");

           foreach($_SERVER as $vservKey=>$vservVal){

               if (substr($vservKey,0,4) == "HTTP" && isset($vservVal) && !in_array($vservKey,$vservNotToLog)) {

                   $vservParams[] = "hd[".$vservKey."]=".rawurlencode($vservVal);

               }

           }            

       }

       $vservParams[] = "zoneid=".rawurlencode($vservZoneId);

$vservPostParams = implode("&",$vservParams);

       $vservHost = "rq.vserv.mobi";

       $vservDURL = "/delivery/adapi.php?$vservZoneId";

       $vservCINIT = curl_init();

       curl_setopt($vservCINIT, CURLOPT_URL, "http://".$vservHost.$vservDURL);

       curl_setopt($vservCINIT, CURLOPT_HTTPHEADER, array($vservContext));

       curl_setopt($vservCINIT, CURLOPT_FOLLOWLOCATION, 1); //allow redirection

       curl_setopt($vservCINIT, CURLOPT_FORBID_REUSE, 1); //force close conn

       curl_setopt($vservCINIT, CURLOPT_HEADER, 1);

       curl_setopt($vservCINIT, CURLOPT_POST, 1);        

       curl_setopt($vservCINIT, CURLOPT_POSTFIELDS, $vservPostParams);

       curl_setopt($vservCINIT, CURLOPT_RETURNTRANSFER, true);

       curl_setopt($vservCINIT, CURLOPT_CONNECTTIMEOUT, 5);

       curl_setopt($vservCINIT, CURLOPT_TIMEOUT, 7);

       $vservADRequest = curl_exec($vservCINIT);

       $vservinfo = curl_getinfo($vservCINIT);

       curl_close($vservCINIT);

       $vservReturnAd = false;

       if($vservinfo["http_code"]==200){

           $vservADPage=explode("\r\n",$vservADRequest);        

           $vservResponseStatus = false;

           $vservFoundAd=false;

           for($vservLoopCnt=0;$vservLoopCnt<count($vservADPage);$vservLoopCnt++){

               if(preg_match("/200 OK/",$vservADPage[$vservLoopCnt])){

                   $vservResponseStatus = true;

               }

               if(stripos($vservADPage[$vservLoopCnt],"-VSERV-CONTEXT")){

                   $vservContext=$vservADPage[$vservLoopCnt];

               }

               if($vservADPage[$vservLoopCnt] == "" && $vservResponseStatus){

                   $vservFoundAd = $vservLoopCnt;

                   break;

               }

           }

           if($vservFoundAd !== false){

               $vservReturnAd = implode("\r\n",array_slice($vservADPage,$vservFoundAd));

           }

       }

       return $vservReturnAd;

   }

}

// In Case of Second request For the ad In same page. just Call the below Function with proper zoneid.

//Set vservTestMode to true for testing

$vservTestMode = false;

$vservZoneId = "896eada3";

echo vservADRequest($vservZoneId,$vservTestMode);
?>
