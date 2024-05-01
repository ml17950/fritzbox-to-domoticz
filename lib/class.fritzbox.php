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
}