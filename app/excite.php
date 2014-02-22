<?php
/* Excite WebSite Manager
 * author: Bill Creswell
 * license: use it or not
 *
 * Goal: A flat file site that can be easily maintained by user
 * Idea:
 * -A template with fixed main top-level pages
 * -pages, events, news and newsletters will allow user drop in text files
 * Required files
*/

// get requested page
    if (isset($_REQUEST["page"]) && $_REQUEST["page"] != "") {
        $page = $_REQUEST["page"];
    } else $page="home";

/**
 * Get Content
 * this will get the top level content
 */

    function getContent($page)
    {

        switch ($page) {
            case "home":
            case "index":
            case "":
            #content="Index";
                $content = file_get_contents("pages/home.html");
                break;

            case "newsletters":
                $content = file_get_contents("pages/newsletters.html");
                $content.= getDirList($page);
                break;

            case "newsletter":
            case "article":
            case "event":
                if(isset($_REQUEST["issue"])) {
                     $issuepage = "newsletters/" . $_REQUEST['issue'];
                     $content = file_get_contents($issuepage);
                    
                } elseif(isset($_REQUEST["articleid"])) {
                     $viewid = "articles/" . $_REQUEST['articleid'];
                     $content = file_get_contents($viewid);
                 } elseif(isset($_REQUEST["eventid"])) {
                     $viewid = "events/" . $_REQUEST['eventid'];
                     $content = file_get_contents($viewid);
                } else {
                    $content = "no issue specified";   
                }
               
                break;
            case "articles":
                $content = file_get_contents("pages/articles.html");
                $content.= getDirList($page);
                break;

            default:
                $content = file_get_contents("pages/$page.html");
                break;
        }
        return $content;
    }

    function getMenuList()
    {
        $menu = "<ul>";
        if(isset($_REQUEST["page"]) && $_REQUEST["page"] != "home") {
            $menu .= "<li><a href='/'>Home</a></li>";
        }

        $dirlist = getFileList("pages/", true, 1);
        foreach($dirlist as $file) {
            if ($file['name'] != "Home") {
                // Issue #6 Remove Current page link
                if(isset($_REQUEST["page"]) && strtoupper($_REQUEST["page"]) != strtoupper($file['name'])) { 
                    $menu.="<li><a href='?page={$file['call']}'>{$file['name']}</a></li>";
                } elseif(isset($_REQUEST["page"]) && strtoupper($_REQUEST["page"]) === strtoupper($file['name'])) { 
                    $menu.="<li>{$file['name']}</li>";
                } else {
                     $menu.="<li><a href='?page={$file['call']}'>{$file['name']}</a></li>";
                }
            }
        };
    // Insert manual links
       /** $menu.= "<li><a href='https://www.facebook.com/pages/your-site'>Facebook</a></li>"; **/
        $menu.= "</ul>";
        return $menu;
    }

    function getDirList($dir)
    {
        $list = "";
        $dirlist = getFileList("$dir/", true,1);
          if(isset($_REQUEST["page"]) && $_REQUEST["page"] === 'newsletters'){
                foreach($dirlist as $file) {$sname[]=$file["name"];}
 
                array_multisort($sname,SORT_DESC,$dirlist);
          }
        foreach($dirlist as $file) {
            $fname = "";
            if ($file["type"] == "dir") {
                $fname = ucwords(str_replace("_"," ", $file["name"]));
                $list .= "</ul><h3>{$fname}</h3><ul>";
            } else {
                if(isset($_REQUEST["page"]) && $_REQUEST["page"] === 'newsletters'){
                    $issuepath = explode("/",$file["path"]);
                    $issue=$issuepath[1];
                
                    $list.="<li><a href='?page=newsletter&issue=$issue'>{$file['name']}</a></li>";
                }elseif(isset($_REQUEST["page"]) && $_REQUEST["page"] === 'articles'){
                    if($file["type"]==="html") {
                    $issuepath = explode("/",$file["path"]);
                    $issue=$issuepath[1];
                    $list.="<li><a href='?page=article&articleid=$issue'>{$file['name']}</a></li>";
                    } else {
                        $fname = $file["path"];
                        $list.="<li><a href='$fname'>{$file['name']}</a> ({$file['type']})</li>";  
                    }
                }elseif(isset($_REQUEST["page"]) && $_REQUEST["page"] === 'events'){
                    $issuepath = explode("/",$file["path"]);
                    $issue=$issuepath[1];
                    $list.="<li><a href='?page=event&eventid=$issue'>{$file['name']}</a></li>";
                } else {
                     $fname = $file["path"];
                     $list.="<li><a href='$fname'>{$file['name']}</a> ({$file['type']})</li>";
                }
            }
        };
        return $list;
    }

