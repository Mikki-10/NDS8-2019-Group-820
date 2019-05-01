<?php
error_reporting(E_ALL);
ini_set("display_errors", true);

/*
$TOTP = new TOTP();

$secret = $TOTP->createSecret();
echo "<pre>"; var_dump($secret); echo "</pre>";

$code = $TOTP->get_totp_code($secret);
echo "<pre>"; var_dump($code); echo "</pre>";

echo "<pre>"; $TOTP->verify_totp($secret, $code); echo "</pre>";
//echo "<pre>"; var_dump($secret); echo "</pre>";

$label = "Username - Telenor";
$issuer = "Telenor.dk";
$secret = $TOTP->make_totp_url($secret, $label, $issuer);
echo "<pre>"; var_dump($secret); echo "</pre>";
*/

/**
 * 
 */
class TOTP
{
	/**
	 * TOTP example (modified)
	 *
	 * @filesource   totp.php
	 * @created      23.12.2017
	 * @author       Smiley <smiley@chillerlan.net>
	 * @Modified     Mikki
	 * @copyright    2017 Smiley
	 * @license      MIT
	 */

	Private $auth;

	function __construct($mode = "totp", $code_length = 6, $validation_period = 30, $algorithm = "sha1")
	{
		require_once __DIR__ . '/TOTP/Authenticator.php';
		require_once __DIR__ . '/TOTP/AuthenticatorException.php';
		require_once __DIR__ . '/TOTP/Base32.php';
		require_once __DIR__ . '/TOTP/Base32Exception.php';

		$this->auth = new \chillerlan\Authenticator\Authenticator;

		$this->auth
			// switch mode to TOTP (default)
			->setMode($mode)
			// change the code length
			->setDigits($code_length)
			// set validation period (seconds)
			->setPeriod($validation_period)
			// set the HMAC hash algo
			->setAlgorithm($algorithm)
		;
	}

	function createSecret()
	{
		return $this->auth->createSecret();
	}

	function get_totp_code($secret)
	{
		$this->auth
			// Authenticator::createSecret() stores the most recent created secret,
			// so you'll only need to call this when using existing secrets
			->setSecret($secret)
		;

		// get a one time code
		return $this->auth->code();
	}

	function verify_totp($secret, $code)
	{
		$this->auth
			// Authenticator::createSecret() stores the most recent created secret,
			// so you'll only need to call this when using existing secrets
			->setSecret($secret)
		;

		// verify the code
		/*
		var_dump($this->auth->verify($code)); // -> true
		var_dump($this->auth->verify($code, time() - $this->auth->getPeriod())); // -> true
		var_dump($this->auth->verify($code, time() + 2 * $this->auth->getPeriod())); // -> false
		var_dump($this->auth->verify($code, time() + 2 * $this->auth->getPeriod(), 2)); // -> true
		*/

		return $this->auth->verify($code);
	}

	function make_totp_url($secret, $label, $issuer)
	{
		$this->auth
			// Authenticator::createSecret() stores the most recent created secret,
			// so you'll only need to call this when using existing secrets
			->setSecret($secret)
		;

		return $this->auth->getUri($label, $issuer);
	}
}
