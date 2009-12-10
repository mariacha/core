<?php
/*
+------------------------------------------------------------------------------+
| LiteCommerce                                                                 |
| Copyright (c) 2003-2007 Creative Development <info@creativedevelopment.biz>  |
| All rights reserved.                                                         |
+------------------------------------------------------------------------------+
| PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE  "COPYRIGHT" |
| FILE PROVIDED WITH THIS DISTRIBUTION.  THE AGREEMENT TEXT  IS ALSO AVAILABLE |
| AT THE FOLLOWING URLs:                                                       |
|                                                                              |
| FOR LITECOMMERCE                                                             |
| http://www.litecommerce.com/software_license_agreement.html                  |
|                                                                              |
| FOR LITECOMMERCE ASP EDITION                                                 |
| http://www.litecommerce.com/software_license_agreement_asp.html              |
|                                                                              |
| THIS  AGREEMENT EXPRESSES THE TERMS AND CONDITIONS ON WHICH YOU MAY USE THIS |
| SOFTWARE PROGRAM AND ASSOCIATED DOCUMENTATION THAT CREATIVE DEVELOPMENT, LLC |
| REGISTERED IN ULYANOVSK, RUSSIAN FEDERATION (hereinafter referred to as "THE |
| AUTHOR")  IS  FURNISHING  OR MAKING AVAILABLE TO  YOU  WITH  THIS  AGREEMENT |
| (COLLECTIVELY,  THE "SOFTWARE"). PLEASE REVIEW THE TERMS AND  CONDITIONS  OF |
| THIS LICENSE AGREEMENT CAREFULLY BEFORE INSTALLING OR USING THE SOFTWARE. BY |
| INSTALLING,  COPYING OR OTHERWISE USING THE SOFTWARE, YOU AND  YOUR  COMPANY |
| (COLLECTIVELY,  "YOU")  ARE ACCEPTING AND AGREEING  TO  THE  TERMS  OF  THIS |
| LICENSE AGREEMENT. IF YOU ARE NOT WILLING TO BE BOUND BY THIS AGREEMENT,  DO |
| NOT  INSTALL  OR USE THE SOFTWARE. VARIOUS COPYRIGHTS AND OTHER INTELLECTUAL |
| PROPERTY  RIGHTS PROTECT THE SOFTWARE. THIS AGREEMENT IS A LICENSE AGREEMENT |
| THAT  GIVES YOU LIMITED RIGHTS TO USE THE SOFTWARE AND NOT AN AGREEMENT  FOR |
| SALE  OR  FOR TRANSFER OF TITLE. THE AUTHOR RETAINS ALL RIGHTS NOT EXPRESSLY |
| GRANTED  BY  THIS AGREEMENT.                                                 |
|                                                                              |
| The Initial Developer of the Original Code is Creative Development LLC       |
| Portions created by Creative Development LLC are Copyright (C) 2003 Creative |
| Development LLC. All Rights Reserved.                                        |
+------------------------------------------------------------------------------+
*/

/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */

/**
* Class description.
*
* @package Dialog
* @access public
* @version $Id: change_skin.php,v 1.3 2007/05/21 11:53:27 osipov Exp $
*
*/

class Admin_Dialog_Change_Skin extends Admin_Dialog
{
	var $_currentSkin = null;
	var $currentSkinName = null;
	var $_templatesRepository = "skins_original";
	var $_schemasRepository = "schemas";
	var $_skins = null;
	var $_templatesDirectory = "skins";
	var $_modulesPath = "classes/modules";

	function getDirectoriesToCreate()
	{
		$dirs = array("var", $this->_templatesDirectory, "catalog","images");
		$dirs[] = "var/backup";
		$dirs[] = "var/log";
		$dirs[] = "var/run";
		$dirs[] = "var/tmp";

		return $dirs;
	}

	function getCurrentSkin()
	{
		if (isset($this->_currentSkin)) {
			return $this->_currentSkin;
		}
		
        $this->currentSkinName = $this->config->get("Skin.skin");
        $this->_currentSkin = str_replace("_", " ", $this->currentSkinName);

        return $this->_currentSkin;
	}

	function isDisplayWarning()
	{
		$skins = array("3-columns_classic", "3-columns_modern", "2-columns_classic", "2-columns_modern");
		if (!in_array($this->config->get("Skin.skin"), $skins)) {
			if (!array_key_exists($this->config->get("Skin.skin"), $this->get("skins"))) {
				return true;
			}
		}

		return false;
	}

	function getSchemasList()
	{
		$node["name"] = "Standard";
		$node["path"] = $this->_schemasRepository;
		$list[] = $node;

		return $list;
	}

