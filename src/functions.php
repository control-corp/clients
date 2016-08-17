<?php

function build_options($optionsInput, $value = 0, $emptyOption = '', $emptyOptionValue = "")
{
    $options = '';

    if (!\is_array($value)) {
        $value = [$value];
    }

    if ($emptyOption) {
        $optionsInput = [$emptyOptionValue => $emptyOption] + $optionsInput;
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

/**
 * @return Database
 */
function db()
{
    static $db;

    if ($db === null) {
        $db = new Database(DB_DSN, DB_USER, DB_PASS);
    }

    return $db;
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