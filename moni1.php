<?php
'http://qc5qc.com/xqc/mlpxyy/stuyy_tj.php';
$loginUrl	= 'http://qc5qc.com/xqc/mlpxyy/mCheck.php';
$loginData	= array(
	'userSsn'	=> 'XXXXXXXXXXXXXXXXXXXXXXXX',
	'province'	=> '01',
	'city'	=> '01HJG070',
	'token'	=> 'd9b0cd90d24cf621f93d5d9a6e3b2b1f',
);


$cookie	= "PHPSESSID=iio2qakk8d4sc27jdmcceorbl7; xqc2014_session=a%3A5%3A%7Bs%3A10%3A%22session_id%22%3Bs%3A32%3A%2279e4cade2db4cbf50e8f60931ede3ba0%22%3Bs%3A10%3A%22ip_address%22%3Bs%3A13%3A%22124.160.92.82%22%3Bs%3A10%3A%22user_agent%22%3Bs%3A109%3A%22Mozilla%2F5.0+%28Windows+NT+6.2%3B+WOW64%29+AppleWebKit%2F537.36+%28KHTML%2C+like+Gecko%29+Chrome%2F40.0.2214.115+Safari%2F537.36%22%3Bs%3A13%3A%22last_activity%22%3Bi%3A1425955021%3Bs%3A9%3A%22user_data%22%3Bs%3A0%3A%22%22%3B%7Dc44c8842d5fdb1365d49d1950e9bdf418b169a04; CNZZDATA1382947=cnzz_eid%3D683578151-1423305339-http%253A%252F%252Fxqc.qc5qc.com%252F%26ntime%3D1425951357";

$saveUrl	= "http://xqc.qc5qc.com/reservation/training_save";
$saveCh	= curl_init($saveUrl);
curl_setopt($saveCh, CURLOPT_RETURNTRANSFER, true);
curl_setopt($saveCh, CURLOPT_POST, true);
curl_setopt($saveCh, CURLOPT_COOKIE, $cookie); 

$apptimeUrl	= "http://xqc.qc5qc.com/reservation/apptime";
$apptimeCh	= curl_init($apptimeUrl);
curl_setopt($apptimeCh, CURLOPT_RETURNTRANSFER, true);
curl_setopt($apptimeCh, CURLOPT_COOKIE, $cookie); 

$resultUrl	= 'http://xqc.qc5qc.com/reservation/result';
$resultCh	= curl_init($resultUrl);
curl_setopt($resultCh, CURLOPT_RETURNTRANSFER, true);
curl_setopt($resultCh, CURLOPT_POST, true);
curl_setopt($resultCh, CURLOPT_COOKIE, $cookie); 

$pxds	= array(
	'001', 
	'002', 
//	'005', 
//	'006', 
//	'008', 
//	'011'
);
$pxd	= '001';
$ssn	= 'XXXXXXXXXXXXXXXXXXXX';
$sjds	= array(1, 2, 3);
$token	= '5022f4baf33f1624c9505f90af0d9340';
$maxDate	= '2015-03-25';
$start		= microtime(true);
while (true) {
	foreach ($pxds as $pxd) {
		for ($i = 0; $i < 60; $i++) {
			$rq	= date('Y-m-d', strtotime('+' . ($i + 1) . ' days'));
			$diff	= microtime(true) - $start;
			if ($diff < 1) {
			//	sleep(1);
			}
			$start	= microtime(true);
			if ($rq > $maxDate || ($rq >= '2015-02-18' && $rq <= '2015-02-24')) {
				continue;
			}
			echo "curl start: " . date('H:i:s') . "\n";
			curl_setopt($saveCh, CURLOPT_POSTFIELDS, "rq={$rq}&pxd={$pxd}");
			curl_exec($saveCh);
			if (curl_errno($saveCh)) {
				echo curl_error($saveCh);
				exit;
			}

			$content	= curl_exec($apptimeCh);
			if (strpos($content, '选择培训时间段：') !== false) {
				if ($rq <= $maxDate) {
					$lines	= explode("\n", $content);
					$sjd	= null;
					foreach ($lines as $line) {
						if (strpos($line, '<input name="sjd" type="radio"') !== false && strpos($line, 'disabled') === false) {
							$value	= preg_match('/value="(.*?)"/', $line, $m);
							if (isset($m[1])) {
								$sjd	= $m[1];	
							}
						}

						/*
						if (preg_match('/\<input type=\"hidden\" name\=\"token\".*?value=\"(.*?)\"/', $line, $m)) {
							$token	= $m[1];
						}
						 */
					}

					echo $content . "\n";
					if ($sjd === null) {
						continue;
					}
					/*
					echo '-----------------------------------------------------' . "\n";
					echo $content . "\n";
					echo '-----------------------------------------------------' . "\n";
					$ch2	= curl_init('http://qc5qc.com/xqc/mlpxyy/stuyy_tj.php');
					curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch2, CURLOPT_POST, true);
					curl_setopt($ch2, CURLOPT_POSTFIELDS, "xm={$xm}&lkcid=&ssn={$ssn}&xcjxbh=HJG070&jd=%BD%D7%B6%CE%D2%BB&yyrq={$rq}&pxd={$pxd}&sjd={$sjd}&token={$token}");
					curl_setopt($ch2, CURLOPT_COOKIE, $cookie);
					 */
					
					curl_setopt($resultCh, CURLOPT_POSTFIELDS, "sjd={$sjd}");
					$content	= curl_exec($resultCh);
					echo $content . "\n";
					if (strpos($content, '页面正在自动转向') === false) {
						exit;
					}
				}
				echo $pxd . "|" . $rq . "\n\n";
			} else {
				preg_match('/\<div\s+class="content guery"[^\>].*\>(.*?)\<\/div\>/', $content, $m);
				echo "we can't baoming {$pxd} | {$rq} {$m[1]}\n\n";
			}
		}
	}
}


