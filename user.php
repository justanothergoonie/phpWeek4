<?php
class User
{
	private $db_host = 'localhost';
	private $db_user = 'web';
	private $db_pass = 'web';
	private $db_name = 'example';
	private $error = '';
	private $dsn = '';	
	// private $current_user = $_SESSION['user'];
	private function get_dbh(){
		return new PDO($this->dsn, $this->db_user, $this->db_pass);
	}

	function __construct(){
		$this->dsn = "mysql:host={$this->db_host};dbname={$this->db_name};";
		
	}

	function handleAction($action, $vars){
		$action_function = "do_{$action}";
		if (method_exists($this, $action_function)) {
			$this->$action_function($vars);
		}
	}
	public function errorMessage(){
		return $this->error;
	}

	// function  user_check(){
	// 	$current_user = $_SESSION['user'];
	// 	$dbh = $this->get_dbh();
	// 	$sql_user_check =
	// 		'SELECT username FROM users WHERE username = :user';
	// 	$id_check_stmt = $dbh->prepare($sql_user_check);
	// 	$id_check_stmt->execute(['user'=> $current_user['username']]);
	// 	$user_id_relates = $id_check_stmt->fetch();
	// }
	function do_login($vars){
		try {
			$try_user = $vars['username'];
			$try_pass = $vars['password'];

			if (!empty($try_user) && !empty($try_pass)) {
				$dbh = $this->get_dbh();
				$sql =
					'SELECT id,username FROM users WHERE username = :user AND password = :pass';
				$statement = $dbh->prepare($sql);
				$statement->execute(['user' => $try_user, 'pass' => $try_pass]);

				$user = $statement->fetch();
				// print_r($user);
				if (empty($user)) {
					$this->error = 'Invalid Credentials';
				} else {
					$_SESSION['user'] = $user;
					// print_r($user['username']);
					$_SESSION['is_logged_in'] = true;
				}
				$dbh = null;
			} else {
				$this->error = '';
			}
		} catch (PDOException $e) {
			print_r('uh-oh!' . $e->getMessage() . '<br />');
		}
	}
	// function do_update_account($vars){
	// 	try{
	// 		$current_user = $_SESSION['user'];
	// 		$try_update_username = $vars['username'];
	// 		$try_update_password = $vars['password'];
	// 		$try_confirm_update_password = $vars['confirm_password'];

	// 		if ($try_update_password !== $try_confirm_update_password) {

	// 			$this->error = 'pass dont match yo';
	// 		}
	// 		if (
	// 			!empty($try_update_username) &&
	// 			!empty($try_update_password) &&
	// 			!empty($try_confirm_update_password)
	// 		) {
	// 			$dbh = $this->get_dbh();
	// 			$sql_id_check =
	// 			'SELECT id FROM users WHERE username = :user';
	// 			$id_check_stmt = $dbh->prepare($sql_id_check);
	// 			$id_check_stmt->execute(['user' => $current_user['username']]);
	// 			$user_id_relates = $id_check_stmt->fetch();
	// 			if ($user_id_relates) {
	// 				$sql_update_user = 'UPDATE users SET username = :user, password = :pass WHERE id = :existing_user_id';
	// 				$update_stmt = $dbh->prepare($sql_update_user);
	// 				$update_stmt->execute(['user' => $try_update_username, 'pass' => $try_update_password, 'existing_user_id' => $user_id_relates['id']]);
	// 			}  else {
	// 				$this->error='not working';
	// 			}
	// 		}
	// 	} catch (PDOException $e) {
	// 		print_r('uh-oh!' . $e->getMessage() . '<br />');
	// 	}
	// }
	function do_update_account($vars)
	{
		try {
			$current_user = $_SESSION['user'];
			$try_update_username = $vars['username'];
			$try_update_password = $vars['password'];
			$try_confirm_update_password = $vars['confirm_password'];

			if ($try_update_password !== $try_confirm_update_password) {

				$this->error = 'pass dont match yo';
			}
			if (
				!empty($try_update_username) &&
				!empty($try_update_password) &&
				!empty($try_confirm_update_password)
			) {
				$dbh = $this->get_dbh();
				$sql_id_check =
					'SELECT id FROM users WHERE username = :user';
				$id_check_stmt = $dbh->prepare($sql_id_check);
				$id_check_stmt->execute(['user' => $current_user['username']]);
				$user_id_relates = $id_check_stmt->fetch();
				if ($user_id_relates) {
					$dbh = $this->get_dbh();
					$sql_user_check = 'SELECT * FROM  users WHERE username=?';
					$stmt = $dbh->prepare($sql_user_check);
					$stmt->execute([$try_update_username]);
					$user_name_taken = $stmt->fetch();
					if ($user_name_taken) {
						$this->error = 'name already taken';
					} else {
					$sql_update_user = 'UPDATE users SET username = :user, password = :pass WHERE id = :existing_user_id';
					$update_stmt = $dbh->prepare($sql_update_user);
					$update_stmt->execute(['user' => $try_update_username, 'pass' => $try_update_password, 'existing_user_id' => $user_id_relates['id']]);
					}
				} 
			}
		} catch (PDOException $e) {
			print_r('uh-oh!' . $e->getMessage() . '<br />');
		}
	}

	function do_signup($vars){
		try {
			$try_new_username = $vars['username'];
			$try_new_password = $vars['password'];
			$try_confirm_new_password = $vars['confirm_password'];
			if ($try_new_password !== $try_confirm_new_password) {
				$this->error = 'pass dont match';
			}
			if (
				!empty($try_new_username) &&
				!empty($try_new_password) &&
				!empty($try_confirm_new_password)
			) {
				$dbh = $this->get_dbh();
				$sql_user_check = 'SELECT * FROM  users WHERE username=?';
				$stmt = $dbh->prepare($sql_user_check);
				$stmt->execute([$try_new_username]);
				$user_name_taken = $stmt->fetch();
				if ($user_name_taken) {
					$this->error = 'name already taken';
				} else {
					$sql_add_user =
						'INSERT INTO users (username, password) VALUES(:username, :password)';
					$add_stmt = $dbh->prepare($sql_add_user);
					$add_stmt->bindParam(':username', $try_new_username);
					$add_stmt->bindParam(':password', $try_new_password);
					$add_stmt->execute();
				}
			}
		} catch (PDOException $e) {
			print_r('uh-oh!' . $e->getMessage() . '<br />');
		}
	}
	function do_confirm_delete(){
		$current_user = $_SESSION['user'];
		$dbh = $this->get_dbh();
		$sql_delete_user = 'DELETE FROM users WHERE username = :user';
		$delete_stmt = $dbh->prepare($sql_delete_user);
		$delete_stmt->execute(['user' => $current_user['username']]);
	}
	public function doSomething()
	{
		echo 'howdy, i am doing something';
		echo $this->dsn;
	}

	private function doAnotherSomething()
	{
	}
}