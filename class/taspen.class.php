<?php

class getNoTaspen //original class by @makrifLabs
{
	public $cookie;
	public $submit_url;
	public $ch;

	public function __construct()
	{
		$this->cookie = 'tmp/cookie.txt';
	}

	public function doNoTaspen($npwp15, $tahun)
	{

		//$hostnya = 'https://services.taspen.co.id/';
		$headers = array(
			"Connection: keep-alive",
			"User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:43.0) Gecko/20100101 Firefox/43.0",
			"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
		);
		$UrlTaspen = "https://services.taspen.co.id/e-spt/espt_pajak_pensiun.php";
		$this->ch 	= curl_init();
		$ch = $this->ch;
		curl_setopt($ch, CURLOPT_URL, $UrlTaspen);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($ch, CURLOPT_REFERER, $UrlTaspen);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'rd=2&textnotas=' .$npwp15. '&tahun='. $tahun .'&submit=');
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_exec($ch);
		$data = curl_exec($ch);

		return $data;
	}

	public function do_spt_taspen($link)
	{
		//$mfnpwp = substr($npwp, 0, 9);
		$this->cookie_file = "cookie";
		$this->submit_url  = $link;
		$ch = $this->ch;
		

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER, $this->submit_url);
		curl_setopt($ch, CURLOPT_URL, $this->submit_url);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:43.0) Gecko/20100101 Firefox/43.0");
		//curl_setopt($ch, CURLOPT_POST, true);
		//curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
		$data = curl_exec($ch);

		return $data;
	}

}
