<?php foreach ($this->clients as $client) : ?>
<div class="table-responsive clientItem">
	<table class="table table-bordered">
		<tr><th style="width: 150px;">Клиент <img src="<?php echo $client['icon']; ?>"></th><td><?php echo $client['client']; ?></td></tr>
		<tr><th>Град</th><td><?php echo $client['city']; ?></td></tr>
		<tr><th>Email</th><td><?php echo $client['email']; ?></td></tr>
		<tr><th>Телефон</th><td><?php echo $client['phone']; ?></td></tr>
		<tr><th>Тема</th><td><?php echo $client['theme']; ?></td></tr>
		<tr><th>Съобщение</th><td><?php echo nl2br($client['content']); ?></td></tr>
	</table>
	<div class="text-right">
		<button data-id="<?php echo $client['id']; ?>" class="showClient btn btn-success">Покажи</button>
		<button data-id="<?php echo $client['id']; ?>" class="editShowClient btn btn-primary">Редактиране</button>
		<button data-id="<?php echo $client['id']; ?>" class="removeClient btn btn-danger">Изтриване</button>
	</div>
</div>
<?php endforeach; ?>