	function getSchemasRepository()
	{
		return $this->_schemasRepository;
	}

	function getSkins()
	{
		if (isset($this->_skins)) {
			return $this->_skins;
		}
	
		$this->_skins = array();
        foreach ($this->get("schemasList") as $schema) {
	        if ($dir = @opendir($schema["path"] . "/templates")) {
				$files = array();
    	    	$orig_files = array();
        	    while (($file = readdir($dir)) !== false) {
        			if (!($file == "." || $file == "..")) {
        				$orig_files[] = $file;
	        		}	
    	    	}
        	    closedir($dir);

	        	asort($orig_files);
    	    	$reverse_sorting = false;
        		$files = array();
        		$preferential = array();
	        	foreach($orig_files as $key => $file) {
    	    		if (strpos($file, "_modern") !== false) {
        				$preferential[] = $file;
        				unset($orig_files[$key]);
	        		}
    	    	}
        		if (!$reverse_sorting) {
            		foreach($preferential as $file) {
	            		$files[$file] = array("name" => $file);
    	        	}
        	    }
        		foreach($orig_files as $file) {
        			$files[$file] = array("name" => $file);
	        	}
    	    	if ($reverse_sorting) {
        	    	foreach($preferential as $file) {
            			$files[$file] = array("name" => $file);
            		}
	            }
    			foreach($files as $key => $value) {
	    			$preview = $schema["path"] . "/templates/".$value["name"]."/preview.gif";
    				if (is_readable($preview)) {
    					$files[$key]["preview"] = $preview;
    				}
        			$files[$key]["name"] = str_replace("_", " ", $value["name"]);
	    		}

				$this->_skins = array_merge($this->_skins, $files);
	        }
		}

        return $this->_skins;
	}

	function createDirs($dirs)
	{
		$status = true;

		foreach ($dirs as $val) {
			echo "Creating directory: [$val] ... ";

			if (!file_exists($val)) {
				$res = @mkdir($val, 0777);
				$status &= $res;

				echo $this->showStatus($res);
			 } else {
			 	echo "[Already exists]";
			 }

			echo "<BR>\n"; flush();
		}

		return $status;
	}

	function checkBeforeChange()
	{
		$this->checkFiles($this->_templatesRepository, "", $this->_templatesDirectory);
		$log = $this->checkFiles($this->get("schemasRepository")."/templates/".$this->layout, "", $this->_templatesDirectory);

		foreach ($log as $k=>$v)
			$log[$k] = array_unique($log[$k]);

		if ( count($log["write"]) > 0 ) {
			echo "<font color='red'><b>The following files have insufficient write permissions and cannot be overwritten:</b></font><br>";
			foreach ($log["write"] as $v) {
				echo "<font color='black'>$v</font><BR>";
			}
			echo "<br>";
		}

		if ( count($log["read"]) > 0 ) {
			echo "<font color='red'><b>The following files have insufficient read permissions and cannot be read:</b></font><br>";
			foreach ($log["read"] as $v) {
				echo "<font color='black'>$v</font><BR>";
			}
			echo "<br>";
		}

		if ( count($log["read"]) > 0 || count($log["write"]) > 0 )
			return false;

		return true;
	}

	function checkFiles($source_dir, $parent_dir, $destination_dir)
	{
		static $log = array("read"=>array(), "write"=>array());

		if ( !$handle = @opendir($source_dir) ) {
			echo $this->showStatus(false)."<BR>\n";
			return false;
		}

		while ( ($file = readdir($handle)) !== false ) {
			if ( is_file($source_dir."/".$file) ) {
				if ( !is_readable("$source_dir/$file") ) {
					$log["read"][] = "$source_dir$parent_dir/$file";
				}
				if (file_exists("$destination_dir$parent_dir/$file") && !is_writeable("$destination_dir$parent_dir/$file") ) {
					$log["write"][] = "$destination_dir$parent_dir/$file";
				}
			} else if ( is_dir($source_dir."/".$file) && $file != "." && $file != ".." ) {
				if( !file_exists("$destination_dir$parent_dir/$file") ) {
					if ( !is_writeable("$destination_dir$parent_dir") )
						$log["write"][] = "$destination_dir$parent_dir";
						continue;
				} else {
					if (file_exists("$destination_dir$parent_dir/$file") && !is_writeable("$destination_dir$parent_dir/$file") ) {
						$log["write"][] = "$destination_dir$parent_dir/$file";
						continue;
					}
				}

				$this->checkFiles($source_dir."/".$file, $parent_dir."/".$file, $destination_dir);
			}
		}

		closedir($handle);

		return $log;
	}

