<form class="form-horizontal" id="form" method="post" action="">
	<div class="form-group">
		<div class="col-md-12">
			<label>Клиент</label>
			<input type="text" name="client" class="form-control" required value="<?php echo value($this->item, 'client'); ?>" />
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-12">
			<label>Град</label>
			<select name="cityId" class="form-control" required><?php echo build_options(db()->fetchPairs('SELECT id, name FROM cities'), value($this->item, 'cityId', 0), 'Изберете'); ?></select>
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-12">
			<label>Email</label>
			<input type="text" name="email" class="form-control" required value="<?php echo value($this->item, 'email'); ?>" />
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-12">
			<label>Телефон</label>
			<input type="text" name="phone" class="form-control" required value="<?php echo value($this->item, 'phone'); ?>" />
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-12">
			<label>Тема</label>
			<input type="text" name="theme" class="form-control" value="<?php echo value($this->item, 'theme'); ?>" />
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-12">
			<label>Съобщение</label>
			<textarea rows="5" name="content" class="form-control"><?php echo value($this->item, 'content'); ?></textarea>
		</div>
	</div>
	<input type="submit" data-id="<?php echo value($this->item, 'id', 0); ?>" name="btnSave" class="btn btn-primary" value="<?php echo ($this->item ? 'Редактиране' : 'Добавяне'); ?>" />
</form>