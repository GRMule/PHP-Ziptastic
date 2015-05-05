<?php
	namespace PHPZiptastic;
	class Ziptastic implements \JsonSerializable {
		const API_URL = 'http://zip.getziptastic.com/v2/US/';

		public $city = null;
		public $state = null;
		public $zip = null;
		public $error = false;

		private $full_return = null;
		private $exceptionOnError = false;
		private $endpointOverride = null;

		public function __construct ($zip=null, $exceptionOnError=false, $endpointOverride=null) {
			$this->exceptionOnError = $exceptionOnError;
			$this->endpointOverride = $endpointOverride;
			if (is_null($zip) === false)
				$this->lookup($zip);
		}
		public function lookup ($zip) {
			$this->reset();

			$zip = $this->parseAndValidate($zip);
			// self-return indicates error
			if (is_object($zip) === true)
				return $zip;

			$this->zip = $zip;

			$result = $this->doLookup();
			// self-return indicates error
			if (is_object($result) === true)
				return $result;

			$this->state = ucwords($result['state']);
			$this->city = ucwords(strtolower($result['city']));
			$this->full_return = $result;
			return $this;
		}
		public function __toString () {
			if (is_null($this->zip))
				return '';
			return $this->city.', '.strtoupper(substr($this->full_return['state_short'], 0, 2));
		}
		public function jsonSerialize() {
			return $this->full_return;
		}

		private function returnError ($message) {
			if ($this->exceptionOnError === true) {
				throw new \Exception($message);
			}
			$this->error = $message;
			return $this;
		}
		private function reset() {
			$this->city = null;
			$this->state = null;
			$this->zip = null;
			$this->error = false;
			$this->full_return = null;
		}
		private function parseAndValidate($zip) {
			// remove non-numeric, add hyphens for spaces
			$zip = trim(preg_replace('([^0-9-\s])', '-', $zip));
			$zip = preg_replace('([\s\+,])', '-', $zip);
			if (strlen($zip) < 5)
				return $this->returnError('Zip code too short');

			// chunk by hyphens -- getting rid of the +4
			$parts = explode('-', $zip);

			// if there are too many parts, we probably got a crazy string
			if (count($parts) > 2)
				return $this->returnError('Malformed zip code');

			// nothing we can do with non-numeric,
			// nb: though it isn't clear how it could be at this stage! -- pointless check?
			if (!is_numeric($parts[0]))
				return $this->returnError('Non-numeric zip code');
			return $parts[0];
		}
		private function doLookup () {
			$endpoint = is_null($this->endpointOverride) ? self::API_URL : $this->endpointOverride;
			$result = @file_get_contents($endpoint.$this->zip);
			if (strlen(trim($result)) < 1) {
				return $this->returnError('Unexpected endpoint response');
			}
			$result = @json_decode($result, true);
			if (is_array($result) === false) {
				return $this->returnError('Unexpected endpoint response');
			}
			if (
				array_key_exists('city', $result) === false
			) {
				return $this->returnError('Invalid zip code');
			}
			return $result;
		}
	}
?>
