<?php
'http://qc5qc.com/xqc/mlpxyy/stuyy_tj.php';
$loginUrl	= 'http://qc5qc.com/xqc/mlpxyy/mCheck.php';
$loginData	= array(
	'userSsn'	=> 'XXXXXXXXXXXXXXXXXXXXXXXX',
	'province'	=> '01',
	'city'	=> '01HJG070',
	'token'	=> 'd9b0cd90d24cf621f93d5d9a6e3b2b1f',
);


$cookie	= "PHPSESSID=iio2qakk8d4sc27jdmcceorbl7; xqc2014_session=a%3A5%3A%7Bs%3A10%3A%22session_id%22%3Bs%3A32%3A%221eddb9726a76ef69ab219bef53ce76f5%22%3Bs%3A10%3A%22ip_address%22%3Bs%3A13%3A%22124.160.92.82%22%3Bs%3A10%3A%22user_agent%22%3Bs%3A109%3A%22Mozilla%2F5.0+%28Windows+NT+6.2%3B+WOW64%29+AppleWebKit%2F537.36+%28KHTML%2C+like+Gecko%29+Chrome%2F41.0.2272.101+Safari%2F537.36%22%3Bs%3A13%3A%22last_activity%22%3Bi%3A1427627163%3Bs%3A9%3A%22user_data%22%3Bs%3A0%3A%22%22%3B%7D9c4881334b647abdc8b4a392902a68e53f629f8a; CNZZDATA1382947=cnzz_eid%3D683578151-1423305339-http%253A%252F%252Fxqc.qc5qc.com%252F%26ntime%3D1427622187";

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
$today	= date('Y-m-d');
$pxd	= '001';
$ssn	= 'XXXXXXXXXXXXXXXXXXXX';
$token	= '5022f4baf33f1624c9505f90af0d9340';
$maxDate	= '2015-04-25';
$wantDates	= array('2015-04-04', '2015-04-05', '2015-04-06', '2015-04-18', '2015-04-19');
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
			if (!checkIsDateOK($rq)) {
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
				preg_match_all('/<ul\s+class="shake_list">((.*\s+)+?)<\/ul>/', $content, $bmxxMatches);
				$lisStr	= $bmxxMatches[1][0];	
				$liArr	= explode("\n", $lisStr);
				$curYuyueRq	= '';
				foreach ($liArr as $li) {
					if (strpos($li, '预约日期') !== false) {
						preg_match('/<b>(.*)<\/b>/', $li, $curYuyueRqMatches);
						$curYuyueRq	= $curYuyueRqMatches[1];
						break;
					}
				}
				echo '预约日期：' . $curYuyueRq . "\n";
				if (!checkIsDateOK($rq)) {
					continue;
				}

				if ($rq <= $maxDate && $rq > $today) {
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

function checkIsDateOK($date)
{
	global $maxDate, $wantDates, $today;
	if (strlen($date) != 10) {
		throw new Exception('error date ' . $date);
	}
	if ($date <= $maxDate && $date > $today) {
		if ($wantDates) {
			if (in_array($date, $wantDates)) {
				return true;
			} else {
				return false;
			}
		}
		return true;
	}
	return false;
}
