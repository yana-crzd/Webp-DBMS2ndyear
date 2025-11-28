<?php session_start();






?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FreshTaste - Home</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    font-family: 'Poppins', sans-serif;
    background: #fffaf2;
    color: #333;
    line-height: 1.6;
  }

  header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 40px;
    background: white;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    z-index: 1000;
  }

  .logo {
    font-size: 2rem;
    font-weight: bold;
    color: #692aa4;
  }

  .header-icons {
    display: flex;
    gap: 20px;
    align-items: center;
  }

  .icon-btn {
    position: relative;
    width: 50px;
    height: 50px;
    background: white;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    color: #692aa4;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transition: 0.3s;
  }

  .icon-btn:hover {
    background: #692aa4;
    color: white;
    transform: scale(1.1);
  }

  .cart-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #692aa4;
    color: white;
    font-size: 12px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  /* Hamburger Menu */
  .hamburger-wrap {
    position: relative;
  }

  #hamburgerBtn {
    background: none;
    border: none;
    font-size: 28px;
    cursor: pointer;
    color: #692aa4;
  }

  #hamburgerDropdown {
    position: absolute;
    top: 60px;
    right: 0;
    background: white;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
    display: none;
    flex-direction: column;
    width: 180px;
    z-index: 999;
  }

  #hamburgerDropdown.show {
    display: flex;
  }

  #hamburgerDropdown a {
    padding: 14px 20px;
    text-decoration: none;
    color: #444;
    font-weight: 600;
  }

  #hamburgerDropdown a:hover {
    background: #ffe6d5;
    color: #692aa4;
  }

  /* Hero & Carousel */
  .hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 80px 60px;
    background: linear-gradient(135deg, #fff3e0, #ffe6cc);
  }

  .hero-text {
    max-width: 50%;
  }

  .hero-text h2 {
    font-size: 3rem;
    color: #692aa4;
  }

  .hero-text p {
    margin: 20px 0;
    font-size: 1.1rem;
  }

  .btn {
    padding: 12px 30px;
    background: #692aa4;
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
  }

  .carousel {
    position: relative;
    width: 500px;
    height: 380px;
    overflow: hidden;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  }

  .carousel img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    position: absolute;
    opacity: 0;
    transition: opacity 1s ease;
  }

  .carousel img.active {
    opacity: 1;
  }

  .prev,
  .next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0, 0, 0, 0.5);
    color: white;
    border: none;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    font-size: 24px;
    cursor: pointer;
  }

  .prev {
    left: 15px;
  }

  .next {
    right: 15px;
  }

  /* Menu Section */
  .products {
    padding: 80px 40px;
    background: #fff8f0;
    text-align: center;
  }

  .products h2 {
    font-size: 2.5rem;
    color: #692aa4;
    margin-bottom: 30px;
  }

  .category-filter button {
    padding: 10px 20px;
    margin: 8px;
    border: none;
    background: #692aa4;
    color: white;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
  }

  .category-filter button.active {
    background: #4a1f7a;
  }

  .product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
    margin-top: 40px;
  }

  .product-card {
    background: white;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1 smoker);
  }

  .product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
  }

  .product-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
  }

  .product-info {
    padding: 18px;
    text-align: center;
  }

  .product-info h3 {
    color: #692aa4;
    margin: 8px 0;
    font-size: 1.4rem;
  }

  .product-info .price {
    font-size: 1.3rem;
    font-weight: bold;
    color: #333;
    margin: 8px 0;
  }

  .product-info .description {
    font-size: 0.95rem;
    color: #666;
    margin: 12px 0;
    line-height: 1.5;
    min-height: 60px;
  }

  .addon-select {
    width: 100%;
    padding: 10px;
    margin: 12px 0;
    border: 1.5px solid #ddd;
    border-radius: 8px;
    font-size: 0.95rem;
  }

  .actions {
    display: flex;
    justify-content: center;
    gap: 25px;
    margin-top: 15px;
  }

  .actions i {
    font-size: 26px;
    cursor: pointer;
    transition: 0.3s;
  }

  .actions .fa-heart:hover {
    color: red;
  }

  .actions .fa-cart-plus:hover {
    color: #692aa4;
    transform: scale(1.2);
  }

  /* Cart Modal */
  .modal-overlay {
    display: none;
    position: fixed;
    z-index: 9999;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
  }

  .modal-box {
    background: white;
    width: 90%;
    max-width: 520px;
    max-height: 85vh;
    overflow-y: auto;
    border-radius: 18px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
  }

  .success-title {
    color: #692aa4;
    font-size: 22px;
    margin-bottom: 10px;
    font-weight: 700;
  }

  .success-text {
    font-size: 16px;
    color: #444;
  }

  .cart-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 18px 10px;
    border-bottom: 1px solid #eee;
  }

  #cart-list {
    margin-top: 15px;
    margin-bottom: 15px;
  }

  .cart-item img {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 12px;
  }

  .quantity-controls button {
    width: 36px;
    height: 36px;
    background: #692aa4;
    color: white;
    border: none;
    border-radius: 50%;
    font-size: 18px;
    cursor: pointer;
  }

  #cart-total {
    font-size: 1.5rem;
    font-weight: bold;
    text-align: right;
    margin: 20px 0;
    color: #692aa4;
  }

  .checkout-btn {
    width: 100%;
    padding: 16px;
    background: #692aa4;
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 1.1rem;
    cursor: pointer;
    font-weight: bold;
  }

  .checkout-btn:hover {
    background: #551a8a;
  }


  /* Hamburger Menu */
  .hamburger-wrap {
    position: relative;
  }

  #hamburgerBtn {
    background: none;
    border: none;
    font-size: 28px;
    cursor: pointer;
    color: #692aa4;
  }

  #hamburgerDropdown {
    position: absolute;
    top: 60px;
    right: 0;
    background: white;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
    width: 180px;
    display: none;
    flex-direction: column;
    z-index: 999;
  }

  #hamburgerDropdown.show {
    display: flex;
  }

  #hamburgerDropdown a {
    padding: 14px 20px;
    text-decoration: none;
    color: #444;
    font-weight: 600;
  }

  #hamburgerDropdown a:hover {
    background: #ffe6d5;
    color: #692aa4;
  }

  /* Modals (beautiful & simple) */
  .modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    align-items: center;
    justify-content: center;
    z-index: 2000;
    backdrop-filter: blur(5px);
  }

  .modal-content {
    background: white;
    width: 90%;
    max-width: 400px;
    border-radius: 18px;
    padding: 30px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
  }

  .modal-content h2 {
    color: #692aa4;
    margin-bottom: 20px;
  }

  .profile-info {
    text-align: left;
    line-height: 2;
    font-size: 1.1rem;
    color: #444;
  }

  .profile-info strong {
    color: #692aa4;
  }

  .modal-buttons {
    display: flex;
    gap: 15px;
    margin-top: 25px;
  }

  .modal-btn {
    flex: 1;
    padding: 12px;
    border: none;
    border-radius: 10px;
    font-weight: bold;
    cursor: pointer;
  }

  .yes-btn {
    background: #692aa4;
    color: white;
  }

  .no-btn,
  .close-btn {
    background: #692aa4;
    color: white;
  }

  .close-btn {
    width: 100%;
    margin-top: 20px;
  }

  .modal-overlay {
    display: none;
    position: fixed;
    z-index: 2000;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
  }

  .modal-box {
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    width: 300px;
    text-align: center;
  }

  .success-btn {
    width: 100%;
    padding: 12px 0;
    background: #692aa4;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s ease;
  }

  .success-btn:hover {
    background: #692aa4;
  }
  footer {
  background: #692aa4;
  color: #fff;
  text-align: center;
  padding: 8px 0;
  font-size: 0.9rem;
}
</style>

