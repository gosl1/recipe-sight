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
    const formDetails = new FormData();
    formDetails.append('email', document.getElementById('loginEmail').value);
    formDetails.append('password', document.getElementById('loginPassword').value);

    const res = await fetch('../database/login.php', {
        method: 'POST',
        body: formDetails
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
    const formDetails = new FormData();
    formDetails.append('username', document.getElementById('signupName').value);
    formDetails.append('email', document.getElementById('signupEmail').value);
    formDetails.append('password', document.getElementById('signupPassword').value);

    const res = await fetch('../database/signup.php', {
        method: 'POST',
        body: formDetails
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
    console.log('signup response:', JSON.stringify(data));
}

function logout() {
    sessionStorage.removeItem('user');
    location.reload();
}