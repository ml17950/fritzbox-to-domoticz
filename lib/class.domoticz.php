<?php
class ClassDomoticz {
	protected $hostaddr   = '';

	public function __construct($hostaddr = null) {
		if ($hostaddr === null) return false;
		$this->hostaddr = $hostaddr;
	}

	public function sendRequest($url) {
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

		return json_decode($response, true);
	}

	public function setValue($id, $nvalue = 0, $svalue = '') {
		$url = 'http://'.$this->hostaddr.'/json.htm?type=command&param=udevice&idx='.$id.'&nvalue='.$nvalue.'&svalue='.$svalue;

		$response = $this->sendRequest($url);

		print_r($response);

		if ($response['status'] != 'OK')
			return false;

		return true;
	}
}