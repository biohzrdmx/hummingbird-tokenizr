<?php
	namespace Tokenizr\Tests;

	use PHPUnit\Framework\TestCase;
	use Tokenizr\Tokenizr;

	class TokenizrTest extends TestCase {

		protected $salt = 'r/]+xfUp23FQ}qhj]0flh_O2_Nt0ax:CvU>KMiD6-F9KMu?8g0Pg.eZQ4?4PTHKQ';
		protected $simple = 'foo.0ce136e3544373922f1a380e6f4e252227428b2e4b64f69c0ec6fdf699efb02c';
		protected $complex = 'd=foo&p=read&e=1617313700.25573f1cbaa9a7498f2bfa446e6af934200a1d9e505b8b267c924a5b6d5a6bd3';
		protected $simple_tampered = 'bar.0ce136e3544373922f1a380e6f4e252227428b2e4b64f69c0ec6fdf699efb02c';
		protected $complex_tampered = 'd=foo&p=write&e=1617398300.d3d7b4f0d0b68a7f43d5fa2bed6018eec0f4df6000957853b32181b8eafadd37';

		public function testCreate() {
			$tokenizr = new Tokenizr($this->salt);
			# Test with simple content
			$data = 'foo';
			$token = $tokenizr->create($data);
			$this->assertEquals($this->simple, $token);
			# Test with complex content
			$data = [
				'd' => 'foo',
				'p' => 'read',
				'e' => 1617313700
			];
			$token = $tokenizr->create($data);
			$this->assertEquals($this->complex, $token);
		}

		public function testCheck() {
			$tokenizr = new Tokenizr($this->salt);
			# Check a valid token
			$result = $tokenizr->check($this->simple);
			$this->assertTrue($result);
			# Check an invalid token
			$result = $tokenizr->check($this->simple_tampered);
			$this->assertFalse($result);
			# Check an invalid complex token
			$result = $tokenizr->check($this->complex_tampered);
			$this->assertFalse($result);
			# Check with null
			$result = $tokenizr->check(null);
			$this->assertFalse($result);
			# Check with empty
			$result = $tokenizr->check('');
			$this->assertFalse($result);
		}

		public function testGetData() {
			$tokenizr = new Tokenizr($this->salt);
			$token = $tokenizr->create('foo');
			# Check a valid token
			$result = $tokenizr->getData($this->simple);
			$this->assertEquals('foo', $result);
			# Check a valid complex token
			$result = $tokenizr->getData($this->complex);
			$this->assertIsArray($result);
			# Check an invalid token
			$result = $tokenizr->getData($this->simple_tampered);
			$this->assertFalse($result);
			# Check an invalid complex token
			$result = $tokenizr->getData($this->complex_tampered);
			$this->assertFalse($result);
		}
	}

?>