<?php
	/**
	* Lớp kiểm tra các giá trị đầu vào có hợp lệ hay không
	*/
	class Validate {
		/**
		* @param bool $_passed lưu trạng thái có hợp lệ hay không
		* @param array $_errors Danh sách các lỗi
		* @param string $_db Dữ liệu
		*/
		private $_passed = false,
				$_errors = array(),
				$_db = null;
		/**
		* Hàm khởi tạo lớp
		*/
		public function __construct() {
			$this->_db = Database::getInstance();
		}

		/**
		* Hàm thực hiện kiểm tra giá trị đầu vào
		* @param string $source Dữ liệu cần được kiểm tra
		* @param array $items Danh sách các luật cần được kiểm tra 
		*/
		public function check($source, $items = array()) {
			foreach ($items as $item => $rules) {
				foreach ($rules as $rule => $rule_value) {
					$value 	= trim($source[$item]);
					$item 	= escape($item);
					
					if ($rule === 'required' && empty($value)) {
						$this->addError("{$item} is required");	//ToDo: Pick up 'name' value
					} else if (!empty($value)) {
						switch ($rule) {
							case 'min':
								if (strlen($value) < $rule_value) {
									$this->addError("{item} must be a minimum of {$rule_value} characters");
								}
								break;
							case 'max':
								if (strlen($value) > $rule_value) {
									$this->addError("{item} must be no longer than {$rule_value} characters");
								}
								break;
							case 'matches':
								if ($value != $source[$rule_value]) {
									$this->addError("{$rule_value} must match {$item}");
								}
								break;
							case 'unique':
								$check = $this->_db->get($rule_value,array($item, '=' , $value));
								if ($check->count()) {
									$this->addError("{$item} already exists");
								}
								break;
						}
					}
				}
			}

			if (empty($this->_errors)) {
				$this->_passed = true;
			}

			return $this;
		}

		/**
		* Hàm thực hiện thêm 1 lỗi mới vào danh sách lỗi đã có
		* @return array danh sách lỗi sau khi đã được bổ sung
		*/
		private function addError($error) {
			$this->_errors[] = $error;
		}

		/**
		* Hàm thực hiện lấy danh sách các lỗi vi phạm
		* @return string Tên lỗi
		*/
		public function errors() {
			return $this->_errors;
		}

		/**
		* Hàm thực hiện trạng thái đầu ra hợp lệ của chuỗi được kiểm tra
		* @return bool
		*/
		public function passed() {
			return $this->_passed;
		}
	}
?>