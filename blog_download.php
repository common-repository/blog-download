<?php
 
    /* 
    Plugin Name: Blog Download
    Plugin URI: http://www.zencherry.com
    Description: Plugin for downloading posts from wordpress in pdf, doc and html format. 
    Author: Rodrigo Villanueva Ceballos
    Version: 1.3 
    Author URI: http://www.zencherry.com 
    License: GPLv2
    */  
add_action('admin_menu', 'blogd_plugin_menu');

function blogd_plugin_menu() {
	add_options_page('Blog Download Options', 'Blog Download', 'manage_options', 'blogd-unique-identifier', 'blogd_plugin_options');
}

function blogd_plugin_options() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	echo '<div class="wrap">';
	echo '<h3><p>Blog Download allows visitors to download your blog posts in PDF, DOC and HTML format.<br><br> 
	You can delete the files created at wp-content/plugins/blog-download/downloads/,<br> 
	they are only temporal files created for the conversion, after the user downloads the blog post,<br>
 	they are not required. This can be done automaticly with a cron job.<br> <br> 
 	If you want to change the language of your plugin you can do so in config.php:<br><br>  
	$docformat = "Document Format:";<br><br> 
	$selectformat = "Select Format...";<br><br> 
	$download = "Download";</p>
 	</h3>';
	echo '</div>';
}

function adddownloadblog($content) {
      include 'wp-content/plugins/blog-download/config.php';
      $downloadbox="";
      if(isset($_GET["p"])){
        $cuerpohtml = get_the_content();
	$prenom = rand(1000, 1000000);
	$titulo = get_the_title();
	$cuerpohtml="<H3>".$titulo."</H3>".$cuerpohtml;
	$titulo = str_replace(" ", "z20z", $titulo);
	$titulo = preg_replace("/[^a-zA-Z0-9\s]/", "", $titulo);
	$titulo = str_replace("z20z", "_", $titulo);
        $downloadbox= '<form method="post"><b>'.$docformat.' 
	<select name="formatos">
	<option value="noselecionado">'.$selectformat.'</option>
	<option value="doc">.doc</option>
	<option value="pdf">.pdf</option>
	<option value="html">.html</option>
	</select> 
	</b><input type="submit" style="width: 128px" value="'.$download.'" /></form>';
	$nomcodi = "{$prenom}{$titulo}.html";
	$nomcodidoc = "{$prenom}{$titulo}.doc";
	$nomcodipdf = "{$prenom}{$titulo}.pdf";
	$nomcodipdfn = "{$titulo}.pdf";
	$titulocode= rawurlencode($titulo);
        
	if($_POST['formatos'] == "doc"||$_POST['formatos'] == "html"||$_POST['formatos'] == "pdf"){   
		//////////////////////HTML   BASE
		$myFile = "wp-content/plugins/blog-download/downloads/{$nomcodi}";
		$fh = fopen($myFile, 'w') or die("can't open file");
		fwrite($fh, $cuerpohtml);
		fclose($fh);
		sleep(3);
		////////////////////// PDF HERE
		if($_POST['formatos'] == "pdf"){ 
			$downloadbox= $downloadbox. '<a href="http://zencherry.com/elgg/index.php?dir=';
			$downloadbox= $downloadbox. $_SERVER['HTTP_HOST'].$dirwp."downloads/".$nomcodi;
			$downloadbox= $downloadbox. "&set=".$_POST['formatos'];
			$downloadbox= $downloadbox. '" TARGET="_blank"><img src="wp-content/plugins/blog-download/graphics/oficina_pdf.png"></a>';
		}
		////////////////////DOC
		if($_POST['formatos'] == "doc"){ 
			$downloadbox= $downloadbox. '<a href="http://zencherry.com/elgg/index.php?dir=';
			$downloadbox= $downloadbox. $_SERVER['HTTP_HOST'].$dirwp."downloads/".$nomcodi;
			$downloadbox= $downloadbox. "&set=".$_POST['formatos'];
			$downloadbox= $downloadbox. '" TARGET="_blank"><img src="wp-content/plugins/blog-download/graphics/oficina_docdown.png"></a>';
		}
		////////////////////////HTML
		if($_POST['formatos'] == "html"){ 
			$downloadbox= $downloadbox. '<a href="http://zencherry.com/elgg/index.php?dir=';
			$downloadbox= $downloadbox. $_SERVER['HTTP_HOST'].$dirwp."downloads/".$nomcodi;
			$downloadbox= $downloadbox. "&set=".$_POST['formatos'];
			$downloadbox= $downloadbox. '" TARGET="_blank"><img src="wp-content/plugins/blog-download/graphics/html_file.png"></a>';
		}
	 }}
    return  $content . $downloadbox; 
}
add_filter('the_content', 'adddownloadblog'); 
