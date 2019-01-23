<?php
    $curfiledir = getcwd();
    date_default_timezone_set('Europe/Brussels');
    $timestamp=time();
    $myDate= date("d.m.Y - H:i", $timestamp);
        
    // log to file
    $myFile = "targetreg.txt";
    $fh = fopen($myFile, 'a') or die("can't open file");
    $data_back = file_get_contents('php://input');
    $json_back_clean2 = preg_replace('/[\x00-\x1F\x80-\xFF]/','',$data_back);
    $json_back_clean = preg_replace('/^jsonData=/','',$json_back_clean2);

    $json_format=json_encode($json_back_clean, JSON_PRETTY_PRINT);

    //fwrite($fh, "\nDate: " .$myDate." ******* JSONresponse: ".$json_back_clean."\n");
    //fwrite($fh, "\nDate: " .$myDate." ******* JSONresponse: ".$json_format."\n");

    // parse JSON object
    try {
        
        $json_data  = json_decode($json_back_clean,true);
        
        fwrite($fh, "\nDate: " .$myDate." ******* \033[31m".$json_data['Fun']."\033[0m\n");
        fwrite($fh, "\t\t".$json_back_clean."\n");

        // manage tv discovery
        if ($json_data['Fun'] == 'TVDiscoveryService') {


            fwrite($fh, "This is if statement \n");

            $pollfreq   = $json_data['CommandDetails']['WebServiceParameters']['PollingFrequency'];
            $uniqueid   = $json_data['CommandDetails']['WebServiceParameters']['TVUniqueID'];
            $serialnr   = $json_data['CommandDetails']['TVDiscoveryParameters']['TVSerialNumber'];
            $modelnr    = $json_data['CommandDetails']['TVDiscoveryParameters']['TVModelNumber'];
            $roomid     = $json_data['CommandDetails']['TVDiscoveryParameters']['TVRoomID'];
            $macaddress = $json_data['CommandDetails']['TVDiscoveryParameters']['TVMACAddress'];
            $ipaddress  = $json_data['CommandDetails']['TVDiscoveryParameters']['TVIPAddress'];
            $vsecureid  = $json_data['CommandDetails']['TVDiscoveryParameters']['VSecureTVID'];

            $html = new DOMDocument(); 
            $html->loadHTMLFile('template.html'); 
            $html->getElementById('ip')->nodeValue = "IP address is: $ipaddress";
            $html->saveHTMLFile("index.html");

            fwrite($fh, "This is the ip address of the tv " .$ipaddress. " \n");         

            if ($json_data['CommandDetails']['TVDiscoveryParameters']['PowerStatus'] == "ON") {
                $pwstatus = "On";
            }
            else {
                $pwstatus = "StandBy";
            }	
	}
        // end TV discovery
    }
    catch (Exception $e) {
        fwrite($fh, $e->getMessage()."\n");
    }

    fclose($fh);

