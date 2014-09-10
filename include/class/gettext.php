<?php
class Language
{
    var $lang;
    var $charset;
    var $code;
    var $textdomain = "wht";
    
    function Language($lang, $charset, $code)
    {
        global $DocumentRoot, $version;
        
        $this->lang = $lang;
        $this->charset = $charset;
        $this->code = $code;
        
        bindtextdomain($this->textdomain, "$DocumentRoot/$version/locale");
        setlocale(LC_ALL, $code);
        bind_textdomain_codeset ($this->textdomain, $charset);
        textdomain($this->textdomain);
    }
}

?>