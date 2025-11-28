<?php
session_start();
require_once __DIR__ . '/db.php';

$errors = [
    'fullname' => '',
    'email' => '',
    'role' => '',
    'address' => '',
    'password' => '',
    'confirmPassword' => ''
];

$values = [
    'fullname' => '',
    'email' => '',
    'role' => '',
    'address' => ''
];

// For showing success modal
$showSuccessModal = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullname = trim($_POST['fullName']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $address = trim($_POST['address']);
    $password = $_POST['password'];
    $confirmPass = $_POST['confirmPassword'];

    $values = [
        'fullname' => $fullname,
        'email' => $email,
        'role' => $role,
        'address' => $address
    ];

    $isValid = true;

    if (!preg_match("/^[A-Za-z ]+$/", $fullname)) {
        $errors['fullname'] = "Full name must contain letters only.";
        $isValid = false;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !str_ends_with($email, "@gmail.com")) {
        $errors['email'] = "Email must be a valid @gmail.com address.";
        $isValid = false;
    }
    if (empty($role)) {
        $errors['role'] = "Please select a role.";
        $isValid = false;
    }
    if (empty($address)) {
        $errors['address'] = "Address is required.";
        $isValid = false;
    }
    if (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters.";
        $isValid = false;
    }
    if ($password !== $confirmPass) {
        $errors['confirmPassword'] = "Passwords do not match.";
        $isValid = false;
    }

    if ($isValid) {
        $check = $conn->prepare("SELECT id FROM user WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $errors['email'] = "Email already exists.";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $insert = $conn->prepare("
                INSERT INTO user (fullname, address, email, password, role)
                VALUES (?, ?, ?, ?, ?)
            ");
            $insert->bind_param("sssss", $fullname, $address, $email, $passwordHash, $role);
            $insert->execute();

            // Set session for auto-login
            $_SESSION['loggedin'] = true;
            $_SESSION['user_role'] = $role;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_fullname'] = $fullname;

            // Show success modal
            $showSuccessModal = true;
            $successMessage = "Welcome, $fullname!<br>Your account has been created successfully.";
            $redirectUrl = $role === 'Admin' ? 'admindashboard.php' : 'login.php';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - FreshTaste</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: url("asset/loginImage.png") no-repeat center center / cover;
            background-attachment: fixed;
            position: relative;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(2px);
            z-index: -1;
        }

        .container {
            width: 380px;
            padding: 35px;
            text-align: center;
            background: rgba(255, 255, 255, 0.35);
            backdrop-filter: blur(12px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.35);
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.25);
        }

        h2 {
            font-size: 1.9rem;
            font-weight: 700;
            color: #692aa4;
            margin-bottom: 25px;
        }

        .input-group {
            text-align: left;
            margin-bottom: 20px;
        }

        .input-group label {
            font-size: 0.9rem;
            color: #444;
            font-weight: 600;
            display: block;
            margin-bottom: 6px;
        }

        .input-group input,
        .input-group select {
            width: 100%;
            padding: 12px 15px;
            height: 45px;
            border-radius: 10px;
            border: 1px solid #ccc;
            background: #fff;
            font-size: 0.95rem;
        }

        .input-group input:focus,
        .input-group select:focus {
            border-color: #692aa4;
            outline: none;
        }

        .error {
            color: #d63031;
            font-size: 13px;
            margin-top: 5px;
        }

        button {
            width: 100%;
            padding: 14px;
            background: #692aa4;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        button:hover {
            background: #5a1f8c;
        }

        /* SUCCESS MODAL */
        .success-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
            display:
                <?= $showSuccessModal ? 'flex' : 'none' ?>
            ;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            animation: fadeIn 0.4s ease;
        }

        .success-box {
            background: white;
            padding: 40px 30px;
            border-radius: 20px;
            width: 90%;
            max-width: 420px;
            text-align: center;
            box-shadow: 0 15px 40px rgba(105, 42, 164, 0.3);
            animation: slideDown 0.5s ease;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: #d4edda;
            color: #155724;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            animation: bounce 1s;
        }

        .success-box h3 {
            color: #692aa4;
            font-size: 1.8rem;
            margin-bottom: 15px;
        }

        .success-box p {
            color: #555;
            font-size: 1rem;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .success-box button {
            width: auto;
            padding: 12px 30px;
            background: #692aa4;
            font-size: 1rem;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes bounce {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.15);
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Create Account</h2>
        <form method="POST" action="">
            <!-- Full Name -->
            <div class="input-group">
                <label>Full Name</label>
                <input type="text" name="fullName" value="<?= htmlspecialchars($values['fullname']) ?>"
                    placeholder="Enter full name" required>
                <p class="error"><?= $errors['fullname'] ?></p>
            </div>

            <!-- Email -->
            <div class="input-group">
                <label>Email</label>
                <input type="text" name="email" value="<?= htmlspecialchars($values['email']) ?>"
                    placeholder="example@gmail.com" required>
                <p class="error"><?= $errors['email'] ?></p>
            </div>

            <!-- Role -->
            <div class="input-group">
                <label>Role</label>
                <select name="role" required>
                    <option value="" disabled <?= empty($values['role']) ? 'selected' : '' ?>>Select role</option>
                    <option value="Customer" <?= $values['role'] === 'Customer' ? 'selected' : '' ?>>Customer</option>
                    

                </select>
                <p class="error"><?= $errors['role'] ?></p>
            </div>

            <!-- Address -->
            <div class="input-group">
                <label>Address</label>
                <input type="text" name="address" value="<?= htmlspecialchars($values['address']) ?>"
                    placeholder="Enter address" required>
                <p class="error"><?= $errors['address'] ?></p>
            </div>

            <!-- Password -->
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter password" required>
                <p class="error"><?= $errors['password'] ?></p>
            </div>

            <!-- Confirm Password -->
            <div class="input-group">
                <label>Confirm Password</label>
                <input type="password" name="confirmPassword" placeholder="Confirm password" required>
                <p class="error"><?= $errors['confirmPassword'] ?></p>
            </div>

            <button type="submit">Sign Up</button>
        </form>
    </div>

    <!-- SUCCESS MODAL -->
    <?php if ($showSuccessModal): ?>
        <div class="success-modal" id="successModal">
            <div class="success-box">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h3>Success!</h3>
                <p><?= $successMessage ?></p>
                <button onclick="window.location.href='<?= $redirectUrl ?>'">
                    Continue to <?= $role === 'Admin' ? 'Dashboard' : 'Home' ?>
                </button>
            </div>
        </div>

        <script>
            // Auto redirect after 3 seconds
            setTimeout(() => {
                window.location.href = "<?= $redirectUrl ?>";
            }, 3000);
        </script>
    <?php endif; ?>

</body>

</html>