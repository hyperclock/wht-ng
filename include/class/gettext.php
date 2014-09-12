<?php
/**
*    Web Hosting Toolkit - Next Generation (WHT-NG)
*    Copyright (C) 2014  Jimmy M. Coleman <hyperclock@ok.de>
*    Copyright (C) 2003  Nikolay Ivanov <nivanov@email.com> (GPLv2)
*
*    This program is free software: you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation, either version 3 of the License, or
*    (at your option) any later version.
*
*    This program is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class Language
{
    var $lang;
    var $charset;
    var $code;
    var $textdomain = "wht-ng";
    
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
