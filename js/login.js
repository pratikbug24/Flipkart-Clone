function loginUser() {
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const msg = document.getElementById("msg");
  
    if (email === "" || password === "") {
      msg.style.color = "red";
      msg.innerHTML = "All fields are required.";
      return;
    }
  
    fetch(API.login, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email, password }),
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          msg.style.color = "green";
          msg.innerHTML = "Login successful!";
          setCurrentUser(data.user);
          setTimeout(() => {
            window.location.href = "home.html";
          }, 800);
        } else {
          msg.style.color = "red";
          msg.innerHTML = data.message;
        }
      })
      .catch(() => {
        msg.style.color = "red";
        msg.innerHTML = "API Error!";
      });
  }
  
  function togglePassword() {
    const pass = document.getElementById("password");
    pass.type = pass.type === "password" ? "text" : "password";
  }
  