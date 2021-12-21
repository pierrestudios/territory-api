<?php


/**
 * getAdminData
 * 
 * @param TestCase $test 
 * 
 * @return object
 */
function getAdminData($test)
{
	return getUserData([
		'email' => config('app.adminEmail'),
		'password' => config('app.adminPassword')
	], $test);
}

/**
 * getUserData
 * 
 * @param array $creds 
 * @param TestCase $test 
 * 
 * @return object
 */
function getUserData($creds = [], $test)
{
	return empty($creds) ? null : $test->json('POST', '/v1/signin', $creds);
}

/**
 * logResult
 * 
 * @param string $endpoint 
 * @param array  $result 
 * 
 * @return object
 */
function logResult($endpoint, $result = [])
{
	$blue = "\033[36m";
	$bold = "\033[1m";
	$normal = "\033[0m";
	$grey = "\033[37m";

	// Remove styles for browser run (argv set to: --colors=never)
	foreach ($_SERVER['argv'] as $arg) {
		if (strpos($arg, '--colors') !== false) {
			$colorsFlag_arr = explode('=', $arg);
			if (end($colorsFlag_arr) === 'never') {
				$blue = $bold = $normal = $grey = "";
			}
		}
	}

	$log = "\n" . $blue . date('Y-m-d h:i:s') . 
		' Successfully Tested Api Endpoint: ' . $bold . $endpoint . $normal;
	$log .= "\n" . $grey . 'Result: ' . json_encode($result) . $normal . "\n";

	fwrite(STDOUT, $log . "\n");
}