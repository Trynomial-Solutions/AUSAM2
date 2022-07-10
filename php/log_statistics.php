<?php
// Log statistics of AUSAM2 use
if (($ip=get_ip_address())!=false) $geolocate=get_geolocate($ip);

require_once("connecti.inc.php");
$sql="INSERT INTO ausam2_stats (pmid_count, pmid_errors, lic_count, lic_errors, ip, zip, org) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt=$dbi->prepare($sql) or die ($dbi->error);
$stmt->bind_param("iiiisss", 
                  $_POST['pmid_count'],
                  $_POST['pmid_errors'],
                  $_POST['lic_count'],
                  $_POST['lic_errors'],
                  $ip,
                  $geolocate['zip'],
                  $geolocate['org']
                 );
$stmt->execute();
$stmt->close();

function get_ip_address(){
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
        if (array_key_exists($key, $_SERVER) === true){
            foreach (explode(',', $_SERVER[$key]) as $ip){
                $ip = trim($ip); // just to be safe

                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                    return $ip;
                }
            }
        }
    }
}

function get_geolocate($ip) {
    $url="http://ip-api.com/json/".$ip;
    $json=json_decode(file_get_contents($url));
    $rVal=array('zip' => $json->zip, 'org' => $json->org);
//    print_r($json);
    return $rVal;
}
?>