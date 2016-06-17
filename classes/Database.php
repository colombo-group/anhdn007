<?php
	/**
	* Đây là lớp chứa các hàm xử lí kết nối tới cơ sở dữ liệu
	*/
	class Database {
		/**
		* @param string|null $_instance Lưu đối tượng static CSDL 
		*/
		private static $_instance = null;
		private $_pdo,
				$_query,
				$_error = false,
				$_results,
				$_count = 0;

		/**
		* Đây là hàm khởi tạo cơ sở dữ liệu qua host, dbname,username
		* Phương thức khởi tạo private để không thể bị thay đổi tại các class khác
		*/

		private function __construct() {


			try {
				$this->_pdo = new PDO('mysql:host='.Config::get('mysql/host').';dbname='.Config::get('mysql/db'),Config::get('mysql/username'),Config::get('mysql/password'));
			} catch (PDOException $e) {
				die($e->getMessage());
			}
		}

		/**
		* lấy đối tượng kiểu static để mỗi lần đảm bảo chỉ có 1 kết nối đến CSDL
		* @return $_instance 
		*/
		public static function getInstance() {
			if (!isset(self::$_instance)) {
				self::$_instance = new Database();
			}
			return self::$_instance;
		}
		/**
		* Hàm thực hiện các câu truy vấn sql
		* @param string $sql Đây là câu lệnh truy vấn sql được truyền vào
		* @param array $params Đây là các tham số được truyền 
		*/
		public function query($sql, $params = array()) {
			$this->_error = false;
			if ($this->_query = $this->_pdo->prepare($sql)) {
				$x = 1;
				if (count($params)) {
					foreach ($params as $param) {
						$this->_query->bindValue($x, $param);
						$x++;
					}
				}

				if ($this->_query->execute()) {
					$this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
					$this->_count	= $this->_query->rowCount();
				} else {
					$this->_error = true;
				}
			}

			return $this;
		}
		/**
		* Hàm thực hiện các câu lệnh trong truy vấn
		* @param string $action Biến lưu các thao tác truy vấn 
		* @param string $table Các bảng dữ liệu cần thao tác
		* @param string $where Mảng các điều kiện giới hạn câu truy vấn
		*/

		public function action($action, $table, $where = array()) {
			if (count($where) === 3) {	//Allow for no where
				$operators = array('=','>','<','>=','<=','<>');

				$field		= $where[0];
				$operator	= $where[1];
				$value		= $where[2];

				if (in_array($operator, $operators)) {
					$sql = "{$action} FROM {$table} WHERE ${field} {$operator} ?";
					if (!$this->query($sql, array($value))->error()) {
						return $this;
					}
				}
			}
			return false;
		}

		/**
		* Đây là hàm thực hiện câu truy vấn select ra CSDL
		* @param string $table Lưu các bảng cần tương tác để truy vấn CSDL
		* @param string $where Điều kiện đầu ra của câu truy vấn
		*/

		public function get($table, $where) {
			return $this->action('SELECT *', $table, $where); //ToDo: Allow for specific SELECT (SELECT username)
		}

		/**
		* Đây là hàm thực hiện câu truy vấn delete CSDL, xóa dữ liệu từ CSDL
		* @param string $table Lưu các bảng cần tương tác để truy vấn CSDL
		* @param string $where Điều kiện đầu ra của câu truy vấn
		*/

		public function delete($table, $where) {
			return $this->action('DELETE', $table, $where);
		}

		/**
		* Đây là hàm thực hiện câu truy vấn insert CSDL
		* @param string $table Lưu các bảng cần tương tác để truy vấn CSDL
		* @param array $fields Lưu các trường dữ liệu cần thêm 
		* @return false|true True: nếu thêm dữ liệu thành công False: nếu không thêm được CSDL theo yêu cầu
		*/

		public function insert($table, $fields = array()) {
			if (count($fields)) {
				$keys 	= array_keys($fields);
				$values = null;
				$x 		= 1;

				foreach ($fields as $field) {
					$values .= '?';
					if ($x<count($fields)) {
						$values .= ', ';
					}
					$x++;
				}

				$sql = "INSERT INTO {$table} (`".implode('`,`', $keys)."`) VALUES({$values})";

				if (!$this->query($sql, $fields)->error()) {
					return true;
				}
			}
			return false;
		}
		/**
		* Đây là hàm update lại CSDL 
		* @param string $table Lưu các bảng CSDL cần thao tác
		* @param int $id Địa chỉ của cột cần thao tác
		* @param array $fields Các trường của dữ liệu ta muốn update
		*/

		public function update($table, $id, $fields = array()) {
			$set 	= '';
			$x		= 1;

			foreach ($fields as $name => $value) {
				$set .= "{$name} = ?";
				if ($x<count($fields)) {
					$set .= ', ';
				}
				$x++;
			}

			$sql = "UPDATE {$table} SET {$set} WHERE id = {$id}";
			
			if (!$this->query($sql, $fields)->error()) {
				return true;
			}
			return false;
		}

		/**
		* Hàm trả vể các bản ghi thỏa mãn yêu cầu truy vấn
		*/
		public function results() {
			return $this->_results;
		}
		/**
		* Hàm trả về bản ghi đầu tiên thỏa mãn câu truy vấn
		*/

		public function first() {
			return $this->_results[0];
		}
		/**
		* Hàm trả về các thông báo lỗi
		*/

		public function error() {
			return $this->_error;
		}

		/**
		* Hàm thực hiện đếm số lượng bản ghi
		*/
		public function count() {
			return $this->_count;
		}
	}
?>