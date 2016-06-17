<?php
	/**
	* Cookie là lớp chứa các hàm xử lí trên Cookie
	* @method bool exist(string $name) 
	* @method string get(string $name)
	* @method bool put (string $name, string $value, number $expiry)
	* @method void delete( string $name)
	*/
	class Cookie {

		/**
		* Hàm kiểm tra sự tồn tại của 1 cookie theo biến tên được nhập vào
		* @param string $name Tên của cookie cần kiểm tra có tồn tại hay không
		* @return bool True: tồn tại cookie False: không tồn tại cookie 
		*/
		public static function exists($name) {
			return (isset($_COOKIE[$name])) ? true : false;
		}

		/**
		* Hàm để lấy 1 cookie theo tên được truyền vào
		*@param string $name Tên của cookie muốn lấy
		*@return S_COOKIE[$name]
		*/
		public static function get($name) {
			return $_COOKIE[$name];
		}

		/**
		* Hàm để set giá trị các biến cho 1 cookie
		* @param string $name Tên của cookie
		* @param string $value Giá trị của biến cookie
		* @param number $expiry Thời gian tồn tại được thiết lập cho cookie
		* @return bool True: thiết lập cookie thành công False: Thiết lập cookie ko thành công
		*/
		public static function put($name, $value, $expiry) {
			if (setcookie($name, $value, time()+$expiry, '/')) {
				return true;
			}
			return false;
		}
		
		/**
		* Hàm để xóa 1 cookie theo tên
		* @param string $name Tên của cookie muốn delete
		* @return void Trả lại trạng thái sau khi đã xóa cookie
		*/
		public static function delete($name) {
			self::put($name, '', time()-1);
		}
	}
?>