<?php
final class UrlShortner
{
    protected $fp;

    public function
    __construct ($database = 'links.db')
    {
        if (($this->fp = fopen ($database, "a+")) === FALSE)
            throw new Exception ("Unable to open database");
    }

    private function
    search ($key)
    {
        fseek ($this->fp, 0);
        while (!feof ($this->fp))
        {
            $line = fgets ($this->fp);
            if (preg_match ('/^' . $key . '\|/', $line))
                break;
        }
        if (!preg_match ('/^' . $key . '\|/', $line))
            return false;
        return preg_replace ('/^.+?\|/', '', $line);
    }

    private function
    searchLink ($key)
    {
        fseek ($this->fp, 0);
        while (!feof ($this->fp))
        {
            preg_match ('/^(.+?)\|(.+)$/', fgets ($this->fp), $matches);
            $line = array ($matches[1], $matches[2]);
            if ($line[1] == $key)
                break;
        }
        if ($line[1] != $key)
            return false;
        return $line[0];
    }

    public function
    get ($key)
    {
        if (($link = $this->search ($key)) === false)
            throw new Exception ("Key not stored in database");
        return $link;
    }

    private function
    generateCode ()
    {
        $voc = preg_split ('//', "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789");
        do
        {
            list ($code, $random) = array ("", array_rand ($voc, 4));
            foreach ($random AS $ran)
                $code .= $voc[$ran];
        }
        while ($this->search ($code) !== false);
        return $code;
    }

    public static function
    checkLink ($url)
    {
        $url = rtrim (trim ($url), '/');
        if (!preg_match ('/^(http|https|ftp):\/\/[a-z0-9_]+([\-\.]{1}[a-z_0-9]+)*\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\/.*)?$/i', $url))
		$url = "http:/". "/" . $url;
	

	if (!preg_match (
            '/^(http|https|ftp):\/\/[a-z0-9_]+([\-\.]{1}[a-z_0-9]+)*\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\/.*)?$/i',
            $url))
                return "Invalid URI form";
    
        preg_match ('/^([a-zA-Z]+[a-zA-Z0-9\+\-\.]*):/', $url, $matches);
        if (!in_array ($matches[1], array ('http', 'https', 'ftp')))
            return "Unknown protocol";
        $protocol = $matches[1];
        preg_match ('/^[a-zA-Z]+[a-zA-Z0-9\+\-\.]*:\/\/(?:(?:(?:[A-Za-z0-9;\/\?:@&=\+\$,]|%[0-9A-Fa-f]{2})+@)?((?:(?:[a-zA-Z0-9]+|[a-zA-Z0-9]+[a-zA-Z0-9-]*[a-zA-Z0-9]+)\.)*(?:[a-zA-Z]+|[a-zA-Z]+[a-zA-Z0-9-]*[a-zA-Z0-9]+)\.?|[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})(?::[0-9]+)?)/',
            $url, $matches);
        $site = $matches[1];
    
        preg_match ('/^[a-zA-Z]+[a-zA-Z0-9\+\-\.]*:\/\/(?:(?:(?:[A-Za-z0-9;\/\?:@&=\+\$,]|%[0-9A-Fa-f]{2})+@)?(?:(?:(?:[a-zA-Z0-9]+|[a-zA-Z0-9]+[a-zA-Z0-9-]*[a-zA-Z0-9]+)\.)*(?:[a-zA-Z]+|[a-zA-Z]+[a-zA-Z0-9-]*[a-zA-Z0-9]+)\.?|[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})(?::([0-9]+))?)/',
            $url, $matches);
        $port = (empty ($matches[1]) ? ($protocol == 'ftp' ? 21 : 80) : $matches[1]);
    
        $sock = fsockopen ($site, $port, $errno, $errstr, 5);
        if ($errno)
            return "Invalid URL";
    
        fclose ($sock);
        return true;
    }

    public function
    insert ($url)
    {
        if (($error = self::checkLink ($url)) !== true)
            throw new Exception ($error);
        if (($link = $this->searchLink ($url)) !== false)
            return $link;
	if (!preg_match ('/^(http|https|ftp):\/\/[a-z0-9_]+([\-\.]{1}[a-z_0-9]+)*\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\/.*)?$/i', $url))
                $url = "http:/". "/" . $url;
        fseek ($this->fp, 0, SEEK_END);
        $generated = $this->generateCode ();
        fwrite ($this->fp ,"{$generated}|{$url}\n");
        return $generated;
    }

    public function
    __destruct ()
    {
        fclose ($this->fp);
    }
}
?>
