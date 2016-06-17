<?php
	/**
	* Hàm thực hiện băm giá trị nhập vào để bảo đảm tính bảo mật của CSDL
	*/
	class Hash {
		/**
		* Hàm trả về giá trị của chuỗi sau khi thực hiện sha256
		* @param string $string Đầu vào là chuỗi cần mã hóa
		* @param string $salt giá trị chuỗi salt thêm vào để tăng tính bảo mật 
		*/
		public static function make($string, $salt = '') {
			return hash('sha256', $string.$salt);
		}
		
		public static function salt($length) {
			return mcrypt_create_iv($length);
		}

		public static function unique() {
			return self::make(uniqid());
		}
	}
?>