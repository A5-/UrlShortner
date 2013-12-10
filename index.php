<?php
error_reporting (0);
include_once ('config.php');
include_once ('UrlShortner.class.php');
include_once ('UrlTemplate.class.php');


$template = new UrlTemplate ();

if (array_key_exists ("redirect", $_GET))
{
    try
    {
        $db = new UrlShortner ($database);
        header ("Location: " . $db->get ($_GET['redirect']));
    }
    catch (Exception $e)
    {
        echo $template->error ($e->getMessage ());
    }
}
else if (array_key_exists ("url", $_GET))
{
    try
    {
        $db = new UrlShortner ($database);
        echo $template->link ($db->insert ($_GET['url']));
    }
    catch (Exception $e)
    {
        echo $template->error ($e->getMessage ());
    }
}
else
{
    echo $template->index ();
}
?>
