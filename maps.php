<?php
include __DIR__ . '/config.php';
include __DIR__ . '/src/database.php';
include __DIR__ . '/src/functions.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: text/html; charset=utf8");
$cities = db()->fetchPairs('SELECT id, CONCAT(name, ", ", code, ", България") FROM cities WHERE lat IS NULL AND fetched = 0 LIMIT 100');
echo '<pre>';
$affected = 0;
foreach($cities as $id => $name) {
	$result = getLatLng($name);
	echo $name . ' :: ' . json_encode($result) . '<br />';
	db()->update('cities', array('fetched' => 1), array('id' => $id));
	if (isset($result['error_message'])) {
		echo $name . ' :: ' . $result['error_message'] . '<br />';
		continue;
	}
	if (empty($result['results'])) {
		continue;
	}
	$loc = $result['results'][0]['geometry']['location'];
	$affected += db()->update('cities', array('lat' => $loc['lat'], 'lng' => $loc['lng']), array('id' => $id));
}
echo $affected;