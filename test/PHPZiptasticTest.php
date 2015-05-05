<?php
	use PHPZiptastic\Ziptastic;
	require(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'Ziptastic.php');

	class PHPZiptasticTest extends \PHPUnit_Framework_TestCase {

		public function testMakeInstance() {
			$zt = new Ziptastic();
			$this->assertEquals($zt->city, null);
		}

		public function testLookUp () {
			$zt = new Ziptastic(49506);
			$this->assertEquals($zt->city, 'Grand Rapids');
		}

		public function testLookUpAfterConstruct () {
			$zt = new Ziptastic();
			$zt->lookup(49506);
			$this->assertEquals($zt->city, 'Grand Rapids');
		}

		public function testChainLookup () {
			$zt = new Ziptastic();
			$this->assertEquals($zt->lookup(49506)->city, 'Grand Rapids');
		}

		public function testToString() {
			$zt = new Ziptastic();
			$string = (string) $zt->lookup(49506);
			$this->assertEquals($string, 'Grand Rapids, MI');
		}

		public function testToJson() {
			$zt = new Ziptastic();
			$json = json_encode($zt->lookup(49506));
			$this->assertEquals(is_array(json_decode($json, true)), true);
		}

		public function testWithPlusFour() {
			$zt = new Ziptastic();
			$json = json_encode($zt->lookup('49506-1234'));
			$this->assertEquals($zt->city, 'Grand Rapids');
		}

		public function testMultiLookup () {
			$zt = new Ziptastic();
			$this->assertEquals($zt->lookup(49506)->city, 'Grand Rapids');
			$this->assertEquals($zt->lookup(49404)->city, 'Coopersville');
			$this->assertEquals($zt->lookup(49411)->state, 'Michigan');
		}

		public function testWithBadLookup() {
			$zt = new Ziptastic();
			$zt->lookup('cats make terrible pets');
			$this->assertEquals($zt->city, null);
			$this->assertEquals($zt->error, 'Malformed zip code');
		}

		/**
		 * @expectedException \Exception
		 * @expectedExceptionMessage Malformed zip code
		 */
		public function testExceptionWithBadLookup() {
			$zt = new Ziptastic('cats make terrible pets', true);
		}

		public function testWithEmptyLookup() {
			$zt = new Ziptastic();
			$zt->lookup('');
			$this->assertEquals($zt->city, null);
			$this->assertEquals($zt->error, 'Zip code too short');
		}

		/**
		 * @expectedException \Exception
		 * @expectedExceptionMessage Zip code too short
		 */
		public function testExceptionWithEmptyLookup() {
			$zt = new Ziptastic('', true);
		}

		public function testNotFoundLookup() {
			$zt = new Ziptastic();
			$zt->lookup(12344);
			$this->assertEquals($zt->city, null);
			$this->assertEquals($zt->error, 'Invalid zip code');
		}

		/**
		 * @expectedException \Exception
		 * @expectedExceptionMessage Invalid zip code
		 */
		public function testExceptionNotFoundLookup() {
			$zt = new Ziptastic(12344, true);
		}

		public function testBadEndpoint() {
			$zt = new Ziptastic(49506, false, 'http://www.google.com');
			$this->assertEquals($zt->error, 'Unexpected endpoint response');
		}

		/**
		 * @expectedException \Exception
		 * @expectedExceptionMessage Unexpected endpoint response
		 */
		public function testExceptionBadEndpoint() {
			$zt = new Ziptastic(49506, true, 'http://www.google.com');
		}
	}
?>