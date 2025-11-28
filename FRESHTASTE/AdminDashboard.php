<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$db = "myfreshtaste_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8");

// === HANDLE AJAX CRUD & STATUS UPDATE ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  header('Content-Type: application/json');

  // User CRUD
  if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $id = !empty($_POST['id']) ? (int) $_POST['id'] : null;
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $role = $_POST['role'] ?? 'Customer';
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    if ($action === 'delete') {
      $stmt = $conn->prepare("DELETE FROM user WHERE id = ?");
      $stmt->bind_param("i", $id);
      echo json_encode(['success' => $stmt->execute()]);
      $stmt->close();
      exit;
    }

    // Add or Update User
    if ($id) {
      // Update
      if ($password) {
        $stmt = $conn->prepare("UPDATE user SET fullname=?, email=?, address=?, role=?, password=? WHERE id=?");
        $stmt->bind_param("sssssi", $fullname, $email, $address, $role, $password, $id);
      } else {
        $stmt = $conn->prepare("UPDATE user SET fullname=?, email=?, address=?, role=? WHERE id=?");
        $stmt->bind_param("ssssi", $fullname, $email, $address, $role, $id);
      }
    } else {
      // Insert
      $defaultPass = $password ?? password_hash('user123', PASSWORD_DEFAULT);
      $stmt = $conn->prepare("INSERT INTO user (fullname, email, address, role, password) VALUES (?, ?, ?, ?, ?)");
      $stmt->bind_param("sssss", $fullname, $email, $address, $role, $defaultPass);
    }

    $success = $stmt->execute();
    echo json_encode(['success' => $success]);
    $stmt->close();
    exit;
  }

  // Update Order Status (from updatedorderstatus.php logic merged here)
  if (isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = (int) $_POST['order_id'];
    $status = $_POST['status'];

    $valid_statuses = ['pending', 'confirmed', 'delivered'];
    if (in_array($status, $valid_statuses)) {
      $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
      $stmt->bind_param("si", $status, $order_id);
      $success = $stmt->execute();
      echo json_encode(['success' => $success]);
      $stmt->close();
    } else {
      echo json_encode(['success' => false, 'error' => 'Invalid status']);
    }
    exit;
  }
}

// === FETCH DYNAMIC DASHBOARD STATS ===
$total_users_result = $conn->query("SELECT COUNT(*) as cnt FROM user");
$total_users = $total_users_result->fetch_assoc()['cnt'];

$total_orders_result = $conn->query("SELECT COUNT(*) as cnt FROM orders");
$total_orders = $total_orders_result->fetch_assoc()['cnt'];

$pending_orders_result = $conn->query("SELECT COUNT(*) as cnt FROM orders WHERE status = 'pending'");
$pending_orders = $pending_orders_result->fetch_assoc()['cnt'];

