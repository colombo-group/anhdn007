<?php
	/**
	* Lớp thực hiện xử lí dữ liệu nhập vào từ form do người dùng tương tác với hệ thống
	*/
	class Input {
		/**
		* Đây là hàm kiểm tra xem có tương tác từ người dùng không
		* @param string $type Lưu tên của tương tác có 2 giá trị post hoặc get, mặc định là $post
		* @return bool True: nếu có dữ liệu được POST hoặc GET False: Nếu cả 2 thao tác đều không có dữ liệu
		*/
		public static function exists($type = 'post') {
			switch ($type) {
				case 'post':
					return (!empty($_POST)) ? true : false;
					break;
				case 'get':
					return (!empty($_GET)) ? true : false;
					break;
				default:
					return false;
					break;
			}
		}
		/**
		* Xử lí với dữ liệu được nhận từ form
		* Kiểm tra xem nếu là POST hay GET sẽ thực hiện return kết quả phù hợp
		*/
		public static function get($item) {
			if (isset($_POST[$item])) {
				return $_POST[$item];
			} else if (isset($_GET[$item])) {
				return $_GET[$item];
			}
			return '';
		}
	}
?>