const user = requireLogin();
const currentUserId = user ? user.id : null;

if (!currentUserId) {
  alert("User ID not found. Please log in again.");
  window.location.href = "login.html";
}

function toggleDropdown() {
  const dropdown = document.getElementById("dropdown");
  dropdown.style.display =
    dropdown.style.display === "block" ? "none" : "block";
}

window.addEventListener("click", (event) => {
  if (!event.target.closest(".user-area")) {
    document.getElementById("dropdown").style.display = "none";
  }
});

function resetTotals(list) {
  list.innerHTML = "<p>Your cart is empty!</p>";
  document.getElementById("cartCount").innerText = 0;
  document.getElementById("totalItems").innerText = 0;
  document.getElementById("subTotal").innerText = "₹0.00";
  document.getElementById("totalAmount").innerText = "₹0.00";
}

function renderCartItem(item) {
  const mrp = item.mrp || (item.price * 1.2).toFixed(2);
  const discount = item.mrp
    ? (((item.mrp - item.price) / item.mrp) * 100).toFixed(0)
    : 15;

  return `
    <div class="cart-item-card">
      <div class="item-image">
        <img src="${item.image_url}" alt="${item.title}">
      </div>
      <div class="item-details">
        <h3>${item.title}</h3>
        <p class="price">
          <span class="mrp">₹${mrp}</span> ₹${item.price}
          <span class="discount">${discount}% Off</span>
        </p>
        <div class="quantity-control">
          <button onclick="updateQuantity(${item.product_id}, ${item.quantity - 1})">-</button>
          <input type="text" value="${item.quantity}" readonly>
          <button onclick="updateQuantity(${item.product_id}, ${item.quantity + 1})">+</button>
          <button class="remove-btn" onclick="removeItem(${item.product_id})">REMOVE</button>
        </div>
      </div>
    </div>
  `;
}

function fetchCartItems() {
  fetch(`${API.cartItems}?user_id=${currentUserId}`)
    .then((res) => res.json())
    .then((data) => {
      const list = document.getElementById("cartItemsList");

      if (!data.success || data.items.length === 0) {
        resetTotals(list);
        return;
      }

      const totalProducts = data.items.reduce(
        (sum, item) => sum + parseInt(item.quantity, 10),
        0,
      );

      document.getElementById("cartCount").innerText = totalProducts;
      document.getElementById("totalItems").innerText = totalProducts;
      document.getElementById("subTotal").innerText = `₹${data.subtotal}`;
      document.getElementById("totalAmount").innerText = `₹${data.subtotal}`;

      list.innerHTML = data.items.map(renderCartItem).join("");
    })
    .catch((error) => {
      console.error("Error fetching cart:", error);
      document.getElementById("cartItemsList").innerHTML =
        "<p>Error loading cart items. Please try again.</p>";
    });
}

function updateQuantity(productId, newQuantity) {
  alert(`Changing quantity of Product ${productId} to ${newQuantity}.`);
}

function removeItem(productId) {
  alert(`Removing Product ${productId}.`);
}

document.addEventListener("DOMContentLoaded", fetchCartItems);
