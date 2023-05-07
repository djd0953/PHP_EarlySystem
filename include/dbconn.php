<?php
	$conn = mysqli_connect('localhost', 'userWooboWeb', 'wooboWeb!@', 'parking');
	include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbvo.php";

	class DBCONNECT
	{
		/**
		 * @brief DB 접속 정보
		 * @param $host 	: 주소
		 * @param $port 	: 포트
		 * @param $dbname 	: DB명
		 * @param $charset 	: 문자 인코딩형
		 * 
		 * @param $username : 유저 ID
		 * @param $password : 비밀번호
		 * @param $db_conn	: 주로 사용될 PDO Class 형태의 변수
		 */

		private $host = 'localhost';
		private $port = '3306';
		private $dbname = 'parking';
		private $charset = 'utf8';

		private $username = 'userWooboWeb';
		private $password = 'wooboWeb!@';

		public $db_conn;

		function __construct()
		{
			$this->db_conn = new PDO("mysql:host={$this->host}:{$this->port};dbname={$this->dbname};charset={$this->charset}", "{$this->username}", "{$this->password}");
			$this->db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
	}

	class FOCUS_DBCONNECT
	{
		/**
		 * @brief DB 접속 정보
		 * @param $host 	: 주소
		 * @param $port 	: 포트
		 * @param $dbname 	: DB명
		 * @param $charset 	: 문자 인코딩형
		 * 
		 * @param $username : 유저 ID
		 * @param $password : 비밀번호
		 * @param $db_conn	: 주로 사용될 PDO Class 형태의 변수
		 */

		private $host = 'localhost';
		private $port = '3306';
		private $dbname = 'focus';
		private $charset = 'utf8';

		private $username = 'userWooboWeb';
		private $password = 'wooboWeb!@';

		public $db_conn;

		function __construct()
		{
			$this->db_conn = new PDO("mysql:host={$this->host}:{$this->port};dbname={$this->dbname};charset={$this->charset}", "{$this->username}", "{$this->password}");
			$this->db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
	}
?>
