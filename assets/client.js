var map;
var markers = {};
var infoWindow;

function initMap()
{
	map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 42.66536841838456, lng: 25.21921489374994},
        zoom: 7,
		mapTypeId: google.maps.MapTypeId.HYBRID
    });

	infoWindow = new google.maps.InfoWindow();
	
	google.maps.event.addListener(infoWindow, 'closeclick', function() {
		$('.clientItem').css('background-color', 'inherit');
	});
}

function addMarker(params)
{
	if (params.data.id) {
		removeMarker(params.data.id);
	}

	var image = {
		url:  params.data.icon,
		// This marker is 20 pixels wide by 32 pixels high.
		scaledSize: new google.maps.Size(32, 32)
	};

	var marker = new google.maps.Marker({
		position: params.position,
		map: map,
		icon: image,
		draggable: (params.draggable || false),
		data: params.data,
		title: params.data.client
	});
	
	marker.addListener('click', function (e) {
		var icons = '<select class="form-control pull-left" onchange="updateIcon(' + this.data.id + ', this.value);" style="width: 100px; margin-left: 5px;">';
		for (var i in pointers) {
			icons += '<option' + (i == this.data.icon ? ' selected' : '') + ' value="' + i + '">' + pointers[i] + '</option>';
		}
		icons += '</select>';
		var content = '<div data-id="' + this.data.id + '" style="width: 300px"><table style="width: 100%; margin-bottom: 5px;" class="client table table-bordered">' +
			'<tr><th style="width: 30%">Клиент</th><td>' + nl2br(this.data.client) + '</td></tr>' +
			'<tr><th>Град</th><td>' + this.data.city + '</td></tr>' +
			'<tr><th>Адрес</th><td>' + (this.data.address ? nl2br(this.data.address) : '') + '</td></tr>' +
			'<tr><th>Email</th><td>' + this.data.email + '</td></tr>' +
			'<tr><th>Телефон</th><td>' + this.data.phone + '</td></tr>' +
			'<tr><th>Описание</th><td>' + (this.data.theme ? this.data.theme : '') + '</td></tr>' +
			'<tr><th>Бележки</th><td>' + (this.data.content ? nl2br(this.data.content) : '') + '</td></tr>' +
			'<tr><td colspan="2" style="text-align: right"><a style="padding: 3px;" class="wayto" target="_blank" href="https://www.google.com/maps/dir/' + this.data.lat + ',' + this.data.lng + '?hl=bg">Упътване до тук</a></td></tr>' +
		'</table>' + 
		'<button data-id="' + this.data.id + '" class="toggleDraggable btn btn-default pull-left">Drag ' + (this.getDraggable() ? 'ON' : 'OFF') + '</button>' + icons
		'</div>';
		infoWindow.setContent(content);
		infoWindow.open(map, this);
		$('.clientItem').css('background-color', 'inherit');
		$('.clientItem[data-id="' + this.data.id + '"]').css('background-color', '#f2f2f2');
	});
	
	marker.addListener('dragend', function (e) {
		updateLatLng(this.data.id, e.latLng);
	});

	markers[marker.data.id] = marker;
}

function updateIcon(id, icon)
{
	if (markers[id]) {
		$.post('src/ajax.php', {op: 'updateIcon', id: id, icon: icon}, function (result) {
			if (result.success === 0) {
				alert(result.error);
			} else {
				getClients();
			}
		}, 'json');
	}
}

function removeMarker(id)
{
	var key = id;
	
	if (markers[key]) {
		markers[key].setMap(null);
		delete markers[key];
	}
}

function updateLatLng(id, position)
{
	$.post('src/ajax.php', {op: 'updateLatLng', lat: position.lat(), lng: position.lng(), id: id}, function (result) {
		if (markers[id]) {
			markers[id].data.lat = position.lat();
			markers[id].data.lng = position.lng();
			var wayto = $('div[data-id="' + id + '"]').find('.wayto');
			if (wayto.length) {
				wayto.attr('href', 'https://www.google.com/maps/dir/' + position.lat() + ',' + position.lng() + '?hl=bg');
			}
		}
		if (result.success === 0) {
			alert(result.error);
		}
	}, 'json');
}

function getClients(cb)
{
	$('.getClients').html('Зареждане...');
	
	$.post('src/ajax.php', {op: 'getClients'}, function (result) {
		if (result.success === 0) {
			alert(result.error);
		} else {
			$('.getClients').html(result.data.html);
			var clients = result.data.clients;
			for (var i = 0, n = clients.length; i < n; ++i) {
				var client = clients[i];
				addMarker({
					position: new google.maps.LatLng(client.lat, client.lng),
					//draggable: true,
					data: client,
					map: map
				});
			}
			if (typeof(cb) === 'function') {
				cb();
			}
		}
	}, 'json');
}

