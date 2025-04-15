# 🛒 Walmart Feature App with Grocery Points System

This is a web-based Walmart-style shopping app featuring dynamic cart functionality, user authentication, and a **points reward system** for grocery clearance items.

---

## 🔑 Key Features

### ✅ User Authentication
- Users can sign up and log in.
- After signup, users are prompted to enter their address before accessing their account.
- After signup, users are awarded 500 points

### 🛍️ Grocery & Clearance Items
- Items are grouped into **regular groceries** and **clearance items**.
- Clearance items are eligible for **reward points**.

### ⭐ Points System
- Only grocery items from the clearance section earn points.
- Each item shows the number of points that will be rewarded.
- On checkout, earned points are added to the user's account.
- Points can be redeemed on future purchases (1000 points = $1 discount).

### 🛒 Dynamic Cart
- Users can add, increase, or decrease items without page reloads.
- The cart count updates dynamically across all pages.
- Quantity controls appear as `- quantity +` once an item is added.

### 🔄 Suggestions
- Suggested grocery items are randomized from the product list.
- Suggested items exclude products already in the user's cart.
- Appears in both the **homepage** and **cart page**.

---

## 💻 Technologies Used
- PHP for backend logic and session handling
- JavaScript (Vanilla) for AJAX-powered interactions
- HTML/CSS for layout and responsive design
- MySQL for data storage
- FontAwesome for icons

---

## 📂 Folder Structure

```
├── index.php
├── cart.php
├── product-detail.php
├── clearance.php
├── login.php
├── signup.php
├── address.php
├── confirm_order.php
├── data/
│   ├── products.json
│   └── others.json
├── js/
│   ├── index.js
│   └── product.js
├── css/
│   ├── index.css
│   └── product.css
└── assets/
```

---

## 🚀 Getting Started

1. Clone this project into your local server directory.
2. Create a MySQL database and import the tables for `users` and `addresses`.
3. Modify the connection.php credential to your database credential.
4. Ensure your `products.json` contains clearance grocery items with `points`.
5. on your terminal 
```bash
php -S localhost:<port>
```
5. Launch `localhost:<port>` in your browser.

---

## 🙌 Contributing

Pull requests are welcome! If you encounter bugs or have feature suggestions, feel free to open an issue.

---

## 📄 License

This project is for educational purposes only and is not affiliated with Walmart Inc.
