<?php
session_start();

$db_host = 'localhost';
$db_user = 'web';
$db_pass = 'web';
$db_name = 'example';

if ($_REQUEST['logout']) {
	session_destroy();
	header('Location: index.php');
	die();
}
$error = '';

$dsn = "mysql:host=$db_host;dbname=$db_name;";

try {
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$try_user = $_REQUEST['username'];
		$try_pass = $_REQUEST['password'];
		// $try_new_username = $_REQUEST['new_username'];
		// if ($_REQUEST['new_password'] == $_REQUEST['confirm_new_password']) {
		// 	$try_new_password = $_REQUEST['new_password'];
		// 	$try_confirm_new_password = $_REQUEST['confirm_new_password'];
		// } else {
		// 	$error = 'passwords dont match';
		// }
		if (!empty($try_user) && !empty($try_pass)) {
			$dbh = new PDO($dsn, $db_user, $db_pass);
			// foreach ($dbh->query('SELECT username from users') as $user_list) {
			// 	print_r($user_list);
			// }
			$sql =
				'SELECT id,username FROM users WHERE username = :user AND password = :pass';
			$statement = $dbh->prepare($sql);
			$statement->execute(['user' => $try_user, 'pass' => $try_pass]);
			$user = $statement->fetch();
			if (empty($user)) {
				$error = 'Invalid Credentials';
			} else {
				$_SESSION['user'] = $user;
				$_SESSION['is_logged_in'] = true;
			}
			$dbh = null;
		} else {
			$error = '';
		}
	}
} catch (PDOException $e) {
	print_r('uh-oh!' . $e->getMessage() . '<br />');
}

try {
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$try_new_username = $_REQUEST['new_username'];
		$try_new_password = $_REQUEST['new_pass'];
		$try_confirm_new_password = $_REQUEST['confirm_new_pass'];
		if ($try_new_password !== $try_confirm_new_password) {
			$error = 'pass dont match';
		}
		if (
			!empty($try_new_username) &&
			!empty($try_new_password) &&
			!empty($try_confirm_new_password)
		) {
			$dbh = new PDO($dsn, $db_user, $db_pass);
			$sql_user_check = 'SELECT * FROM  users WHERE username=?';
			$stmt = $dbh->prepare($sql_user_check);
			$stmt->execute([$try_new_username]);
			$user_name_taken = $stmt->fetch();
			if ($user_name_taken) {
				$error = 'name already taken';
			} else {
				$sql_add_user =
					'INSERT INTO users (username, password) VALUES(:username, :password)';
				$add_stmt = $dbh->prepare($sql_add_user);
				$add_stmt->bindParam(':username', $try_new_username);
				$add_stmt->bindParam(':password', $try_new_password);
				$add_stmt->execute();
			}
		}
	}
} catch (PDOException $e) {
	print_r('uh-oh!' . $e->getMessage() . '<br />');
}

$is_logged_in = $_SESSION['is_logged_in'];
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>Add a Title</title>
    <meta name="description" content="add a description" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="stylesheet" href="dist/css/main.css" />
</head>

<body>
    <?php if ($is_logged_in): ?>
    <p>Welcome, <?php echo $user['username']; ?>!</p>
    <a href="?logout=true">Log out</a>
    <?php else: ?>

    <form method="post">
        <h2>Existing Users</h2>
        <label>Username</label>
        <input type="text" name="username" />
        <br />

        <label>Password</label>
        <input type="password" name="password" />
        <br />

        <input type="submit" value="Login" />
        <span><?php echo $error; ?></span>

    </form>
    <?php endif; ?>


    <form method="post">
        <h2>Create an Account</h2>
        <br />

        <label>Create Username</label>
        <input type="text" name="new_username">
        <br />

        <label>Password</label>
        <input type="new_password" name="new_pass">
        <br />

        <label>Confirm Password</label>
        <input type="confirm_new_password" name="confirm_new_pass">
        <span><?php echo $error; ?></span>
        <br />

        <input type="submit" value="Create">
    </form>


    <script src="dist/js/main.js"></script>
</body>

</html>