<body>

  <!-- HEADER -->
  <header>
    <h1 class="logo">FreshTaste</h1>
    <div class="header-icons">
      <a href="#home" class="icon-btn"><i class="fa-solid fa-house"></i></a>
      <a href="#menu" class="icon-btn"><i class="fa-solid fa-utensils"></i></a>
      <div class="icon-btn" onclick="openCart()">
        <i class="fa-solid fa-shopping-cart"></i>
        <span class="cart-count">0</span>
      </div>
      <div class="hamburger-wrap">
        <button id="hamburgerBtn"><i class="fas fa-bars"></i></button>
        <div id="hamburgerDropdown">
          <a href="#" onclick="openProfileModal()">Profile</a>
          <a href="#" onclick="openAboutModal()">About Us</a>
          <a href="#" onclick="openLogoutModal()">Logout</a>
        </div>
      </div>
    </div>
  </header>


  <!-- HERO + CAROUSEL -->
  <section class="hero" id="home">
    <div class="hero-text">
      <h2>Welcome to FreshTaste</h2>
      <p>Enjoy the freshest burgers, pizzas, smoothies, milk tea, donuts, cakes, and cookies made with love and premium
        ingredients!</p>
      <a href="#menu" class="btn">Explore Menu</a>
    </div>
    <div class="carousel">
      <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38" alt="Food 1" class="active">
      <img src="https://images.unsplash.com/photo-1513104890138-7c749659a591" alt="Pizza">
      <img src="https://images.unsplash.com/photo-1551024601-bec78aea704b" alt="Dessert">
      <button class="prev" onclick="changeSlide(-1)">Prev</button>
      <button class="next" onclick="changeSlide(1)">Next</button>
    </div>
  </section>



  <!-- PROFILE MODAL -->
  <div id="profileModal" class="modal">
    <div class="modal-content">
      <h2>My Profile</h2>
      <div class="profile-info">
        <p><strong>Name: </strong> <?= $_SESSION['fullname'] ?? 'Guest' ?></p>
        <p><strong>Email:</strong> <?= $_SESSION['email'] ?? '-' ?></p>
        <p><strong>Address: </strong> <?= $_SESSION['address'] ?? '-' ?></p>
        <p><strong>Role: </strong> <?= $_SESSION['role'] ?? '-' ?></p>
      </div>
      <button class="modal-btn close-btn" onclick="closeProfileModal()">Close</button>
    </div>
  </div>

  <!-- ABOUT US MODAL -->
  <div id="aboutModal" class="modal">
    <div class="modal-content">
      <h2>About FreshTaste</h2>
      <p style="text-align:left; line-height:1.8; color:#555; margin:20px 0;">
        FreshTaste serves the best burgers, pizzas, drinks, and desserts in town!<br><br>
        Location: 123 Fresh Street, Manila, Philippines<br>
        Phone: +63 912 345 6789<br>
        Email: support@freshtaste.com
      </p>
      <button class="modal-btn close-btn" onclick="closeAboutModal()">Close</button>
    </div>
  </div>

  <!-- LOGOUT CONFIRM MODAL -->
  <div id="logoutModal" class="modal">
    <div class="modal-content">
      <h2>Logout</h2>
      <p>Are you sure you want to logout?</p>
      <div class="modal-buttons">
        <button class="modal-btn yes-btn" onclick="confirmLogout()">Yes</button>
        <button class="modal-btn no-btn" onclick="closeLogoutModal()">No</button>
      </div>
    </div>
  </div>

  <!-- MENU SECTION -->
  <section class="products" id="menu">
    <h2>Our Menu</h2>
    <div class="category-filter">
      <button class="active" data-category="all">All</button>
      <button data-category="food">Foods</button>
      <button data-category="drinks">Drinks</button>
      <button data-category="desserts">Desserts</button>
    </div>

    <div class="product-grid">
      <!-- Burger -->
      <div class="product-card" data-category="food">
        <img src="https://images.unsplash.com/photo-1565299507177-b0ac66763828" alt="Burger">
        <div class="product-info">
          <h3>Burger</h3>
          <p class="price">₱180</p>
          <p class="description">Juicy beef patty with fresh lettuce, tomato, onions, and our special sauce on a
            toasted.</p>
          <select class="addon-select">
            <option value="0">Regular</option>
            <option value="20">+ Cheese ₱20</option>
            <option value="25">+ Egg ₱25</option>
          </select>
          <div class="actions">
            <i class="fa-regular fa-heart"></i>
            <i class="fa-solid fa-cart-plus" onclick="addToCart(this)"></i>
          </div>
        </div>
      </div>

      <!-- Pizza -->
      <div class="product-card" data-category="food">
        <img src="https://images.unsplash.com/photo-1513104890138-7c749659a591" alt="Pizza">
        <div class="product-info">
          <h3>Pizza</h3>
          <p class="price">₱320</p>
          <p class="description">Hand-tossed crust topped with rich tomato sauce, melted mozzarella premium.</p>
          <select class="addon-select">
            <option value="0">Regular</option>
            <option value="50">+ Pepperoni ₱50</option>
            <option value="80">+ Extra Cheese ₱80</option>
          </select>
          <div class="actions">
            <i class="fa-regular fa-heart"></i>
            <i class="fa-solid fa-cart-plus" onclick="addToCart(this)"></i>
          </div>
        </div>
      </div>

      <!-- Smoothie -->
      <div class="product-card" data-category="drinks">
        <img
          src="https://images.unsplash.com/photo-1553530666-ba11a7da3888?q=80&w=686&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
          alt="Smoothie">
        <div class="product-info">
          <h3>Smoothie</h3>
          <p class="price">₱120</p>
          <p class="description">Refreshing blend of fresh fruits, creamy yogurt, and ice — perfect for a hot day!</p>
          <select class="addon-select">
            <option value="0">Regular</option>
            <option value="35">+ Protein Powder ₱35</option>
            <option value="50">+ Mixed Nuts ₱50</option>
          </select>
          <div class="actions">
            <i class="fa-regular fa-heart"></i>
            <i class="fa-solid fa-cart-plus" onclick="addToCart(this)"></i>
          </div>
        </div>
      </div>

      <!-- Milk Tea -->
      <div class="product-card" data-category="drinks">
        <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24" alt="Milk Tea">
        <div class="product-info">
          <h3>Milk Tea</h3>
          <p class="price">₱110</p>
          <p class="description">Perfectly brewed tea with creamy milk and chewy pearls. Classic comfort in every sip.
          </p>
          <select class="addon-select">
            <option value="0">Regular</option>
            <option value="30">+ Tapioca Pearls ₱30</option>
            <option value="45">+ Pudding ₱45</option>
          </select>
          <div class="actions">
            <i class="fa-regular fa-heart"></i>
            <i class="fa-solid fa-cart-plus" onclick="addToCart(this)"></i>
          </div>
        </div>
      </div>

      <!-- Donut -->
      <div class="product-card" data-category="desserts">
        <img src="https://images.unsplash.com/photo-1551024601-bec78aea704b" alt="Donut">
        <div class="product-info">
          <h3>Donuts (6 pcs)</h3>
          <p class="price">₱140</p>
          <p class="description">Soft, fluffy donuts freshly fried and coated in sugar or topped with creamy glaze.</p>
          <select class="addon-select">
            <option value="0">Regular</option>
            <option value="30">+ Glaze ₱30</option>
            <option value="65">+ Chocolate Topping ₱65</option>
          </select>
          <div class="actions">
            <i class="fa-regular fa-heart"></i>
            <i class="fa-solid fa-cart-plus" onclick="addToCart(this)"></i>
          </div>
        </div>
      </div>

      <!-- Cake -->
      <div class="product-card" data-category="desserts">
        <img
          src="https://images.unsplash.com/photo-1624993014250-fc6877db3222?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
          alt="Cake">
        <div class="product-info">
          <h3>Cake Slice</h3>
          <p class="price">₱160</p>
          <p class="description">Moist and rich cake layered with smooth frosting — pure indulgence in every bite.</p>
          <select class="addon-select">
            <option value="0">Regular</option>
            <option value="85">+ Chocolate Ganache ₱85</option>
            <option value="95">+ Whipped Cream ₱95</option>
          </select>
          <div class="actions">
            <i class="fa-regular fa-heart"></i>
            <i class="fa-solid fa-cart-plus" onclick="addToCart(this)"></i>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CART MODAL -->
  <div id="cartModal" class="modal-overlay">
    <div class="modal-box">
      <h2 style="color:#692aa4; text-align:center;">Your Cart</h2>
      <ul id="cart-list"></ul>
      <p id="cart-total">Total: ₱0</p>
      <button id="checkoutBtn" class="checkout-btn">Proceed to Checkout</button>
      <button style="margin-top:10px; background:#999;" class="checkout-btn" onclick="closeCart()">Close</button>
    </div>
  </div>

 <div id="successModal" class="modal-overlay">
  <div class="modal-box">
    <h2 style="color:violet; text-align:center;">Order Successful!</h2>
    <p style="text-align:center; font-size:18px;">Thank you for your order!<br>We'll prepare it right away.</p>
  </div>
