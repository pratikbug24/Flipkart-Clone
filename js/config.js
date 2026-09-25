// Shared configuration and helpers loaded on every page.
// Update API_BASE if the project is served from a different folder/host.
const API_BASE = "http://localhost/Flipkart-Clone";

const API = {
  login: `${API_BASE}/api/login.php`,
  register: `${API_BASE}/api/register.php`,
  products: `${API_BASE}/api/get_products.php`,
  cartItems: `${API_BASE}/api/get_cart_items.php`,
  addToCart: `${API_BASE}/api/add_to_cart.php`,
};

function getCurrentUser() {
  const raw = localStorage.getItem("user");
  return raw ? JSON.parse(raw) : null;
}

function setCurrentUser(user) {
  localStorage.setItem("user", JSON.stringify(user));
}

function requireLogin() {
  const user = getCurrentUser();
  if (!user) {
    window.location.href = "login.html";
    return null;
  }
  return user;
}

function logout() {
  localStorage.removeItem("user");
  window.location.href = "login.html";
}
