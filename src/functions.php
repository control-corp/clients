<?php

date_default_timezone_set('Europe/Sofia');

function build_options($optionsInput, $value = 0, $emptyOption = '', $emptyOptionValue = "")
{
    $options = '';

    if (!\is_array($value)) {
        $value = array($value);
    }

    if ($emptyOption) {
        $optionsInput = array($emptyOptionValue => $emptyOption) + $optionsInput;
    }

    foreach ($optionsInput as $optionGroup => $group) {
        if (\is_array($group)) {
            $options .= '<optgroup label="' . $optionGroup . '">';
            $options .= build_options($group, $value, '', '');
            $options .= '</optgroup>';
        } else {
            $selected = (\in_array($optionGroup, $value) ? ' selected="selected"' : '');
            $options .= '<option' . $selected . ' value="' . $optionGroup . '">' . $group . '</option>';
        }
    }

    return $options;
}

function value($haystack, $field, $default = '')
{
    if (!is_array($haystack)) {
        return $default;
    }

    return isset($haystack[$field]) ? htmlspecialchars($haystack[$field], ENT_QUOTES) : $default;
}

function getLatLng($address) {

    $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address) . '&key=' . GOOGLE_KEY;

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);
}

/**
 * @return Database
 */
function db()
{
    static $db;

    if ($db === null) {
        $db = new Database(DB_DSN, DB_USER, DB_PASS);
		$db->exec('SET NAMES utf8');
    }

    return $db;
}

class Pointers
{
	public static $data = array(
		'http://map.insa.bg/assets/pointers/1.PNG' => 'Ромпетрол – България',
		'http://map.insa.bg/assets/pointers/2.PNG' => 'Полисан – АД',
		'http://map.insa.bg/assets/pointers/3.PNG' => 'ДМВ – ЕООД',
		'http://map.insa.bg/assets/pointers/4.PNG' => 'Сакса – ООД',
		'http://map.insa.bg/assets/pointers/5.PNG' => 'Сима – ЕООД	',
		'http://map.insa.bg/assets/pointers/6.PNG' => 'Ведима – ООД',
		'http://map.insa.bg/assets/pointers/7.PNG' => 'Екопетрол – ЕООД',
		'http://map.insa.bg/assets/pointers/8.png' => 'Товарни превози – АД',
		'http://map.insa.bg/assets/pointers/9.png' => 'Разни',
		'http://maps.google.com/mapfiles/ms/icons/blue-dot.png' => 'Нашите клиенти, които работят с горива и масла',
		'http://maps.google.com/mapfiles/ms/icons/green-dot.png' => 'Нашите клиенти, които работят с горива',
	);
}

class View
{
    public function render($path)
    {
        ob_start();
        include $path;
        return ob_get_clean();
    }
}