<?php
	/**
	* Lớp thực hiện thao tác sinh token
	*/
	class Token {
		/**
		* Hàm tạo token sử dụng băm md5
		*/
		public static function generate() {
			return Session::put(Config::get('session/tokenName'), md5(uniqid()));
		}
		/**
		* Hàm kiểm tra token 
		* @param string $token Giá trị token cần kiểm tra
		* @return bool
		*/

		public static function check($token) {
			$tokenName = Config::get('session/tokenName');

			if (Session::exists($tokenName) && $token === Session::get($tokenName)) {
				Session::delete($tokenName);
				return true;
			} else {
				return false;
			}
		}
	}
?>