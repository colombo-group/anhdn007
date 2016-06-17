<?php
	/**
	* Hàm giúp chuyển hướng trang web
	*/
	class Redirect {
		/**
		* to là hàm chuyển hướng trang web đến location được truyền vào
		* param string $location Biến lưu vị trí đường dẫn muốn chuyển hướng đến 
		*/
		public static function to($location = null) {
			if ($location) {
				if (is_numeric($location)) {
					switch ($location) {
						case '404':
							header('HTTP/1.0 404 Not Found');
							include 'includes/errors/404.php';
							exit();
						break;
					}
				}
				
				header('Location: '.$location);
				exit();
			}
		}
	}
?>