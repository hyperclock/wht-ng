
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


function more_uploads()
{
    if(document.forms.length == 0) {
        var n = 1;
    } else {
        var n = document.form1.num_files.value;

        document.close();
        document.open();
        document.write();
    }

    document.write("<html><head><meta http-equiv=\"content-type\" content=\"text/html; charset=ISO-8859-1\">    <LINK REL=STYLESHEET TYPE=\"text/css\" HREF=\"css/style.css\"> <SCRIPT SRC=\"moreuploads.js\" TYPE=\"text/javascript\"></SCRIPT></head>");

    document.write("<body><b><i>C L I E N T</i></b><br><br>");
    document.write("<form name=\"form1\" enctype=\"multipart/form-data\" action=\"client_filemanager.php\" method=\"post\" accept-charset=\"ISO-8859-1\">");


    document.write("<select name=\"num_files\" onChange=\"more_uploads()\">");


    for(var i = 0; i < 10; ++i) {
    
        if(i!=n) {
            document.write("<option value=\""+i+"\">"+i+"</option>");
        } else {
            document.write("<option value=\""+i+"\" selected >"+i+"</option>");
        }
    }

    document.write("</select> files to upload <br><br>");


    for(i = 0; i < n; i++) {

        document.write("<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"200000000\"> Send this file: <input name=\"userfile[]\" type=\"file\"><br>");
    }

    var opt = top.main.serverfilemanager.document.getElementsByTagName("option");

    document.write("<input type=\"submit\" value=\"Send File\">");
    document.write("<input type=\"hidden\" name=\"dir\" value=\""+opt[0].value+"/\">");
    document.write("</form></body></html><script type=\"text/javascript\" > document.close(); </script>");
}
