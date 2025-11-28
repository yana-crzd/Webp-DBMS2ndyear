<?php
session_start();
require_once __DIR__ . '/db.php';

$errors = [
  'email' => '',
  'password' => ''
];

$email = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $email = trim($_POST['email']);
  $password = trim($_POST['password']);

  $isValid = true;

  // EMPTY FIELDS
  if (empty($email)) {
    $errors['email'] = "Please enter your email";
    $isValid = false;
  }

  if (empty($password)) {
    $errors['password'] = "Please enter your password";
    $isValid = false;
  }

  if ($isValid) {

    // CHECK EMAIL
    $stmt = $conn->prepare("SELECT id, fullname, email, address, password, role FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
      $errors['email'] = "Invalid email";
    } else {
      $user = $result->fetch_assoc();

      if (!password_verify($password, $user['password'])) {
        $errors['password'] = "Invalid password";
      } else {

        // STORE USER DATA IN SESSION
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['address'] = $user['address'];
        $_SESSION['role'] = $user['role'];

        // REDIRECT
        if ($user['role'] === "Admin") {
          header("Location: AdminDashboard.php");
        } else {
          header("Location: CustomerHome.php");
        }
        exit;
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Login</title>
</head>

<body>

  <div class="login-container">

    <h1 class="title">Welcome Back</h1>
    <p class="subtitle">Login to continue</p>

    <form method="POST" action="">

      <div class="input-group">
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="Enter your email">
        <small class="error"><?= $errors['email'] ?></small>
      </div>

      <div class="input-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="Enter your password">
        <small class="error"><?= $errors['password'] ?></small>
      </div>

      <button type="submit" class="login-btn">LOGIN</button>

    </form>

    <p class="signup-text">
      <a href="signup.php">Don't have an account?</a>
    </p>

  </div>

</body>

</html>


<style>
  html,
  body {
    margin: 0;
    padding: 0;
    height: 100%;
  }

  html {
    background: url("/asset/loginImage.png") no-repeat center center / cover;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
  }

  body {
    margin: 0;
    padding: 0;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: url("/asset/loginImage.png") no-repeat center center/cover;
    font-family: 'Poppins', sans-serif;
    position: relative;
    min-height: 100vh;
  }

  /* Optional: light background overlay for better text visibility */
  body::before {
    content: "";
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.25);
    /* adjust opacity if needed */
    backdrop-filter: blur(2px);
    z-index: -1;
  }

  /*Icon*/


  /* Center login box */
  .login-container {
    width: 350px;
    padding: 30px;
    background: rgba(255, 255, 255, 0.85);
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
    text-align: center;
    margin: auto;
    margin-top: 8%;
  }

  .title {
    font-size: 2rem;
    font-weight: 700;
    color: #692aa4;
    margin-bottom: 5px;
  }

  .subtitle {
    font-size: 0.95rem;
    color: #666;
    margin-bottom: 25px;
  }

  .input-group {
    text-align: left;
    margin-bottom: 18px;
  }

  .input-group label {
    font-size: 0.9rem;
    color: #444;
  }

  .input-group input {
    width: 100%;
    padding: 12px 15px;
    /* equal padding */
    height: 45px;
    /* equal height */
    border-radius: 10px;
    border: 1px solid #ccc;
    margin-top: 5px;
    outline: none;
    box-sizing: border-box;
    /* ensures padding doesn't break size */
    transition: 0.3s;
  }


  .input-group input:focus {
    border-color: #692aa4;
  }

  .login-btn {
    width: 100%;
    background: #692aa4;
    border: none;
    padding: 12px;
    border-radius: 10px;
    color: white;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
  }

  .login-btn:hover {
    background: #ba94dd;
  }

  .signup-text {
    margin-top: 20px;
    font-size: 0.9rem;
  }

  .signup-text a {
    color: #692aa4;
    font-weight: 600;
    text-decoration: none;
  }

  /*Login Container*/
  .login-container {
    width: 350px;
    padding: 40px 35px;
    text-align: center;

    background: rgba(255, 255, 255, 0.35);
    /* transparent white */
    backdrop-filter: blur(12px);
    /* the blur effect */
    -webkit-backdrop-filter: blur(12px);
    /* Safari support */

    border-radius: 15px;
    border: 1px solid rgba(255, 255, 255, 0.35);
    box-shadow: 0 4px 25px rgba(0, 0, 0, 0.25);
  }
</style>