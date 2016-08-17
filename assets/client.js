var map;
var markers = {};
var infoWindow;

function initMap()
{
	map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 42.66536841838456, lng: 25.21921489374994},
        zoom: 7
    });

	infoWindow = new google.maps.InfoWindow();
}

function addMarker(params)
{
	if (params.data.id) {
		removeMarker(params.data.id);
	}

	var marker = new google.maps.Marker({
		position: params.position,
		map: map,
		icon: params.data.icon,
		draggable: (params.draggable || false),
		data: params.data,
		title: params.data.client
	});
	
	marker.addListener('click', function (e) {
		var iconUrls = {
            'http://maps.google.com/mapfiles/ms/icons/red-dot.png': 'Червен',
            'http://maps.google.com/mapfiles/ms/icons/blue-dot.png': 'Син',
            'http://maps.google.com/mapfiles/ms/icons/green-dot.png': 'Зелен'
		};
		var icons = '<select class="form-control pull-left" onchange="updateIcon(' + this.data.id + ', this.value);" style="width: 100px; margin-left: 5px;">';
		for (var i in iconUrls) {
			icons += '<option' + (i == this.data.icon ? ' selected' : '') + ' value="' + i + '">' + iconUrls[i] + '</option>';
		}
		icons += '</select>';
		var content = '<div style="width: 300px"><table class="table table-bordered">' +
			'<tr><th style="width: 30%">Клиент</th><td>' + this.data.client + '</td></tr>' +
			'<tr><th>Град</th><td>' + this.data.city + '</td></tr>' +
			'<tr><th>Email</th><td>' + this.data.email + '</td></tr>' +
			'<tr><th>Телефон</th><td>' + this.data.phone + '</td></tr>' +
			'<tr><th>Тема</th><td>' + (this.data.theme ? this.data.theme : '') + '</td></tr>' +
			'<tr><th>Съобщение</th><td>' + (this.data.content ? nl2br(this.data.content) : '') + '</td></tr>' +
			'<tr><td colspan="2"><button data-id="' + this.data.id + '" class="toggleDraggable btn btn-default pull-left">Drag ' + (this.getDraggable() ? 'ON' : 'OFF') + '</button>' + icons + '</td></tr>' +
		'</table></div>';
		infoWindow.setContent(content);
		infoWindow.open(map, this);
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
				$('#editClient').modal({
					keyboard: true
				});
			}
		}, 'json');
		
	});
	
	$('#editClient').on('click', 'input[name="btnSave"]', function () {
		
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
		
		$.post('src/ajax.php', data, function (result) {
			if (result.success === 0) {
				alert(result.error);
			} else {
				getClients();
				$('#editClient').modal('hide');
			}
		}, 'json');
		
		return false;
		
	});
	
	$('.getClients').on('click', '.showClient', function () {
		var id = $(this).attr('data-id');
		if (markers[id]) {
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
		
		data.push({name: 'op', value: 'add'});
		
		$.post('src/ajax.php', data, function (result) {
			if (result.success === 0) {
				alert(result.error);
			} else {
				form[0].reset();
				getClients();
			}
		}, 'json');
		
		return false;
	});
});