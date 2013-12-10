<?php

function string_escape ($string)
{
    return '"' . str_replace (array ('"', '\\', "\n", "\r", "\t", "\b", "\v"),
            array ("\\\"", "\\\\", "\\n", "\\r", "\\t", "\\b", "\\v"),
            $string) . '"';
}

function json_encode ($value)
{
    function _is_array ($array)
    {
        $keys = array_keys ($array);
        for ($i = 0; $i < count ($array); $i++)
        {
            if ($i !== $keys[$i])
                return false;
        }
        return true;
    }
    $is_array = _is_array ($value);

    $res = ($is_array ? '[' : '{');

    foreach ($value AS $key => $val)
    {
        if (!$is_array)
        {
            if (is_numeric ($key))
                $res .= $key . ":";
            else if (is_string ($key))
                $res .= string_escape ($key) . ":";
        }

        if (is_numeric ($val))
            $res .= $val . ",";
        else if (is_string ($val))
            $res .= string_escape ($val) . ",";
        else if ($val === NULL)
            $res .= 'null,';
        else if ($val === TRUE)
            $res .= 'true,';
        else if ($val === FALSE)
            $res .= 'false,';
        else if (is_array ($val))
            $res .= json_encode ($val) . ',';
        else
            $res .= 'undefined,';
    }

    return substr ($res, 0, -1) . ($is_array ? ']' : '}');
}
?>
