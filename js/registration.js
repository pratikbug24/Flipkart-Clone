function togglePassword() {
    const pass = document.getElementById("password");
    pass.type = pass.type === "password" ? "text" : "password";
  }
  
  function registerUser() {
    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();
    const mobile = document.getElementById("mobile").value.trim();
    const password = document.getElementById("password").value.trim();
  
    if (!name || !email || !mobile || !password) {
      alert("All fields are required!");
      return;
    }
  
    if (!/^[0-9]{10}$/.test(mobile)) {
      alert("Enter a valid 10-digit mobile number!");
      return;
    }
  
    if (password.length < 6) {
      alert("Password must be at least 6 characters!");
      return;
    }
  
    fetch(API.register, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ name, email, mobile, password }),
    })
      .then((res) => res.json())
      .then((data) => {
        alert(data.message);
        if (data.success) {
          window.location.href = "login.html";
        }
      })
      .catch(() => alert("Unable to reach the registration service."));
  }
  