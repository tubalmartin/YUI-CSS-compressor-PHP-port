<?php

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

header('Content-Type: text/plain;charset=utf-8');

/**
 * pTest - PHP Unit Tester
 * @param mixed $test Condition to test, evaluated as boolean
 * @param string $message Descriptive message to output upon test
 * @url http://www.sitepoint.com/blogs/2007/08/13/ptest-php-unit-tester-in-9-lines-of-code/
 */
function assertTrue($test, $message)
{
	static $count;
	if (!isset($count)) $count = array('pass'=>0, 'fail'=>0, 'total'=>0);

	$mode = $test ? 'pass' : 'fail';
	$outMode = $test ? 'PASS' : '!FAIL';
	printf("%s: %s (%d of %d tests run so far have %sed)\n",
		$outMode, $message, ++$count[$mode], ++$count['total'], $mode);

	return (bool)$test;
}

/**
 * Get number of bytes in a string regardless of mbstring.func_overload
 *
 * @param string $str
 * @return int
 */
function countBytes($str)
{
    return (function_exists('mb_strlen') && ((int)ini_get('mbstring.func_overload') & 2))
        ? mb_strlen($str, '8bit')
        : strlen($str);
}

function run_tests()
{
    $yui_tests = glob(dirname(__FILE__) . '/yui/*.css');
	$my_tests = glob(dirname(__FILE__) . '/mine/*.css');

	$files = array_merge($yui_tests, $my_tests);

    // some tests may exhaust memory/stack due to string size/PCRE
    $skip = array(
        //'dataurl-base64-doublequotes.css',
        //'dataurl-base64-noquotes.css',
        //'dataurl-base64-singlequotes.css',
    );

    $cssmin = new CSSmin();

    foreach ($files as $file) {
        if (! empty($skip) && in_array(basename($file), $skip)) {
            echo "INFO: CSSmin: skipping " . basename($file) . "\n";
            continue;
        }

        $src = file_get_contents($file);
        $minExpected = trim(file_get_contents($file . '.min'));
        $minOutput = $cssmin->run($src);
        
        $passed = assertTrue($minExpected == $minOutput, 'CSSmin : ' . basename($file));
        if (! $passed && __FILE__ === realpath($_SERVER['SCRIPT_FILENAME'])) {
            echo "\n---Output: " .countBytes($minOutput). " bytes\n\n{$minOutput}\n\n";
            echo "---Expected: " .countBytes($minExpected). " bytes\n\n{$minExpected}\n\n";
            echo "---Source: " .countBytes($src). " bytes\n\n{$src}\n\n\n";
        }
    }
}


require_once '../cssmin.php';

run_tests();
