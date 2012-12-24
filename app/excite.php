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
 * 
*/

#get requested page
if(isset($_REQUEST["page"]) && $_REQUEST["page"] != "") {
  $page=$_REQUEST["page"];
} else $page="index";

/**
 * Get Content
 * this will get the top level content
 */
 
function getContent($page) {

 switch ($page) {
  case "index":
    #content="Index";
    $content = file_get_contents("pages/index.html");
  break;
  
  case "newsletters":
    $content = file_get_contents("pages/newsletters.html");
    $content.= getDirList($page);
  break;
  
  default:
    $content = file_get_contents("pages/$page.html");
  break; 
 }   
 return $content;
}

function getMenuList() {
 $menu="";
 $dirlist = getFileList("pages/", true, 1); 
 foreach($dirlist as $file) { 
  $menu.="<li><a href='?page={$file['call']}'>{$file['name']}</a></li>"; 
 };
return $menu;
}

function getDirList($dir) {
 $list="";
 $dirlist = getFileList("$dir/", true, 1); 
 foreach($dirlist as $file) { 
  $list.="<li>{$file['name']}</li>"; 
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

function getFileList($dir, $recurse=false, $depth=false) { 
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
  if(substr($dir, -1) != "/") $dir .= "/"; 
// open pointer to directory and read list of files 
  $d = @dir($dir) or die("getFileList: Failed opening directory $dir for reading"); 
  while(false !== ($entry = $d->read())) { 
  // skip hidden files 
    if($entry[0] == ".") continue; 
    if(is_dir("$dir$entry")) { 
      $retval[] = array( 
        "path" => "$dir$entry/", 
        "name" => "$entry", 
        "type" => filetype("$dir$entry"), 
        "size" => 0, 
        "lastmod" => filemtime("$dir$entry")
      ); 
      if($recurse && is_readable("$dir$entry/")) { 
        if($depth === false) { 
          $retval = array_merge(
          $retval, getFileList("$dir$entry/", true)); 
        } elseif($depth > 0) { 
          $retval = array_merge(
            $retval, getFileList("$dir$entry/", true, $depth-1)); 
        } 
      } 
    } elseif(is_readable("$dir$entry")) { 
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
  } $d->close(); return $retval; } 

?><!doctype html>
<htm>
<head>
<title><?php echo $page; ?></title>
<link href="app/miblind.css" rel="stylesheet" type="text/css" />
</head>
<body>

<div id="Banner">
  
<div>Blind and Visually Impaired 
  <a href="#Menu">Menu</a>
  <a href="#Content">Content</a>
  <form style="display:inline;float:right;" action="search.php" method="GET">
      <label for="SiteSearch">Search</label>
      <input type="search" name="keyword" id="SiteSearch" placeholder="search" />
      <input type="submit" name="action" value="SEARCH" />
      <input type="hidden" name="limit" value="10" />
  </form> 
  <br style="clear:both"/>
</div>

</div>

<div id="Content">

<div id="Main">
<?php echo getContent($page); ?>
</div>

<div id="Menu" role="nav">
<?php echo getMenuList(); ?>
</div>

</div>
</body>
</html>
