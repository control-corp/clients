<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Clients</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/main.css">
</head>
<body>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-3">
				<div class="panel panel-default">
					<div class="panel-heading">
                    	Добавяне
                    </div>
                    <div class="panel-body addForm">
						<?php
						$view = new View();
						$view->item = false;
						echo $view->render(__DIR__ . '/views/_add.php');
						?>
                    </div>
                </div>
			</div>
			<div class="col-md-6">
				<div class="panel panel-default">
					<div class="panel-heading">
                    	Карта
                    </div>
                    <div class="panel-body">
                    	<div id="map"></div>
                    </div>
                </div>
			</div>
			<div class="col-md-3">
				<div class="panel panel-default">
					<div class="panel-heading">
                    	<div class="input-group">
                            <input type="text" name="search" class="form-control" />
                            <span class="input-group-addon"><span class="glyphicon glyphicon-search"></span></span>
                        </div>
                    </div>
                    <div class="panel-body getClients">

                    </div>
                </div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="editClient" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-body"></div>
                <div class="modal-footer">
        			<button type="button" class="btn btn-default" data-dismiss="modal">Затваряне</button>
                </div>
            </div>
        </div>
    </div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script src="https://maps.googleapis.com/maps/api/js?callback=initMap&language=bg" async defer></script>
	<script src="assets/client.js"></script>
</body>
</html>