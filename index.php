<?php

function __autoload($class) {
	include 'classes/' . $class . '.php';
}

$pp = new Preprocessor();

foreach ($pp->getData() as $value) {
	echo $value->winner, '>', $value->loser, '<br />';
}

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
	</head>
	<body>
		
	</body>
</html>