	function copyFiles($source_dir, $parent_dir, $destination_dir)
	{
		$status = true;

		if ( !$handle = @opendir($source_dir) ) {
			echo $this->showStatus(false)."<BR>\n";
			return false;
		}
 
		while ( ($file = readdir($handle)) !== false ) {
			if ( is_file($source_dir."/".$file) ) {
				if ( !@copy("$source_dir/$file", "$destination_dir$parent_dir/$file") ) {
					echo "Copying $source_dir$parent_dir/$file to $destination_dir$parent_dir/$file ... ".$this->showStatus(false)."<BR>\n";
					$status &= false;
				}

				flush();

			} else if ( is_dir($source_dir."/".$file) && $file != "." && $file != ".." ) {
				echo "Creating directory $destination_dir$parent_dir/$file ... ";

				if( !file_exists("$destination_dir$parent_dir/$file") ) {
					if( !@mkdir("$destination_dir$parent_dir/$file", 0777) ) {
						echo $this->showStatus(false);
						$status &= false;
					} else {
						echo $this->showStatus(true);
					}
				} else {
					echo "[Already exists]";
				}

				echo "<BR>\n"; flush();

				$status &= $this->copyFiles($source_dir."/".$file, $parent_dir."/".$file, $destination_dir);
			}
		}

		closedir($handle);

		return $status;
	}

	function updateModulesSkins()
	{
		$module = func_new("Module");
		$result = $module->iterate();
		while ($module->next($result)) {
			$name = $module->get("name");
			if (file_exists("./".$this->_modulesPath."/".$name."/install.php")) {
				echo "Changing some skins to work with " .$name. " module correctly...<br>";
				@include_once("./".$this->_modulesPath."/".$name."/install.php");
			}
		}
	}

    function fatalError($message)
    {
?>
<P>
<B><FONT color=red>Fatal error: <b><?php echo $message; ?></b>.<BR></FONT></B>
This unexpected error has canceled the installation.<BR>
To install the selected skin, please correct the problem and start the installation again.
</P>
<?php
    }

	function warningMsg($msg)
	{
?>
<p>
<b><font color="red">Warning: <?php echo $msg; ?></font></b>
<?php
	}

	function showStatus($var)
	{
		return ($var ? "<FONT color=green>[OK]</FONT>" : "<FONT color=red>[FAILED]</FONT>");
	}

	function action_update()
	{
		$this->set("silent", true);

		$this->startDump();
		echo "<H1>Installing skin: " . $this->layout . "</H1>";

		$ck_res = 1;

		if ( $this->ignore_errors != "yes" ) {
			if ( !$this->CheckBeforeChange() ) {
				echo '<font color="red"><b>Note: The files listed above do not have sufficient write permissions.</b></font><br> For further details on changing file permissions, run "man chmod" command in your UNIX system or see your SSH/FTP/Shell client reference manual.<br>';
				echo '<p><a href="'.$this->get("url").'"><u><b>Return to admin zone</b></u></a>';
				echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="admin.php?target=change_skin&action=update&layout='.$this->layout.'&ignore_errors=yes"><u>Continue anyway</u></a>';
				func_refresh_end();
				exit();
			}
		}

    	echo "<BR><B>Creating directories...</B><BR>\n";

    	$ck_res &= $this->createDirs($this->get("directoriesToCreate"));

        $teDialog =& func_new("Admin_Dialog_template_editor");
        $teDialog->getExtraPages();

    	echo "<BR><B>Copying templates...</B><BR>\n";

    	$ck_res &= $this->copyFiles($this->_templatesRepository, "", $this->_templatesDirectory);
     	
    	echo "<BR><B>Installing layout skin...</B><BR>\n";
        // switch templates_repository to layout folder
    	$ck_res &= $this->copyFiles($this->get("schemasRepository")."/templates/".$this->layout, "", $this->_templatesDirectory);

		echo "<br><br>";
 
 		echo "<div>";
    	$this->updateModulesSkins();
		echo "</div>";

        $teDialog->action_reupdate_pages();

		echo "<br><br><b>Cleanup cache...</b><br>";
		func_cleanup_cache("skins", true);

		$config =& func_new("Config");
		$config->createOption("Skin", "skin", $this->layout);
		
		echo "<br><b>Task completed.</b><br>";

    	if (!$ck_res) {
			$this->warningMsg("Files marked [FAILED] have not been re-writen.");
    	}
	}

	function getPageReturnUrl()
	{
		return array('<a href="'.$this->get("url").'"><u>Return to admin zone</u></a>');
	}
}

// WARNING :
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.
?>
