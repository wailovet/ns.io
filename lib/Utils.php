<?php
namespace Nsio;


use Exception;

class Utils
{

    public static function asyncHttp($domain, $url, $ip = "127.0.0.1", $port = 80, $timeout = 30)
    {
        $err_no = "";
        $err_str = "";
        $fp = fsockopen($ip, $port, $err_no, $err_str, $timeout);
        if (!$fp) {
            throw new Exception($err_str, $err_no);
        } else {
            $out = "GET {$url} HTTP/1.1\r\n";
            $out .= "Host: " . $domain . "\r\n";
            $out .= "Connection: Close\r\n\r\n";
            $out .= "Cache-Control:nocache\r\n\r\n";
            $out .= "Pragma:no-cache\r\n\r\n";
            $out .= "Expires:-1\r\n\r\n";
            fwrite($fp, $out);
            $content = "";
            while (!feof($fp)) {
                $content .= fgets($fp, 128);
            }
            fclose($fp);
        }
        return $content;
    }
}