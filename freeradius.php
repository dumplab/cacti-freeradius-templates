<?php
/* freeradius.php
 * enables cacti to read FreeRADIUS statistics
 * by dumplab
 *
 * usage:
 * freeradius.php stat_type host port shared_secret
 * stat_type:
 * auth, acct, proxy-auth
 * Example: freeradius.php auth 10.10.10.23 18121 mysecret
*/

// please verify ... make sure radclient is installed and cacti has rights to execute
$radclient = "/bin/radclient";

if ($_SERVER["argc"] == 5)
{
        // gather args
        $host   = $_SERVER["argv"][2]; $port   = $_SERVER["argv"][3]; $secret = $_SERVER["argv"][4];
        // check radclient
        if (file_exists($radclient)==false) { die("Error: File ".$radclient." does not exists. Cannot get statistics. Check freeradius.php and radclient path\n"); }
        $output = "";
        switch($_SERVER["argv"][1])
        {
                case "auth":
                        // execute radclient
                        $raw = shell_exec("echo -e \"Message-Authenticator = 0x00\nFreeRADIUS-Statistics-Type=Authentication\" | ".$radclient." -x ".$host.":".$port." status ".$secret);
                        $raw = explode("\n", $raw);
                        // when using the original Attribute as ds, cacti doesn't import the values, so we do some nasty rewrite
                        foreach($raw as $line)
                        {
                                if (preg_match("/FreeRADIUS-Total-Access-Requests/i",$line)) { $avp = explode("=", $line); if (sizeof($avp)>1) { $output .= "acreq:".trim($avp[1])." "; } }
                                if (preg_match("/FreeRADIUS-Total-Access-Accepts/i",$line)) { $avp = explode("=", $line); if (sizeof($avp)>1) { $output .= "acacc:".trim($avp[1])." "; } }
                                if (preg_match("/FreeRADIUS-Total-Access-Rejects/i",$line)) { $avp = explode("=", $line); if (sizeof($avp)>1) { $output .= "acrej:".trim($avp[1])." "; } }
                                if (preg_match("/FreeRADIUS-Total-Access-Challenges/i",$line)) { $avp = explode("=", $line); if (sizeof($avp)>1) { $output .= "accha:".trim($avp[1])." "; } }
                                if (preg_match("/FreeRADIUS-Total-Auth-Responses/i",$line)) { $avp = explode("=", $line); if (sizeof($avp)>1) { $output .= "aures:".trim($avp[1])." "; } }
                                if (preg_match("/FreeRADIUS-Total-Auth-Duplicate-Requests/i",$line)) { $avp = explode("=", $line); if (sizeof($avp)>1) { $output .= "audup:".trim($avp[1])." "; } }
                                if (preg_match("/FreeRADIUS-Total-Auth-Malformed-Requests/i",$line)) { $avp = explode("=", $line); if (sizeof($avp)>1) { $output .= "aumal:".trim($avp[1])." "; } }
                                if (preg_match("/FreeRADIUS-Total-Auth-Invalid-Requests/i",$line)) { $avp = explode("=", $line); if (sizeof($avp)>1) { $output .= "auinf:".trim($avp[1])." "; } }
                                if (preg_match("/FreeRADIUS-Total-Auth-Dropped-Requests/i",$line)) { $avp = explode("=", $line); if (sizeof($avp)>1) { $output .= "audro:".trim($avp[1])." "; } }
                                if (preg_match("/FreeRADIUS-Total-Auth-Unknown-Types/i",$line)) { $avp = explode("=", $line); if (sizeof($avp)>1) { $output .= "auunk:".trim($avp[1])." "; } }
                        }
                        $output = rtrim($output);
                        break;

                case "acct":
                        $raw = shell_exec("echo -e \"Message-Authenticator = 0x00\nFreeRADIUS-Statistics-Type=Accounting\" | ".$radclient." -x ".$host.":".$port." status ".$secret);
                        $raw = explode("\n", $raw);
                        foreach($raw as $line)
                        {
                                if (preg_match("/FreeRADIUS-Total-Accounting-Requests/i",$line)) { $avp = explode("=", $line); if (sizeof($avp)>1) { $output .= "acreq:".trim($avp[1])." "; } }
                                if (preg_match("/FreeRADIUS-Total-Accounting-Responses/i",$line)) { $avp = explode("=", $line); if (sizeof($avp)>1) { $output .= "acres:".trim($avp[1])." "; } }
                                if (preg_match("/FreeRADIUS-Total-Acct-Duplicate-Requests/i",$line)) { $avp = explode("=", $line); if (sizeof($avp)>1) { $output .= "acdup:".trim($avp[1])." "; } }
                                if (preg_match("/FreeRADIUS-Total-Acct-Malformed-Requests/i",$line)) { $avp = explode("=", $line); if (sizeof($avp)>1) { $output .= "acmal:".trim($avp[1])." "; } }
                                if (preg_match("/FreeRADIUS-Total-Acct-Invalid-Requests/i",$line)) { $avp = explode("=", $line); if (sizeof($avp)>1) { $output .= "acinv:".trim($avp[1])." "; } }
                                if (preg_match("/FreeRADIUS-Total-Acct-Dropped-Requests/i",$line)) { $avp = explode("=", $line); if (sizeof($avp)>1) { $output .= "acdro:".trim($avp[1])." "; } }
                                if (preg_match("/FreeRADIUS-Total-Acct-Unknown-Types/i",$line)) { $avp = explode("=", $line); if (sizeof($avp)>1) { $output .= "acunk:".trim($avp[1])." "; } }
                        }
                        $output = rtrim($output);
                        break;

                case "proxyauth":
                        // execute radclient
                        $raw = shell_exec("echo -e \"Message-Authenticator = 0x00\nFreeRADIUS-Statistics-Type=4\" | ".$radclient." -x ".$host.":".$port." status ".$secret);
                        $raw = explode("\n", $raw);
                        foreach($raw as $line)
                        {
                                if (preg_match("/FreeRADIUS-Total-Proxy-Access-Requests/i",$line)) { $avp = explode("=", $line); if (sizeof($avp)>1) { $output .= "pacreq:".trim($avp[1])." "; } }
                                if (preg_match("/FreeRADIUS-Total-Proxy-Access-Accepts/i",$line)) { $avp = explode("=", $line); if (sizeof($avp)>1) { $output .= "pacacc:".trim($avp[1])." "; } }
                                if (preg_match("/FreeRADIUS-Total-Proxy-Access-Rejects/i",$line)) { $avp = explode("=", $line); if (sizeof($avp)>1) { $output .= "pacrej:".trim($avp[1])." "; } }
                                if (preg_match("/FreeRADIUS-Total-Proxy-Access-Challenges/i",$line)) { $avp = explode("=", $line); if (sizeof($avp)>1) { $output .= "paccha:".trim($avp[1])." "; } }
                                if (preg_match("/FreeRADIUS-Total-Proxy-Auth-Responses/i",$line)) { $avp = explode("=", $line); if (sizeof($avp)>1) { $output .= "paures:".trim($avp[1])." "; } }
                                if (preg_match("/FreeRADIUS-Total-Proxy-Auth-Duplicate-Requests/i",$line)) { $avp = explode("=", $line); if (sizeof($avp)>1) { $output .= "paudup:".trim($avp[1])." "; } }
                                if (preg_match("/FreeRADIUS-Total-Proxy-Auth-Malformed-Requests/i",$line)) { $avp = explode("=", $line); if (sizeof($avp)>1) { $output .= "paumal:".trim($avp[1])." "; } }
                                if (preg_match("/FreeRADIUS-Total-Proxy-Auth-Invalid-Requests/i",$line)) { $avp = explode("=", $line); if (sizeof($avp)>1) { $output .= "pauinf:".trim($avp[1])." "; } }
                                if (preg_match("/FreeRADIUS-Total-Proxy-Auth-Dropped-Requests/i",$line)) { $avp = explode("=", $line); if (sizeof($avp)>1) { $output .= "paudro:".trim($avp[1])." "; } }
                                if (preg_match("/FreeRADIUS-Total-Proxy-Auth-Unknown-Types/i",$line)) { $avp = explode("=", $line); if (sizeof($avp)>1) { $output .= "pauunk:".trim($avp[1])." "; } }
                        }
                        $output = rtrim($output);

                        break;

                default:
                        die("Error: undefinded parameter given.\nUse one of these: auth, acct, proxy-auth\n");
        }
        echo $output;

} else {
        die("Error: wrong parameter count\nUsage: freeradius.php stat_type host port shared_secret\n");
}
?>