<?php
// Log statistics of AUSAM2 use
$ip=get_ip_address();

require_once("connecti.inc.php");
$sql="INSERT INTO ausam2_stats (pmid_count, pmid_errors, lic_count, lic_errors, board_count, board_errors, ip) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt=$dbi->prepare($sql);
$stmt->bind_param("iiiiiis", 
                  $_POST['pmid_count'],
                  $_POST['pmid_errors'],
                  $_POST['lic_count'],
                  $_POST['lic_errors'],
                  $_POST['board_count'],
                  $_POST['board_errors'],
                  $ip);
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
?>