function nl2br (str, is_xhtml) {
	var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br ' + '/>' : '<br>';
	return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

jQuery.expr[":"].icontains = jQuery.expr.createPseudo(function (arg) {                                                                                                                                                                
    return function (elem) {                                                            
        return jQuery(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;        
    };                                                                                  
});

$(function () 
{
	function onInput() {
		var items = $('.getClients .clientItem');
		var value = $(this).val();
		items.each(function () {
			if (value) {
				if ($(this).is(':icontains("' + value + '")')) {
					$(this).show();
				} else {
					$(this).hide();
				}
			} else {
				$(this).show();
			}
		});
	};
	
	getClients(function () {
		onInput.call($('input[name="search"]')[0]);
	});

	$('input[name="search"]').on('input', onInput);

	$('body').on('click', '.toggleDraggable', function () {
		var id = $(this).attr('data-id');
		if (markers[id]) {
			var old = markers[id].getDraggable();
			if (old) {
				markers[id].setDraggable(false);
				$(this).html('Drag OFF');
			} else {
				markers[id].setDraggable(true);
				$(this).html('Drag ON');
			}
		}
	});
	
	$('.getClients').on('click', '.editShowClient', function () {
		var id = $(this).attr('data-id');
		$.post('src/ajax.php', {op: 'editShowClient', id: id}, function (result) {
			if (result.success === 0) {
				alert(result.error);
			} else {
				$('#editClient .modal-body').html(result.data);
				$('#editClient .select2').select2({language: 'bg', width: '100%'});
				$('#editClient').modal({
					keyboard: true
				});
			}
		}, 'json');
		
	});
	
	$('#editClient').on('click', 'input[name="btnSave"]', function () {
		
		var that = $(this);
		
		var error = 0;
		var form  = $(this).closest('form');

		form.find('.errors').remove();
		
		form.find('input[type="text"], textarea, select').each(function () {
			if ($(this).attr('required') && $.trim(this.value) === '') {
				$(this).after('<span class="errors">Полето е задължително</span>');
				error = 1;
			}
		});
		
		if (error) {
			return false;
		}
		
		var data = form.serializeArray();
		
		data.push({name: 'op', value: 'editClient'});
		data.push({name: 'id', value: $(this).attr('data-id')});
		
		that.attr('disabled', true);
		$.post('src/ajax.php', data, function (result) {
			if (result.success === 0) {
				alert(result.error);
			} else {
				getClients();
				$('#editClient').modal('hide');
			}
			that.attr('disabled', false);
		}, 'json');
		
		return false;
		
	});
	
	$('.fitToAll').on('click', function () {
		var hasMarkers = 0;
		var bounds = new google.maps.LatLngBounds();
		for (var i in markers) {
			bounds.extend(markers[i].getPosition());
			hasMarkers = 1;
		}
		if (hasMarkers) {
			map.fitBounds(bounds);
			infoWindow.close();
			$('.clientItem').css('background-color', 'inherit');
		}
	});
	
	$('.getClients').on('click', '.showClient', function () {
		var id = $(this).attr('data-id');
		if (markers[id]) {
			map.setZoom(17);
			map.setCenter(markers[id].getPosition());
			new google.maps.event.trigger(markers[id], 'click');
		}
	});
	
	$('.getClients').on('click', '.removeClient', function () {
		
		if (!confirm('Сигурни ли сте, че искате ли да изтриете записа?')) {
			return;
		}
		
		var that = $(this);
		var id   = that.attr('data-id');

		$.post('src/ajax.php', {op: 'removeClient', id: id}, function (result) {
			if (result.success === 0) {
				alert(result.error);
			} else {
				removeMarker(id);
				that.closest('.clientItem').remove();
			}
		}, 'json');
		
	});
	
	$('.addForm input[name="btnSave"]').on('click', function () {

		var that = $(this);

		var error = 0;
		var form  = $(this).closest('form');
		
		form.find('.errors').remove();
		
		form.find('input[type="text"], textarea, select').each(function () {
			if ($(this).attr('required') && $.trim(this.value) === '') {
				$(this).closest('[class^="col-md-"]').append('<span class="errors">Полето е задължително</span>');
				error = 1;
			}
		});
		
		if (error) {
			return false;
		}
		
		var data = form.serializeArray();
		
		data.push({name: 'op', value: 'add'});
		
		that.attr('disabled', true);
		
		$.post('src/ajax.php', data, function (result) {
			if (result.success === 0) {
				alert(result.error);
			} else {
				form[0].reset();
				$('select[name="cityId"]').val('');
				$('select[name="cityId"]').select2({language: 'bg'});
				getClients();
			}
			that.attr('disabled', false);
		}, 'json');
		
		return false;
	});
	
	$('.select2').select2({language: 'bg'});
});