// single directory
//$dirlist = getFileList("./");
// include all subdirectories recursively
//$dirlist = getFileList("./documents/", true);
// include just one or two levels of subdirectories
//$dirlist = getFileList("./documents/", true, 1);
//$dirlist = getFileList("./documents/", true, 2);

// Original PHP code by Chirp Internet: www.chirp.com.au
// Please acknowledge use of this code by including this header.

    function getFileList($dir, $recurse=false, $depth=false)
    {
        $types = array(
            "doc" => "word_icon.png",
            "gif" => "image_icon.png",
            "jpeg" => "image_icon.png",
            "jpg" => "image_icon.png",
            "html" => "ie_icon.png",
            "txt" => "document_icon.png",
            "pdf" => "pdf_icon.png",
            "ppt" => "powerpoint_icon.png",
            "xls" => "excel_icon.png"
        );

        $retval = array();
// add trailing slash if missing
        if (substr($dir, -1) != "/") $dir .= "/";
// open pointer to directory and read list of files
        $d = @dir($dir) or die("getFileList: Failed opening directory $dir for reading");
        while (false !== ($entry = $d->read())) {
  // skip hidden files
            if ($entry[0] == ".") continue;
            if (is_dir("$dir$entry")) {
                $retval[] = array(
                    "path" => "$dir$entry/",
                    "name" => "$entry",
                    "type" => filetype("$dir$entry"),
                    "size" => 0,
                    "lastmod" => filemtime("$dir$entry")
                );
                if ($recurse && is_readable("$dir$entry/")) {
                    if($depth === false) {
                        $retval = array_merge(
                        $retval, getFileList("$dir$entry/", true));
                    } elseif($depth > 0) {
                        $retval = array_merge(
                            $retval, getFileList("$dir$entry/", true, $depth-1));
                    }
                }
            } elseif (is_readable("$dir$entry")) {
                $ic = explode(".", "$entry");
                $type = $ic[1];
                $name = ucwords(str_replace("_"," ",$ic[0]));
                $call = $ic[0];
                $icon = $types[$ic[1]];
                $retval[] = array(
                    "path" => "$dir$entry",
                    "call" => "$call",
                    "name" => "$name",
                    "type" => "$ic[1]",
                    "icon" => "$icon",
                    "size" => filesize("$dir$entry"),
                    "lastmod" => filemtime("$dir$entry") );
            }
        }
        $d->close();
        return $retval;
    }
    function TableOfContents($page,$depth)
	/*AutoTOC function written by Alex Freeman
	* Released under CC-by-sa 3.0 license
	* http://www.10stripe.com/  
     <?php //echo TableOfContents($page,3); ?>
    */
	{
        $html_string = getContent($page);
    //get the headings down to the specified depth
	$pattern = '/<h[2-'.$depth.']*[^>]*>.*?<\/h[2-'.$depth.']>/';
	$whocares = preg_match_all($pattern,$html_string,$winners);
 
	//reformat the results to be more usable
	$heads = implode("\n",$winners[0]);
	$heads = str_replace('<a name="','<a href="#',$heads);
	$heads = str_replace('</a>','',$heads);
	$heads = preg_replace('/<h([1-'.$depth.'])>/','<li class="toc$1">',$heads);
	$heads = preg_replace('/<\/h[1-'.$depth.']>/','</a></li>',$heads);
 
	//plug the results into appropriate HTML tags
	$contents = '<div id="toc"> 
	<p id="toc-header" style="float:right">Contents</p>
	<ul style="float:right">
	'.$heads.'
	</ul>
	</div>';
	echo $contents;
	}
    $theme="billcreswell.com";
    include_once("/theme/" . $theme . "/main.tpl");
?>