const user = JSON.parse(sessionStorage.getItem('user'));
if (user) showUserView(user);

function showUserView(user) {
    document.getElementById('guestView').style.display = 'none';
    document.getElementById('userView').style.display = 'block';
    document.getElementById('displayUsername').textContent = user.username;
    document.getElementById('displayEmail').textContent = user.email;
}

function showLogin() {
    document.getElementById('loginForm').style.display = 'block';
    document.getElementById('signupForm').style.display = 'none';
}

function showSignup() {
    document.getElementById('signupForm').style.display = 'block';
    document.getElementById('loginForm').style.display = 'none';
}

async function login() {
    const formData = new FormData();
    formData.append('email', document.getElementById('loginEmail').value);
    formData.append('password', document.getElementById('loginPassword').value);

    const res = await fetch('login.php', {
        method: 'POST',
        body: formData
    });

    const data = await res.text();
    if (data === 'fail') {
        alert('Invalid email or password');
    } else {
        const [id, username, email] = data.split('|');
        const user = { id, username, email };
        sessionStorage.setItem('user', JSON.stringify(user));
        showUserView(user);
    }
}

async function signup() {
    const formData = new FormData();
    formData.append('username', document.getElementById('signupUsername').value);
    formData.append('email', document.getElementById('signupEmail').value);
    formData.append('password', document.getElementById('signupPassword').value);

    const res = await fetch('signup.php', {
        method: 'POST',
        body: formData
    });

    const data = await res.text();
    if (data === 'fail') {
        alert('Signup failed. Email may already be in use.');
    } else {
        const [id, username, email] = data.split('|');
        const user = { id, username, email };
        sessionStorage.setItem('user', JSON.stringify(user));
        showUserView(user);
    }
}

function logout() {
    sessionStorage.removeItem('user');
    location.reload();
}