$revenue_result = $conn->query("
    SELECT COALESCE(SUM(total), 0) as revenue 
    FROM orders 
    WHERE status IN ('confirmed', 'delivered')
");
$total_revenue = $revenue_result->fetch_assoc()['revenue'];

// === FETCH USERS & ORDERS FOR TABLES ===
$users = $conn->query("SELECT * FROM user ORDER BY id DESC");

$statusFilter = $_GET['status'] ?? '';
if ($statusFilter && in_array($statusFilter, ['pending', 'confirmed', 'delivered'])) {
  $stmt = $conn->prepare("SELECT * FROM orders WHERE status = ? ORDER BY id DESC");
  $stmt->bind_param("s", $statusFilter);
} else {
  $stmt = $conn->prepare("SELECT * FROM orders ORDER BY id DESC");
}
$stmt->execute();
$orders_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FreshTaste - Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --p: #692aa4;
      --ph: #5a1f8c;
      --bg: #f8f5ff;
      --green: #51cf66;
      --red: #ff6b6b;
      --orange: #ffa726;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: var(--bg);
      color: #333;
      overflow-x: hidden;
    }

    header {
      background: #fff;
      padding: 15px 30px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, .1);
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logo {
      font-size: 1.8rem;
      font-weight: bold;
      color: var(--p);
    }

    .admin-info span {
      font-weight: 600;
    }

    #toggleSidebar {
      position: fixed;
      top: 90px;
      left: 20px;
      background: var(--p);
      color: #fff;
      border: none;
      width: 50px;
      height: 50px;
      border-radius: 50%;
      font-size: 24px;
      cursor: pointer;
      z-index: 1001;
      box-shadow: 0 4px 15px rgba(105, 42, 164, .4);
    }

    .sidebar {
      position: fixed;
      top: 70px;
      left: -300px;
      width: 280px;
      height: calc(100vh - 70px);
      background: var(--p);
      color: #fff;
      padding: 30px 20px;
      transition: .4s;
      z-index: 999;
    }

    .sidebar.active {
      left: 0;
    }

    .sidebar a {
      display: block;
      padding: 14px 20px;
      color: #fff;
      text-decoration: none;
      border-radius: 10px;
      margin-bottom: 10px;
      transition: .3s;
      position: relative;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background: rgba(255, 255, 255, .2);
      transform: translateX(10px);
    }

    .sidebar a.active::before {
      content: '';
      position: absolute;
      left: 0;
      top: 50%;
      transform: translateY(-50%);
      width: 5px;
      height: 30px;
      background: #fff;
      border-radius: 0 5px 5px 0;
    }

    .main-content {
      margin-left: 0;
      padding: 100px 40px 40px;
      transition: .4s;
    }

    .main-content.shift {
      margin-left: 280px;
    }

    .dashboard-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 25px;
      margin-top: 20px;
    }

    .card {
      background: #fff;
      border-radius: 18px;
      padding: 25px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, .08);
    }

    .card h3 {
      color: var(--p);
      margin-bottom: 15px;
      font-size: 1.4rem;
    }

    .stat-number {
      font-size: 2rem;
      font-weight: 700;
      margin: 10px 0;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    th,
    td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }

    th {
      background: var(--p);
      color: #fff;
    }

    tr:hover {
      background: #f5f0ff;
    }

    .btn-small {
      padding: 6px 12px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: .9rem;
      margin: 0 4px;
    }

    .btn-edit {
      background: #ffc107;
      color: #fff;
    }

    .btn-delete {
      background: #dc3545;
      color: #fff;
    }

    .btn-add {
      background: #28a745;
      color: #fff;
      padding: 10px 20px;
      border-radius: 10px;
      float: right;
      margin-bottom: 15px;
      cursor: pointer;
    }

    .modal {
      display: none;
      position: fixed;
      z-index: 2000;
      inset: 0;
      background: rgba(0, 0, 0, .5);
      backdrop-filter: blur(5px);
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      background: #fff;
      padding: 30px;
      border-radius: 18px;
      width: 90%;
      max-width: 500px;
      box-shadow: 0 10px 30px rgba(105, 42, 164, .3);
    }

    .form-group {
      margin-bottom: 18px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      color: var(--p);
      font-weight: 600;
    }

    .form-group input,
    .form-group select {
      width: 100%;
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 1rem;
    }

    .form-group input:focus,
    .form-group select:focus {
      border-color: var(--p);
      outline: none;
    }

    .modal-buttons {
      text-align: right;
      margin-top: 25px;
    }

    .btn-modal {
      padding: 10px 20px;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      margin-left: 10px;
    }

    .btn-yes {
      background: var(--p);
      color: #fff;
    }

    .btn-no {
      background: #ddd;
      color: #333;
    }

    /* === STATUS DROPDOWN - COLORED BADGES === */
    .status-dropdown {
      border: none;
      color: white;
      font-weight: 600;
      cursor: pointer;
      padding: 6px 14px;
      border-radius: 20px;
      min-width: 110px;
      text-align: center;
      font-size: 0.9rem;
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      background-image: none !important;
      background-color: transparent !important;
      position: relative;
      z-index: 1;
    }

    /* Override default select arrow */
    .status-dropdown::after {
      content: 'down arrow';
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      font-family: 'Font Awesome 6 Free';
      font-weight: 900;
      pointer-events: none;
      color: white;
      z-index: 2;
    }

    /* Remove default arrow in Firefox */
    .status-dropdown::-moz-focus-inner {
      border: 0;
    }

    .status-dropdown:focus {
      outline: none;
    }

    /* Dynamic background colors based on status */
    .status-pending {
      background-color: #E67E22 !important;
      /* Orange */
    }

    .status-confirmed {
      background-color: #0D47A1 !important;
      /* Blue */
    }

    .status-delivered {
      background-color: #0F8A2F !important;
      /* Green */
    }

    /* Ensure text remains white and readable */
    .status-dropdown,
    .status-dropdown option {
      color: white !important;
    }

    .status-dropdown option {
      color: #000 !important;
      background: #fff !important;
    }

    @media (max-width: 768px) {
      .main-content.shift {
        margin-left: 0;
        margin-top: 80px;
      }
    }
  </style>
