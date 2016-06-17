<?php
/**
 * Là lớp giúp chúng ta xử lí các biến toàn cục tương tác với cấu hình
 * @method string|false get( string $path)
*/
	class Config {
	   /**
        * Lớp Config chứa hàm get
        *@param string $path Tên của các đường dẫn cần xử lý lấy đường dẫn chuẩn
        *@return string|false Nếu không tồn tại đường dẫn cần get thì sẽ trả về giá trị false còn ngược lại trả về giá trị của biến config
        */
		public static function get($path = null) {
			if ($path) {
				$config = $GLOBALS['config'];
				$path	= explode('/', $path);

				foreach ($path as $bit) {
					if (isset($config[$bit])) {
						$config = $config[$bit];
					}
				}

				return $config;
			}
			
			return false;
		}
	}
?>