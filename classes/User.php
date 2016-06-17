<?php
	/**
	* Lớp thực hiện các thao tác làm việc với đối tượng
	*/
	class User {
		private $_db,
				$_data,
				$_sessionName,
				$_cookieName,
				$_isLoggedIn;

		/**
		* Hàm khởi tạo cho user, tạo cookie, tạo session và tạo kết nối đến DB
		*/
		public function __construct($user = null) {
			$this->_db 			= Database::getInstance();
			$this->_sessionName = Config::get('session/sessionName');
			$this->_cookieName 	= Config::get('remember/cookieName');

			if (!$user) {
				if (Session::exists($this->_sessionName)) {
					$user = Session::get($this->_sessionName);

					if ($this->find($user)) {
						$this->_isLoggedIn = true;
					} else {
						self::logout();
					}
				}
			} else {
				$this->find($user);
			}
		}

		/**
		* Hàm thực hiện cập nhật các trường dữ liệu người dùng sau khi có login hoặc logout
		* @param array $fields Lưu các trường dữ liệu muốn thực hiện cập nhật
		* @param int $id Lưu id của người dùng cần cập nhật dữ liệu
		*/
		public function update($fields = array(), $id = null) {

			if (!$id && $this->isLoggedIn()) {
				$id = $this->data()->ID;
			}

			if (!$this->_db->update('users', $id, $fields)) {
				throw new Exception("There was a problem updating your details");
			}
		}

		/**
		* Hàm thực hiện tạo phiên làm việc mới cho user
		* @param array $fields danh sách cá trường dữ liệu cần để tạo ra user mới
		*/
		public function create($fields = array()) {
			if (!$this->_db->insert('users', $fields)) {
				throw new Exception("There was a problem creating your account");
			}
		}
		/**
		* THực hiện tìm kiếm user theo tên user nhập vào
		* @param string $user Nhập vào tên user cần tìm
		* @return bool True: nếu tìm thấy bản ghi đầu tiên có chứa tên user False: nếu không có bản ghi này trong CSDL
		*/

		public function find($user = null) {
			if ($user) {
				$fields = (is_numeric($user)) ? 'id' : 'username';	//Numbers in username issues
				$data 	= $this->_db->get('users', array($fields, '=', $user));

				if ($data->count()) {
					$this->_data = $data->first();
					return true;
				}
			}
			return false;
		}

		/**
		* Hàm thực hiện thao tác login cho 1 user 
		* @param string $username Tên user cần đăng nhập
		* @param password $password password của user cần đăng nhập
		* @param bool $remember ghi lại yêu cầu của người dùng có muốn lưu lại mật khẩu trên trang này hay không
		* @return bool True: nếu user đăng nhập thành công False: Nếu user đăng nhập xảy ra lỗi
		*/
		public function login($username = null, $password = null, $remember = false) {
			if (!$username && !$password && $this->exists()) {
				Session::put($this->_sessionName, $this->data()->ID);
			} else {
				$user = $this->find($username);
				if ($user) {
					if ($this->data()->password === Hash::make($password,$this->data()->salt)) {
						Session::put($this->_sessionName, $this->data()->ID);

						if ($remember) {
							$hash = Hash::unique();
							$hashCheck = $this->_db->get('usersSessions', array('userID','=',$this->data()->ID));

							if (!$hashCheck->count()) {
								$this->_db->insert('usersSessions', array(
									'userID' 	=> $this->data()->ID,
									'hash' 		=> $hash
								));
							} else {
								$hash = $hashCheck->first()->hash;
							}
							Cookie::put($this->_cookieName, $hash, Config::get('remember/cookieExpiry'));
						}

						return true;
					}
				}
			}
			return false;
		}
		/**
		* Hàm kiểm tra mã quyền của người dùng là admin hay khách hay thành viên,...
		* @param int $key Để lưu quyền của user
		* @return bool 
		*/
		public function hasPermission($key) {
			$group = $this->_db->get('groups', array('ID', '=', $this->data()->userGroup));
			if ($group->count()) {
				$permissions = json_decode($group->first()->permissions,true);

				if ($permissions[$key] == true) {
					return true;
				}
			}
			return false;
		}
		/**
		* Thực hiện kiểm tra 1 người dùng có tồn tại hay chưa
		*/
		public function exists() {
			return (!empty($this->_data)) ? true : false;
		}
		/**
		* Hàm thực hiện đăng xuất người dùng, hủy cả session và cookie của tiến trình đăng nhập
		*/
		public function logout() {
			$this->_db->delete('usersSessions', array('userID', '=', $this->data()->ID));
			Session::delete($this->_sessionName);
			Cookie::delete($this->_cookieName);
		}

		/**
		* Hàm thực hiện lấy dữ liệu trong DB của user
		*/
		public function data() {
			return $this->_data;
		}

		/**
		* Hàm kiểm tra trạng thái đăng nhập của user
		*/
		public function isLoggedIn() {
			return $this->_isLoggedIn;
		}
	}
?>