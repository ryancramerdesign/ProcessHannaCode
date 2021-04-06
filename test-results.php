<?php namespace ProcessWire;

/** @var TextformatterHannaCode $textformatter */
/** @var string $tag */
/** @var Config $config */

if(!defined("PROCESSWIRE")) die();
$config->debug = true;
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
$timer = Debug::timer();

?><!DOCTYPE html>
<html
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>Hanna Code test results</title>
		<style type='text/css'>
			body {
				background: #fff;
				color: #555;
				font-family: Arial, sans-serif;
			}
		</style>
	</head>
	<body style='margin: 0; padding: 0'>
		<p style='margin-top:0'><pre><?php echo htmlentities($tag); ?></pre></p>
		<div style='border: 1px dashed #ccc; padding: 10px;'>
			<?php echo $textformatter->render($tag); ?>
		</div>
		<p><small><?php echo sprintf($this->_('Executed in %s seconds'), Debug::timer($timer)); ?></small></p>
	</body>
</html>
