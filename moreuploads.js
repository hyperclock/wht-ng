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
