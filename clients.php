<?php
include __DIR__ . '/config.php';
include __DIR__ . '/src/database.php';
include __DIR__ . '/src/functions.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Клиенти</title>
</head>
<body>
	<table style="width: 100%" border="1" cellspacing="0" cellpadding="5">
		<tr>
			<th>Клиент</th>
			<th>Град</th>
			<th>Адрес</th>
			<th>Email</th>
			<th>Телефон</th>
			<th>Описание</th>
			<th>Бележки</th>
		</tr>
	<?php
	$cities = db()->fetchPairs('SELECT id, CONCAT(name, ", ", code) FROM cities');
	foreach(db()->fetchAll('select * from clients order by id desc') as $client) :
	?>
		<tr>
			<td><?php echo nl2br($client['client']); ?><div style="padding-top: 5px; color: gray;"><?php echo Pointers::$data[$client['icon']]; ?></div></td>
			<td><?php echo isset($cities[$client['cityId']]) ? $cities[$client['cityId']] : ''; ?></td>
			<td><?php echo nl2br($client['address']); ?></td>
			<td><?php echo $client['email']; ?></td>
			<td><?php echo $client['phone']; ?></td>
			<td><?php echo $client['theme']; ?></td>
			<td><?php echo nl2br($client['content']); ?></td>
		</tr>
	<?php endforeach; ?>
	</table>
</body>
</html>