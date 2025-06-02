const users = JSON.parse(localStorage.getItem('voters') || '{}');
const elections = JSON.parse(localStorage.getItem('elections') || '{}');
const votes = JSON.parse(localStorage.getItem('votes') || '{}');

const loginForm = document.getElementById('auth-form');
const toggleText = document.querySelector('.toggle');
const authContainer = document.getElementById('auth-container');
const dashboard = document.getElementById('user-dashboard');
const userNameDisplay = document.getElementById('user-name');
const electionList = document.getElementById('election-list');

let currentUser = null;
let isLogin = true;

toggleText.addEventListener('click', function() {
    isLogin = !isLogin;
    if (isLogin) {
        document.getElementById('form-title').textContent = 'Login';
        toggleText.textContent = "Don't have an account? Sign up";
    } else {
        document.getElementById('form-title').textContent = 'Sign Up';
        toggleText.textContent = "Already have an account? Log in";
    }
});

loginForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const username = document.getElementById('login-username').value.trim();
    const password = document.getElementById('login-password').value;

    if (!username || !password) {
        alert('Please fill in both fields');
        return;
    }

    if (isLogin) {
        if (users[username] === password) {
            currentUser = username;
            showDashboard();
        } else {
            alert('Wrong username or password');
        }
    } else {
        if (users[username]) {
            alert('User already exists');
        } else {
            users[username] = password;
            localStorage.setItem('voters', JSON.stringify(users));
            alert('Sign up successful! Please log in now.');
            isLogin = true;
            document.getElementById('form-title').textContent = 'Login';
            toggleText.textContent = "Don't have an account? Sign up";
        }
    }
});

function showDashboard() {
    authContainer.style.display = 'none';
    dashboard.style.display = 'block';
    userNameDisplay.textContent = currentUser;
    displayElections();
}

function displayElections() {
    electionList.innerHTML = '';

    for (const electionName in elections) {
        const election = elections[electionName];
        const electionDiv = document.createElement('div');
        electionDiv.classList.add('election');

        const title = document.createElement('h4');
        title.textContent = electionName;
        electionDiv.appendChild(title);

        for (const position in election.positions) {
            const candidates = election.positions[position];
            const positionDiv = document.createElement('div');
            positionDiv.classList.add('position');

            const positionTitle = document.createElement('p');
            positionTitle.innerHTML = '<b>' + position + '</b>';
            positionDiv.appendChild(positionTitle);

            candidates.forEach(function(candidate) {
                const btn = document.createElement('button');
                btn.textContent = 'Vote for ' + candidate;
                btn.style.margin = '5px';

                let alreadyVoted = votes && votes[currentUser] && votes[currentUser][electionName] && votes[currentUser][electionName][position];
                if (alreadyVoted) {
                    btn.disabled = true;
                    if (alreadyVoted === candidate) {
                        btn.textContent += ' (Your Vote)';
                    }
                }

                btn.addEventListener('click', function() {
                    if (!votes[currentUser]) votes[currentUser] = {};
                    if (!votes[currentUser][electionName]) votes[currentUser][electionName] = {};
                    if (votes[currentUser][electionName][position]) {
                        alert('You already voted for ' + position);
                        return;
                    }

                    votes[currentUser][electionName][position] = candidate;
                    localStorage.setItem('votes', JSON.stringify(votes));
                    displayElections();
                });

                positionDiv.appendChild(btn);
            });

            electionDiv.appendChild(positionDiv);
        }

        electionList.appendChild(electionDiv);
    }
}

function logout() {
    location.reload();
}