</div>

<footer>
    <p>Disclaimer: This website is for educational purposes only. All rights reserved 2025 &copy; Copyright ITGirls  </p>
  </footer>


 <script>
  // Carousel
  let slideIndex = 0;
  const slides = document.querySelectorAll('.carousel img');
  function changeSlide(n) {
    slides[slideIndex].classList.remove('active');
    slideIndex = (slideIndex + n + slides.length) % slides.length;
    slides[slideIndex].classList.add('active');
  }
  setInterval(() => changeSlide(1), 5000);

  // Hamburger & Filter (keep as is)
  document.getElementById('hamburgerBtn').onclick = () => {
    document.getElementById('hamburgerDropdown').classList.toggle('show');
  };

  document.querySelectorAll('.category-filter button').forEach(btn => {
    btn.onclick = () => {
      document.querySelectorAll('.category-filter button').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const cat = btn.dataset.category;
      document.querySelectorAll('.product-card').forEach(card => {
        card.style.display = (cat === 'all' || card.dataset.category === cat) ? 'block' : 'none';
      });
    };
  });

  // Cart System
  let cart = [];

  function addToCart(btn) {
    const card = btn.closest('.product-card');
    const name = card.querySelector('h3').textContent.trim();
    const basePrice = parseFloat(card.querySelector('.price').textContent.replace('₱', ''));
    const addonPrice = parseFloat(card.querySelector('.addon-select')?.value || 0);
    const price = basePrice + addonPrice;
    const img = card.querySelector('img').src;

    const existing = cart.find(item => item.name === name && item.addonPrice === addonPrice);
    if (existing) {
      existing.quantity++;
    } else {
      cart.push({ name, price, img, quantity: 1, addonPrice });
    }
    updateCart();
  }

  function updateQuantity(i, change) {
    cart[i].quantity += change;
    if (cart[i].quantity <= 0) cart.splice(i, 1);
    updateCart();
  }

  function updateCart() {
    const list = document.getElementById('cart-list');
    list.innerHTML = '';
    let total = 0;

    cart.forEach((item, i) => {
      total += item.price * item.quantity;
      list.innerHTML += `
        <div class="cart-item">
          <img src="${item.img}" alt="${item.name}">
          <div style="flex:1;">
            <strong>${item.name}</strong><br>
            <small style="color:#666;">₱${item.price} × ${item.quantity}</small>
          </div>
          <div class="quantity-controls">
            <button onclick="updateQuantity(${i},-1)">-</button>
            <span>${item.quantity}</span>
            <button onclick="updateQuantity(${i},1)">+</button>
          </div>
        </div>`;
    });

    document.getElementById('cart-total').textContent = `Total: ₱${total.toFixed(2)}`;
    document.querySelector('.cart-count').textContent = cart.reduce((s, i) => s + i.quantity, 0) || '';
  }

  function openCart() {
    document.getElementById('cartModal').style.display = 'flex';
    updateCart();
  }

  function closeCart() {
    document.getElementById('cartModal').style.display = 'none';
  }

  // Close modals when clicking outside
  window.onclick = e => {
    if (e.target.classList.contains('modal-overlay') || e.target.classList.contains('modal')) {
      e.target.style.display = 'none';
    }
  };

  // Profile, About, Logout (keep)
  function openProfileModal() { document.getElementById('profileModal').style.display = 'flex'; }
  function closeProfileModal() { document.getElementById('profileModal').style.display = 'none'; }
  function openAboutModal() { document.getElementById('aboutModal').style.display = 'flex'; }
  function closeAboutModal() { document.getElementById('aboutModal').style.display = 'none'; }
  function openLogoutModal() { document.getElementById('logoutModal').style.display = 'flex'; }
  function closeLogoutModal() { document.getElementById('logoutModal').style.display = 'none'; }
  function confirmLogout() { window.location.href = 'login.php'; }

  // FINAL WORKING CHECKOUT BUTTON (ITO NA TALAGA!)
  document.getElementById('checkoutBtn').addEventListener('click', function() {
    if (cart.length === 0) {
      alert('Your cart is empty!');
      return;
    }

    const itemsText = cart.map(item => `${item.name} ×${item.quantity}`).join(', ');
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

    // Get customer info from PHP session (safe way!)
    const customerName = "<?= htmlspecialchars($_SESSION['fullname'] ?? 'Guest', ENT_QUOTES) ?>";
    const customerId = <?= $_SESSION['user_id'] ?? 0 ?>;

    fetch('checkout.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `items=${encodeURIComponent(itemsText)}&total=${total}&name=${encodeURIComponent(customerName)}&cid=${customerId}`
    })
    .then(response => response.text())
    .then(result => {
      if (result.trim() === 'success') {
        cart = []; // Clear cart
        updateCart();
        closeCart();
        document.getElementById('successModal').style.display = 'flex';
        setTimeout(() => {
          document.getElementById('successModal').style.display = 'none';
        }, 4000);
      } else {
       alert("SERVER RESPONSE: " + response);
      }
    })
    .catch(() => {
      alert('No internet connection or server error.');
    });
  });
</script>
</body>

</html>