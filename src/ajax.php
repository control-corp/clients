<?php

include __DIR__ . '/../config.php';
include __DIR__ . '/database.php';
include __DIR__ . '/functions.php';

$result = array(
    'success' => 0,
    'data'    => null,
    'error'   => 'Invalid operation',
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $post = $_POST;

    if (isset($post['op'])) {

        $result['success'] = 1;
        $result['error']   = '';

        try {
            switch ($post['op']) {
                case 'editShowClient' :
                    $view = new View();
                    $view->item = db()->fetch('SELECT * FROM clients WHERE id = ' . (int) $post['id']);
                    $result['data'] = $view->render(__DIR__ . '/views/_add.php');
                    break;
                case 'editClient' :

                    $client = db()->fetch('SELECT * FROM clients WHERE id = ' . (int) $post['id']);

                    if ($client) {

                        $lat = $client['lat'];
                        $lng = $client['lng'];

                        if ($post['cityId'] != $client['cityId'] || $post['address'] !== $client['address']) {

                            $city = db()->fetch('SELECT lat, lng, name FROM cities WHERE id = ' . (int) $post['cityId']);

                            if ($city) {
                                $coordinates = getLatLng($post['address'] . ', ' . $city['name'] . ', България');
                                if (!isset($coordinates['error_message']) && !empty($coordinates['results'])) {
                                    $lat = $coordinates['results'][0]['geometry']['location']['lat'];
                                    $lng = $coordinates['results'][0]['geometry']['location']['lng'];
                                } else {
                                    $lat = $city['lat'] ? $city['lat'] : '42.66536841';
                                    $lng = $city['lng'] ? $city['lng'] : '42.66536841';
                                }
                            }
                        }

                        db()->update('clients', array(
                            'client'  => $post['client'],
                            'icon'    => $post['icon'],
                            'cityId'  => $post['cityId'],
                            'address' => $post['address'],
                            'email'   => $post['email'],
                            'phone'   => $post['phone'],
                            'theme'   => ($post['theme'] ? $post['theme'] : null),
                            'content' => ($post['content'] ? $post['content'] : null),
                            'lat'     => $lat,
                            'lng'     => $lng,
                        ), array('id' => $client['id']));
                    }
                    break;
                case 'updateLatLng' :
                    db()->update(
                        'clients',
                        array('lat' => $post['lat'], 'lng' => $post['lng']),
                        array('id' => (int) $post['id'])
                    );
                    break;
                case 'updateIcon' :
                    db()->update(
                        'clients',
                        array('icon' => $post['icon']),
                        array('id' => (int) $post['id'])
                    );
                    break;
                case 'getClients' :
                    $view = new View();
                    $view->clients  = db()->fetchAll('SELECT * FROM clients ORDER BY id DESC');
                    $cities = db()->fetchPairs('SELECT id, CONCAT(name, ", ", code) FROM cities');
                    foreach ($view->clients as $k => $v) {
                        $view->clients[$k]['city'] = isset($cities[$v['cityId']]) ? $cities[$v['cityId']] : '';
                    }
                    $result['data'] = array(
                        'html'    => $view->render(__DIR__ . '/views/_clients.php'),
                        'clients' => $view->clients,
                    );
                    break;
                case 'removeClient' :
                    db()->delete('clients', array('id' => (int) $post['id']));
                    break;
                case 'add' :

                    $city = db()->fetch('SELECT lat, lng, name FROM cities WHERE id = ' . (int) $post['cityId']);

                    $lat = null;
                    $lng = null;

                    if ($city) {
                        $coordinates = getLatLng($post['address'] . ', ' . $city['name'] . ', България');
                        if (!isset($coordinates['error_message']) && !empty($coordinates['results'])) {
                            $lat = $coordinates['results'][0]['geometry']['location']['lat'];
                            $lng = $coordinates['results'][0]['geometry']['location']['lng'];
                        } else {
                            $lat = $city['lat'] ? $city['lat'] : '42.66536841';
                            $lng = $city['lng'] ? $city['lng'] : '42.66536841';
                        }
                    }

                    db()->insert('clients', array(
                        'client'  => $post['client'],
						'icon'    => $post['icon'],
                        'cityId'  => $post['cityId'],
                        'address' => $post['address'],
                        'email'   => $post['email'],
                        'phone'   => $post['phone'],
                        'theme'   => ($post['theme'] ? $post['theme'] : null),
                        'content' => ($post['content'] ? $post['content'] : null),
                        'lat'     => $lat,
                        'lng'     => $lng,
                    ));

                    break;
                default:
                    throw new Exception('Invalid operation');
            }
        } catch (Exception $e) {
            $result['success'] = 0;
            $result['error']   = $e->getMessage();
        }
    }
}

header('Content-Type: application/json');

echo json_encode($result);