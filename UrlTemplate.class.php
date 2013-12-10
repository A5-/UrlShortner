<?php
final class UrlTemplate
{
    private $skeleton, $serv_name, $url, $header;

    private function
    readTemplate ($template)
    {
        $files = array (
            'skeleton'  => 'template/skeleton.tpl',
            'index'     => 'template/index.tpl',
            'generate'  => 'template/generated.tpl',
            'error'     => 'template/error.tpl'
        );

        if (!array_key_exists ($template, $files))
            die ("Don't have template files");

        $template = $files[$template];
        if (!($fp = fopen ($template, "r")))
            die ("Can't open {$template}");
        $res = fread ($fp, filesize ($template));
        fclose ($fp);
        return $res;
    }

    public function
    __construct ($title = 'UrlShortner')
    {
        $this->skeleton = $this->readTemplate ('skeleton');
        $this->serv_name = $title;
        if (!array_key_exists ('HTTPS', $_SERVER))
            $_SERVER['HTTPS'] = 'off';
        $url = str_replace ('index.php', '', ($_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://') .
            $_SERVER['SERVER_NAME'] .
            ($_SERVER['SERVER_PORT'] != '80' ? ":" . $_SERVER["SERVER_PORT"] : "") .
            $_SERVER["PHP_SELF"]);
        $this->url = (preg_match ('/\/$/', $url) ? $url : $url . "/");
    }

    public function
    index ()
    {
        return str_replace (
                array ('{:TITLE:}',
                    '{:CONTENT:}',
					'{:HEADER:}'),
                array ($this->serv_name,
                    $this->readTemplate ('index')),
                $this->skeleton,
				$header);
    }

    public function
    link ($code)
    {
        return str_replace (
                array ('{:TITLE:}',
                    '{:CONTENT:}'),
                array ($this->serv_name . " - Generated",
                    str_replace (
                        '{:SHORT_LINK:}',
                        $this->url . $code,
                        $this->readTemplate ('generate'))),
                $this->skeleton);
    }

    public function
    error ($description)
    {
        return str_replace (
                array ('{:TITLE:}',
                    '{:CONTENT:}'),
                array ($this->serv_name . " - Error",
                    str_replace ('{:ERROR:}', $description,
                        $this->readTemplate ('error'))),
                $this->skeleton);
    }
}
?>
