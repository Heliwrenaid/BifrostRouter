<?php
class SimpleCache {
	private static $file = '/opt/lampp/htdocs/BifrostRouter/cache/file';
	
	public static function get() {
		if (file_exists(self::$file)) {
			return unserialize(file_get_contents(self::$file));			
		} else {
			return null;
		}
	}
	
	public static function set($obj) {
		file_put_contents(self::$file, serialize($obj));
	}
}