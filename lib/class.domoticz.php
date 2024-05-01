<?php
class ClassDomoticz {
	protected $domo_ip   = '';

	public function __construct($domo_ip = null) {
		if ($domo_ip === null) return false;
		$this->domo_ip = $domo_ip;
	}
}