</head>

<body>

  <!-- HEADER -->
  <header>
    <div class="logo">FreshTaste <span style="font-size:0.8rem;color:#888;">Admin</span></div>
    <div class="admin-info">
      <span><strong>Admin Panel</strong></span>
      <i class="fas fa-user-shield"></i>
    </div>
  </header>

  <!-- TOGGLE SIDEBAR -->
  <button id="toggleSidebar"><i class="fas fa-bars"></i></button>

  <!-- SIDEBAR -->
  <div class="sidebar" id="sidebar">
    <h2>Admin Panel</h2>
    <a href="#" class="active" onclick="setActive(this); showSection('dashboard')"><i class="fas fa-tachometer-alt"></i>
      Dashboard</a>
    <a href="#" onclick="setActive(this); showSection('users')"><i class="fas fa-users"></i> User Management</a>
    <a href="#" onclick="setActive(this); showSection('orders')"><i class="fas fa-shopping-bag"></i> User Orders</a>
    <a href="#" onclick="openLogoutModal()"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <!-- MAIN CONTENT -->
  <div class="main-content" id="mainContent">

    <!-- DASHBOARD -->
    <div id="dashboard">
      <h1 style="color:var(--p); margin-bottom:30px;">Welcome back, Admin!</h1>
      <div class="dashboard-grid">
        <div class="card">
          <h3>Total Users</h3>
          <div class="stat-number" style="color:var(--p);"><?= $total_users ?></div>
        </div>
        <div class="card">
          <h3>Total Orders</h3>
          <div class="stat-number" style="color:#333;"><?= $total_orders ?></div>
        </div>
        <div class="card">
          <h3>Pending Orders</h3>
          <div class="stat-number" style="color:var(--red);"><?= $pending_orders ?></div>
        </div>
        <div class="card">
          <h3>Total Revenue</h3>
          <div class="stat-number" style="color:var(--green);">₱<?= number_format($total_revenue, 2) ?></div>
        </div>
      </div>
    </div>

    <!-- USER MANAGEMENT -->
    <div id="users" style="display:none;">
      <h1 style="color:var(--p); margin-bottom:20px;">User Management</h1>
      <button class="btn-add" onclick="openAddModal()">Add New User</button>
      <div class="card">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Full Name</th>
              <th>Email</th>
              <th>Address</th>
              <th>Role</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($u = $users->fetch_assoc()): ?>
              <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['fullname']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['address']) ?></td>
                <td>
                  <span
                    style="padding:5px 10px; border-radius:20px; font-size:0.8rem; background:<?= $u['role'] == 'Admin' ? '#d4edda' : '#fff3cd' ?>; color:<?= $u['role'] == 'Admin' ? '#155724' : '#856404' ?>;">
                    <?= $u['role'] ?>
                  </span>
                </td>
                <td>
                  <button class="btn-small btn-edit" onclick='openEditModal(<?= json_encode($u) ?>)'>Edit</button>
                  <button class="btn-small btn-delete" onclick="deleteUser(<?= $u['id'] ?>)">Delete</button>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ORDERS -->
    <div id="orders" style="display:none;">
      <h1 style="color:var(--p); margin-bottom:20px;">User Orders</h1>
      <div class="card">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Customer ID</th>
              <th>Full Name</th>
              <th>Items</th>
              <th>Total</th>
              <th>Order Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($o = $orders_result->fetch_assoc()): ?>
              <tr>
                <td><?= $o['id'] ?></td>
                <td><?= $o['customer_id'] ?></td>
                <td><?= htmlspecialchars($o['customer_name']) ?></td>
                <td><?= htmlspecialchars($o['items']) ?></td>
                <td>₱<?= number_format($o['total'], 2) ?></td>
                <td><?= date('M j, Y g:i A', strtotime($o['order_date'])) ?></td>
                <td>
                  <select class="status-dropdown status-<?= $o['status'] ?>" data-id="<?= $o['id'] ?>"
                    onchange="updateOrderStatus(this)">
                    <option value="pending" <?= $o['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="confirmed" <?= $o['status'] == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                    <option value="delivered" <?= $o['status'] == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                  </select>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ADD/EDIT USER MODAL -->
  <div id="userModal" class="modal">
    <div class="modal-content">
      <h2 style="color:var(--p); text-align:center;" id="modalTitle">Add New User</h2>
      <form id="userForm">
        <input type="hidden" id="userId">
        <div class="form-group"><label>Full Name</label><input type="text" id="fullname" required></div>
        <div class="form-group"><label>Email</label><input type="email" id="email" required></div>
        <div class="form-group"><label>Address</label><input type="text" id="address" required></div>
        <div class="form-group">
          <label>Role</label>
          <select id="role" required>
            <option value="Customer">Customer</option>
            <option value="Admin">Admin</option>
          </select>
        </div>
        <div class="form-group">
          <label>Password <small>(leave blank to keep current)</small></label>
          <input type="password" id="password">
        </div>
        <div class="modal-buttons">
          <button type="button" class="btn-modal btn-no" onclick="closeModal()">Cancel</button>
          <button type="submit" class="btn-modal btn-yes">Save User</button>
        </div>
      </form>
    </div>
  </div>

  <!-- LOGOUT MODAL -->
  <div id="logoutModal" class="modal">
    <div class="modal-content" style="text-align:center;">
      <i class="fas fa-sign-out-alt" style="font-size:3rem; color:var(--p); margin-bottom:15px;"></i>
      <h3>Confirm Logout</h3>
      <p>Are you sure you want to logout?</p>
      <div class="modal-buttons">
        <button class="btn-modal btn-no" onclick="closeModal('logoutModal')">No</button>
        <button class="btn-modal btn-yes" onclick="window.location.href='login.php'">Yes, Logout</button>
      </div>
    </div>
  </div>



  <script>
    // Sidebar & Section Toggle
    document.getElementById('toggleSidebar').onclick = () => {
      document.getElementById('sidebar').classList.toggle('active');
      document.getElementById('mainContent').classList.toggle('shift');
    };

    function setActive(el) {
      document.querySelectorAll('.sidebar a').forEach(a => a.classList.remove('active'));
      el.classList.add('active');
    }

    function showSection(id) {
      document.querySelectorAll('#dashboard, #users, #orders').forEach(s => s.style.display = 'none');
      document.getElementById(id).style.display = 'block';
    }

    // User Modal
    function openAddModal() {
      document.getElementById('modalTitle').textContent = 'Add New User';
      document.getElementById('userForm').reset();
      document.getElementById('userId').value = '';
      document.getElementById('userModal').style.display = 'flex';
    }

    function openEditModal(u) {
      document.getElementById('modalTitle').textContent = 'Edit User';
      document.getElementById('userId').value = u.id;
      document.getElementById('fullname').value = u.fullname;
      document.getElementById('email').value = u.email;
      document.getElementById('address').value = u.address;
      document.getElementById('role').value = u.role;
      document.getElementById('userModal').style.display = 'flex';
    }

    function closeModal(id = 'userModal') {
      document.getElementById(id).style.display = 'none';
    }

    // Save User
    document.getElementById('userForm').onsubmit = async function (e) {
      e.preventDefault();
      const fd = new FormData();
      fd.append('action', 'save');
      fd.append('id', document.getElementById('userId').value);
      fd.append('fullname', document.getElementById('fullname').value);
      fd.append('email', document.getElementById('email').value);
      fd.append('address', document.getElementById('address').value);
      fd.append('role', document.getElementById('role').value);
      fd.append('password', document.getElementById('password').value);

      const res = await fetch('', { method: 'POST', body: fd });
      const data = await res.json();
      if (data.success) location.reload();
    };

    // Delete User
    async function deleteUser(id) {
      if (confirm('Delete this user permanently?')) {
        const fd = new FormData();
        fd.append('action', 'delete');
        fd.append('id', id);
        const res = await fetch('', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) location.reload();
      }
    }

    // Update Order Status
    async function updateOrderStatus(select) {
      const orderId = select.dataset.id;
      const newStatus = select.value;

      // Update visual style
      select.className = `status-dropdown status-${newStatus}`;

      // Send to server
      const fd = new FormData();
      fd.append('order_id', orderId);
      fd.append('status', newStatus);

      await fetch('', {
        method: 'POST',
        body: fd
      });
    }

    // Logout Modal
    function openLogoutModal() {
      document.getElementById('logoutModal').style.display = 'flex';
    }

    // Close modal on outside click
    window.onclick = e => {
      if (e.target.classList.contains('modal')) {
        e.target.style.display = 'none';
      }
    };

    // Initialize status colors
    document.querySelectorAll('.status-dropdown').forEach(sel => {
      sel.classList.add(`status-${sel.value}`);
    });
  </script>
</body>

</html>