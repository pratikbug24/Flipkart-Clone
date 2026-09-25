const user = requireLogin();
const currentUserId = user ? user.id : null;

if (user) {
  document.getElementById("userName").innerText = user.name || user.email;
  document.getElementById("welcomeName").innerText = user.name || user.email;
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

function renderProducts(products) {
  const grid = document.getElementById("productGrid");
  grid.innerHTML = "";

  if (!products.length) {
    grid.innerHTML = "<p>No products available</p>";
    return;
  }

  products.forEach((product) => {
    grid.innerHTML += `
      <div class="product-card">
        <img src="${product.image_url}" alt="${product.title}">
        <h3>${product.title}</h3>
        <p class="price">₹${product.price}</p>
        <p class="category">${product.category_name ?? ""}</p>
        <button onclick="addToCart(${product.id})">Add to Cart</button>
      </div>
    `;
  });
}

function loadProducts() {
  fetch(API.products)
    .then((res) => res.json())
    .then((data) => {
      if (!data.success) {
        throw new Error(data.message || "Failed to load products");
      }
      renderProducts(data.products);
    })
    .catch((err) => {
      console.error("Fetch error:", err);
      document.getElementById("productGrid").innerHTML =
        "<p>Unable to load products</p>";
    });
}

function addToCart(productId) {
  if (!currentUserId) {
    alert("You must be logged in to add items to the cart.");
    return;
  }

  fetch(API.addToCart, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ product_id: productId, user_id: currentUserId }),
  })
    .then((res) => res.json())
    .then((data) => {
      alert(data.success ? `✅ ${data.message}` : `❌ ${data.message}`);
    })
    .catch((error) => {
      console.error("Cart fetch error:", error);
      alert("Error connecting to the cart service.");
    });
}

document.addEventListener("DOMContentLoaded", loadProducts);
