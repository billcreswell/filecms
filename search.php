<?php

/* terraserver.de/search-0.2-11.04.2002 - http://www.terraserver.de/*/

$my_server = "http://".getenv("SERVER_NAME");
$my_webroot = "/filecms/";
$my_root = getenv("DOCUMENT_ROOT") . "/filecmsc/";

$s_dirs = array("pages","newsletters","events");
$s_skip = array("..",".","subdir2");
$s_files = "html|htm|HTM|HTML|php|txt|doc";

$min_chars = "3";
$max_chars = "30";

$default_val = "Searchphrase";

$limit_hits = array("5","10","25","50","100");

$message_1 = "Invalid Searchterm!";
$message_2 = "Please enter at least '$min_chars', highest '$max_chars' characters.";
$message_3= "Your searchresult for:";
$message_4 = "Sorry, no hits.";
$message_5 = "results";
$message_6 = "Match case";

$no_title = "Untitled";
$limit_extracts_extracts = "";
$byte_size = "51200";

// render search_form():
    function search_form($HTTP_GET_VARS, $limit_hits, $default_val, $message_5, $message_6, $PHP_SELF)
    {
        @$keyword = $HTTP_GET_VARS['keyword'];
        @$case = $HTTP_GET_VARS['case'];
        @$limit = $HTTP_GET_VARS['limit'];
        echo
        "<form action=\"$PHP_SELF\" method=\"GET\">\n",
        "<input type=\"hidden\" value=\"SEARCH\" name=\"action\">\n",
        "<input type=\"text\" name=\"keyword\" class=\"text\" size=\"10\"  maxlength=\"30\" value=\"";
        if (!$keyword) echo "$default_val";
        else echo str_replace("&amp;","&",htmlentities($keyword));
        echo "\" ";
        echo "onFocus=\" if (value == '";
        if (!$keyword) echo "$default_val";
        else echo str_replace("&amp;","&",htmlentities($keyword));
        echo "') {value=''}\" onBlur=\"if (value == '') {value='";
        if (!$keyword) echo "$default_val";
        else echo str_replace("&amp;","&",htmlentities($keyword));
        echo "'}\"> ";
        $j = count($limit_hits);
        if ($j == 1) echo "<input type=\"hidden\" value=\"".$limit_hits[0]."\" name=\"limit\">";
        elseif ($j>1) {
            echo
            "<select name=\"limit\" class=\"select\">\n";
            for ($i=0;$i<$j;$i++) {
                echo "<option value=\"".$limit_hits[$i]."\"";
                if($limit==$limit_hits[$i]) echo "SELECTED";
                echo ">".$limit_hits[$i]." $message_5</option>\n";
            }
            echo "</select> ";
        }
        echo
            "<input type=\"submit\" value=\"OK\" class=\"button\">\n",
            "<br>\n",
            "<span class=\"checkbox\">$message_6</span>
            <input type=\"checkbox\" name=\"case\" value=\"true\" class=\"checkbox\"";
            if ($case)  echo " CHECKED";
            echo ">\n",
            "<br>\n",
            "<a href=\"http://www.terraserver.de/\" class=\"ts\" target=\"_blank\">Powered by terraserver.de/search</a>",
            "</form>\n";
    }

    # search_headline():
    function search_headline($HTTP_GET_VARS, $message_3) {
        @$keyword = $HTTP_GET_VARS['keyword'];
        @$action = $HTTP_GET_VARS['action'];
        if ($action == "SEARCH") // Volltextsuche
        echo "<h1 class=\"result\">$message_3 '<i>".htmlentities(stripslashes($keyword))."</i>'</h1>";
    }

    # search_error():
    function search_error($HTTP_GET_VARS, $min_chars, $max_chars, $message_1, $message_2, $limit_hits) {
        global $HTTP_GET_VARS;
        @$keyword=$HTTP_GET_VARS['keyword'];
        @$action=$HTTP_GET_VARS['action'];
        @$limit=$HTTP_GET_VARS['limit'];
        if ($action == "SEARCH") {
            if (strlen($keyword)<$min_chars||strlen($keyword)>$max_chars||!in_array ($limit, $limit_hits)) {
                echo "<p class=\"result\"><b>$message_1</b><br>$message_2</p>";
                $HTTP_GET_VARS['action'] = "ERROR";
            }
        }
    }

    # search_dir():
    function search_dir($my_server, $my_root, $s_dirs, $s_files, $s_skip, $message_1, $message_2, $no_title, $limit_extracts, $byte_size, $HTTP_GET_VARS) {
        global $count_hits;
        @$keyword = $HTTP_GET_VARS['keyword'];
        @$action = $HTTP_GET_VARS['action'];
        @$limit = $HTTP_GET_VARS['limit'];
        @$case = $HTTP_GET_VARS['case'];

        if($action == "SEARCH") {
            foreach($s_dirs as $dir) {
                $handle = @opendir($my_root.$dir);
                while ($file = @readdir($handle)) {
                    if (in_array($file, $s_skip)) { continue; }
                    elseif ($count_hits >= $limit) { break; }
                    elseif(is_dir($my_root.$dir."/".$file)) {
                        $s_dirs = array("$dir/$file");
                        search_dir($my_server, $my_root, $s_dirs, $s_files, $s_skip, $message_1, $message_2, $no_title, $limit_extracts, $byte_size, $HTTP_GET_VARS); // search_dir() rekursiv auf alle Unterverzeichnisse aufrufen
                    } elseif (preg_match("/($s_files)$/i", $file)) {
                    // Alle Dateien gemaess Endungen $s_files
                    $fd = fopen($my_root.$dir."/".$file,"r");
                    $text = fread($fd, $byte_size); // 50 KB
                    $keyword_html = htmlentities($keyword);
                    if ($case) {
                        // Gross-/Kleinschreibung beruecksichtigen?
                        $do=strstr($text, $keyword)||strstr($text, $keyword_html);
                    } else {
                        $do=stristr($text, $keyword)||stristr($text, $keyword_html);
                    }
                    if ($do) {
                        $count_hits++;
                        // Treffer zaehlen
                        if(preg_match_all("=<title[^>]*>(.*)</title>=siU", $text, $titel)) {
                        // Generierung des Link-Textets aus <title>...</title>
                            if(!$titel[1][0]) // <title></title> ist leer...
                                $link_title=$no_title; // ...also $no_title
                            else $link_title=$titel[1][0];  // <title>...</title> vorhanden...
                        } else {
                            $link_title=$no_title; // ...ansonsten $no_title
                        }
                        $page = explode(".",$file);
                        echo "<a href=\"/code/excitecms?page=$page[0]\" target=\"_self\" class=\"result\">$count_hits.  $link_title</a><br>"; // Ausgabe des Links
                        $auszug = strip_tags($text);
                        $keyword = preg_quote($keyword); // unescapen
                        $keyword = str_replace("/","\/","$keyword");
                        $keyword_html = preg_quote($keyword_html); // unescapen
                        $keyword_html = str_replace("/","\/","$keyword_html");
                        echo "<span class=\"extract\">";
                        if (preg_match_all("/((\s\S*){0,3})($keyword|$keyword_html)((\s?\S*){0,3})/i", $auszug, $match, PREG_SET_ORDER));
                        {
                            if (!$limit_extracts)
                                $number = count($match);
                            else
                                $number = $limit_extracts;
                            for ($h = 0; $h<$number; $h++) {
                            // Kein Limit angegeben also alle Vorkommen ausgeben
                                if (!empty($match[$h][3]))
                                    printf("<i><b>..</b> %s<b>%s</b>%s <b>..</b></i>", $match[$h][1], $match[$h][3], $match[$h][4]);
                                }
                        }
                        echo "</span><br><br>";
                        flush();
                        }
                    fclose($fd);
                    }
                }
            @closedir($handle);
            }
        }
    }


    // search_no_hits(): Ausgabe 'keine Treffer' bei der Suche
    function search_no_hits($HTTP_GET_VARS, $count_hits, $message_4)
    {
        @$action=$HTTP_GET_VARS['action'];
        if ($action == "SEARCH" && $count_hits<1) // Volltextsuche, kein Treffer
        echo "<p class=\"result\">$message_4</p>";
    }

// Template
?><!doctype html>
<html>
<head>
<title>Search MCBVI Website</title>
</head>
<body>
<?
// search_form(): Gibt das Suchformular aus
    search_form($HTTP_GET_VARS, $limit_hits, $default_val, $message_5, $message_6, $PHP_SELF);
// search_headline(): Ueberschrift Suchergebnisse
    search_headline($HTTP_GET_VARS, $message_3);
// search_error(): Auf Fehler testen und Suchfehler anzeigen
    search_error($HTTP_GET_VARS, $min_chars, $max_chars, $message_1, $message_2, $limit_hits);
// search_dir(): Volltextsuche in Verzeichnissen (siehe config.php4)
    search_dir($my_server, $my_root, $s_dirs, $s_files, $s_skip, $message_1, $message_2, $no_title, $limit_extracts, $byte_size, $HTTP_GET_VARS);
// search_no_hits(): Ausgabe 'keine Treffer' bei der Suche
    search_no_hits($HTTP_GET_VARS, $count_hits, $message_4);
?>
</body>
</html>
