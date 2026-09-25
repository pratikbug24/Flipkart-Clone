const user = requireLogin();

if (user) {
  document.getElementById("profileName").innerText = user.name || "User";
  document.getElementById("profileNameField").innerText = user.name || "N/A";
  document.getElementById("profileEmail").innerText = user.email || "N/A";
  document.getElementById("profileMobile").innerText = user.mobile || "N/A";

  const initial = (user.name || user.email || "U").charAt(0).toUpperCase();
  document.getElementById("profileInitial").innerText = initial;
}

function goHome() {
  window.location.href = "home.html";
}
