<?php
class Ziptastic implements JsonSerializable {
	private $url = 'http://zip.elevenbasetwo.com/?zip=';
	public $city = null;
	public $state = null;
	public $zip = null;
	public $error = false;
	private $error_code = false;
	public function __construct ($zip=false) {
		if ($zip)
			$this->lookup($zip);
	}
	private function reset() {
		$this->city = null;
		$this->state = null;
		$this->zip = null;
		$this->error = false;
		$this->error_code = false;
	}
	private function returnError ($message, $code) {
		$this->error = $message;
		$this->error_code = $code;		
		return $this;
	}
	public function lookup ($zip) {
		$this->reset();

		$zip = trim(preg_replace('([^0-9-\s])', '-', $zip));
		$zip = preg_replace('([\s\+,])', '-', $zip);

		if (strlen($zip) < 5)
			return $this->returnError('Zip code too short', 1);

		$parts = explode('-', $zip);
		if (count($parts) > 2)
			return $this->returnError('Malformed zip code', 2);

		if (!is_numeric($parts[0]))
			return $this->returnError('Non-numeric zip code', 3);

		$result = @file_get_contents($this->url.$parts[0]);
		if (!$result)
			return $this->returnError('Invalid zip code', 4);

		$result = @json_decode($result, true);
		if (!$result)
			return $this->returnError('Invalid zip code', 5);

		$this->zip = $parts[0];
		$this->state = strtoupper($result['state']); 
		$this->city = ucwords(strtolower($result['city'])); 
		return $this;
	}
	public function __toString () {
		if (is_null($this->zip))
			return '';
		return $this->city.', '.$this->state;
	}
	public function jsonSerialize() {
		return array(
			'city'=>$this->city,
			'state'=>$this->state,
			'zip'=>$this->zip
		);
	}
}
?>
