<?php
class ClassFritzBox {
	protected $fb_ip   = '';
	protected $fb_user = '';
	protected $fb_pass = '';
	protected $connected = null;
	protected $sid = null;

	public function __construct($fb_ip = null, $fb_pass = null, $fb_user = null) {
		if( $fb_ip === null || $fb_pass == null ) return false;
		if( $fb_user === null ) $this->fb_user = "fritz-api";
		$this->fb_ip = $fb_ip;
		$this->fb_pass = $fb_pass;
	}

	function doLogin() {
		// Challenge bei der Fritz.box holen
		$ch = curl_init('http://'.$this->fb_ip.'/login_sid.lua');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$login = curl_exec($ch);
		$session_status_simplexml = @simplexml_load_string($login);

		if ($session_status_simplexml->SID != '0000000000000000') {
			$this->sid = $session_status_simplexml->SID;
		}
		else {
			if ($session_status_simplexml->BlockTime > 0) {
				echo "\n\n:: BlockTime";
				$this->sid = null;
				return false;
			}

			$challenge = $session_status_simplexml->Challenge;
			$response = $challenge.'-'.md5(mb_convert_encoding($challenge.'-'.$this->fb_pass, 'UCS-2LE', 'UTF-8'));

			$post_data = array('response' => $response,
								'lp' => '',
								'loginView' => 'user',
								'username' => 'martin');
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data)); // "response=".$response."&page=/login_sid.lua");
			$sendlogin = curl_exec($ch);
			$session_status_simplexml = simplexml_load_string($sendlogin);

			if ($session_status_simplexml->SID != '0000000000000000') {
				$this->sid = $session_status_simplexml->SID;
			}
			else {
				echo "\n\n:: LoginFailed";
				$this->sid = null;
				return false;
			}
		}

		// echo "<hr>\r\n";
		// print_r($session_status_simplexml);
		// echo "SID: ",$this->sid;
		// echo "<hr>\r\n";

		curl_close($ch);

		return true;
	}

	public function getEnergyValues() {
		$url = 'https://'.$this->fb_ip.'/net/home_auto_query.lua?sid='.$this->sid.'&no_sidrenew=1&command=EnergyStats_10&id=16&useajax=1&xhr=1&t'.time().'=nocache';

		$header = array('Accept: */*',
						'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:97.0) Gecko/20100101 Firefox/97.0',
						'Content-Type: application/json',
						'X-Requested-With: XMLHttpRequest',
						'Connection: keep-alive',
						'Pragma: no-cache',
						'Cache-Control: no-cache');

		$options = array(
			'http' => array(
				'timeout' => 5,
				'method'  => 'GET',
				'header'  => $header
			),
			'ssl' => array(
				'verify_peer'      => false,
				'verify_peer_name' => false
			)
		);

		$response = file_get_contents($url, false, stream_context_create($options));

		$json = json_decode($response, true);

		// print_r($json);

		$ret['current_power'] = round($json['MM_Value_Power'] / 100);
		$ret['today_power'] = round($json['sum_Day'] / 100);

		return $ret;
	}
}