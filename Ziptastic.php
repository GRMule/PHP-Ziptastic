<?php
class Ziptastic implements JsonSerializable {
	const API_URL = 'http://zip.elevenbasetwo.com/?zip=';
	public $city = null;
	public $state = null;
	public $zip = null;
	public $error = false;
	public function __construct ($zip=false) {
		if ($zip)
			$this->lookup($zip);
	}
	private function returnError ($message) {
		$this->error = $message;
		return $this;
	}
	public function lookup ($zip) {
		$this->city = null;
		$this->state = null;
		$this->zip = null;
		$this->error = false;

		$zip = trim(preg_replace('([^0-9-\s])', '-', $zip));
		$zip = preg_replace('([\s\+,])', '-', $zip);

		if (strlen($zip) < 5)
			return $this->returnError('Zip code too short');

		$parts = explode('-', $zip);
		if (count($parts) > 2)
			return $this->returnError('Malformed zip code');

		if (!is_numeric($parts[0]))
			return $this->returnError('Non-numeric zip code');

		$result = @file_get_contents(self::API_URL.$parts[0]);
		if (!$result)
			return $this->returnError('Invalid zip code');

		$result = @json_decode($result, true);
		if (!$result)
			return $this->returnError('Invalid zip code');

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
