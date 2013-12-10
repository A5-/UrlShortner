<?php
error_reporting (0);
header ("Content-Type: text/plain");
include_once ('config.php');
include_once ('UrlShortner.class.php');
if (!function_exists ('json_encode'))
{
    include_once ('json.php');
}

if (!isset ($_GET['url']) or
        empty ($_GET['url']))
    die (json_encode (array ("Error" => "No link Detected")));

try
{
    $db = new UrlShortner ($database);
    echo json_encode (array ("short_link" => $db->insert ($_GET['url'])));
}
catch (Exception $e)
{
    die (json_encode (array ("Error" => $e->getMessage ())));
}
?>
