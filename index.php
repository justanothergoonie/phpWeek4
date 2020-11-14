<?php
session_start();

include 'user.php';
if ($_REQUEST['_action'] == 'logout') {
    session_destroy();
    header('Location: index.php');
    die();
}

$userManager = new User();
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_REQUEST['_action'])) {
    $userManager->handleAction($_REQUEST['_action'], $_REQUEST);
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

    <?php if ($is_logged_in) : ?>
    <em style="color: red;"><?php echo $userManager->errorMessage(); ?></em>
    <p>Welcome, <?php echo $user['username']; ?>!</p>
    <p>
        Actions:
        <a href="?_action=edit">Edit Account</a> |
        <a href="?_action=delete">Delete Account</a> |
        <a href="?_action=logout">Log out</a>
    </p>
    <?php switch ($_GET['_action']) {
            case 'edit':
                //show edit code


        ?>
    <h2>Edit Account</h2>
    <form method="post">
        <input type="hidden" name="_action" value="update_account">

        <label>Username</label>
        <input type="text" name="username" value="" placeholder="<?php echo $user['username']; ?>" />
        <br />

        <label>Password</label>
        <input type="password" name="password" />
        <br />
        <label>Confirm Password</label>
        <input type="password" name="confirm_password">

        <input type="submit" value="Update" />
    </form>
    <?php
                break;

            case 'delete':
                //show delete code
            ?>
    <h2>Delete Account</h2>
    <strong>Are You Sure?</strong>
    <form method="post">
        <input type="hidden" name="_action" value="confirm_delete">
        <button>Yes, Im Sure</button>
        <a href="index.php">Cancel</a>
    </form>
    <?php
                break;
        }
        ?>
    <?php else : ?>

    <em style="color: red;"><?php echo $userManager->errorMessage(); ?></em>

    <form method="post">
        <input type="hidden" name="_action" value="login">
        <h2>Existing Users</h2>
        <label>Username</label>
        <input type="text" name="username" />
        <br />

        <label>Password</label>
        <input type="password" name="password" />
        <br />
        <input type="submit" value="Login" />
    </form>



    <form method="post">
        <input type="hidden" name="_action" value="signup">
        <h2>Create an Account</h2>
        <br />

        <label>Create Username</label>
        <input type="text" name="username">
        <br />

        <label>Password</label>
        <input type="password" name="password">
        <br />

        <label>Confirm Password</label>
        <input type="password" name="confirm_password">

        <br />

        <input type="submit" value="Create">
    </form>
    <?php endif; ?>

    <script src="dist/js/main.js"></script>
</body>

</html>