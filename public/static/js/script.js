const loginBtn = document.getElementById("loginBtn");
const registerBtn = document.getElementById("registerBtn");
const loginForm = document.getElementById("loginForm");
const registerForm = document.getElementById("registerForm");

function showForm(type) {
  loginForm.classList.remove("active");
  registerForm.classList.remove("active");
  setTimeout(() => {
    const alert = document.querySelector('.alert');
    if (alert) {
        alert.classList.add('fade-out');
        setTimeout(() => {
            alert.remove(); 
        }, 500); 
    }
}, 4000); 

  setTimeout(() => {
    if (type === "login") {
      loginForm.style.display = "flex";
      registerForm.style.display = "none";
      loginBtn.classList.add("active");
      registerBtn.classList.remove("active");

      setTimeout(() => loginForm.classList.add("active"), 20);
    } else {
      loginForm.style.display = "none";
      registerForm.style.display = "flex";
      registerBtn.classList.add("active");
      loginBtn.classList.remove("active");

      setTimeout(() => registerForm.classList.add("active"), 20);
    }
  }, 200);
}

loginBtn.onclick = () => showForm("login");
registerBtn.onclick = () => showForm("register");
showForm("login");