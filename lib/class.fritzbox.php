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
}