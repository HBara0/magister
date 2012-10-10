<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright � 2009 Orkila International Offshore, All Rights Reserved
 *
 * Templates Class
 * $id: Templates_class.php
 * Created:		@zaher.reda
 * Last Update: @zaher.reda 	Feb 20, 2009 | 04:07 PM
 */
class Templates {
	protected $cache = array();

	public function get($title, $raw=1) {
		global $db;

		//if(!isset($this->cache[$title]))
		//{
			if (TEMPLATES_SYSTEM=='DB') {
				$template_content = $db->fetch_field($db->query("SELECT template FROM ".Tprefix."templates WHERE title='".$db->escape_string($title)."'"), 'template');
			}
			else {
				$base_dir=substr(ROOT,0,strlen($base_dir)-1).'\templates';
				$templatefile = $base_dir.'\\'.$title;
				$handle = fopen($templatefile, 'r');
				$template_content = fread($handle, filesize($templatefile));
				fclose($handle);
			}

			$this->cache[$title] = $template_content;
		//}
		$template = "<!-- start: {$title} -->\n".$this->cache[$title]."\n<!-- end: {$title} -->";

		if($raw == 1) {
			$template = str_replace("\\'", "'", $db->escape_string($template));
		}

		return $template;
	}
}
?>