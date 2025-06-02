const users = JSON.parse(localStorage.getItem('users') || '{}');
const elections = JSON.parse(localStorage.getItem('elections') || '{}');

/* LOCAL STORAGE MUNA HABANG WALA PA TAYO PHP */

function signup() {
const user = document.getElementById('username').value;
const pass = document.getElementById('password').value;
if (users[user]) return alert('User already exists.');
users[user] = pass;
localStorage.setItem('users', JSON.stringify(users));
alert('Signed up! You can log in now.');
}

function login() {
const user = document.getElementById('username').value;
const pass = document.getElementById('password').value;
if (users[user] === pass) {
document.getElementById('auth-forms').classList.add('hidden');
document.getElementById('admin-panel').classList.remove('hidden');
document.getElementById('admin-username').textContent = user;
loadElections();
} else {
alert('Invalid log in or User does not exist');
}
}

function logout() {
location.reload();
}

function createElection() {
const name = document.getElementById('election-name').value;
if (!name) return alert('Election name required.');
if (!elections[name]) {
elections[name] = {
positions: {},
};
}
localStorage.setItem('elections', JSON.stringify(elections));
document.getElementById('election-name').value = '';
loadElections();
}

function loadElections() {
const list = document.getElementById('election-list');
const select = document.getElementById('election-select');
list.innerHTML = '';
select.innerHTML = '';

for (let name in elections) {
const option = document.createElement('option');
option.value = name;
option.textContent = name;
select.appendChild(option);

const li = document.createElement('li');  
li.textContent = name;  

const removeBtn = document.createElement('button');  
removeBtn.textContent = 'Remove';  
removeBtn.style.marginLeft = '10px';  
removeBtn.style.backgroundColor = '#e53935';  
removeBtn.style.color = 'white';  
removeBtn.onclick = () => {  
  if (confirm(`Are you sure you want to delete election "${name}"?`)) {  
    delete elections[name];  
    localStorage.setItem('elections', JSON.stringify(elections));  
    loadElections();  
    document.getElementById('position-list').innerHTML = '';  
    document.getElementById('candidate-list').innerHTML = '';  
  }  
};  

li.appendChild(removeBtn);  
list.appendChild(li);

}

if (select.options.length > 0) {
select.selectedIndex = 0;
loadPositions();
}
}

function addPosition() {
const election = document.getElementById('election-select').value;
const name = document.getElementById('position-name').value.trim();

if (!election) {
alert("Please select an election first.");
return;
}

if (!name) {
alert("Position name is required.");
return;
}

if (!elections[election].positions) {
elections[election].positions = {};
}

if (elections[election].positions[name]) {
alert("Position already exists.");
return;
}

elections[election].positions[name] = [];
localStorage.setItem('elections', JSON.stringify(elections));
document.getElementById('position-name').value = '';
loadPositions();
}

function loadPositions() {
const election = document.getElementById('election-select').value;
const list = document.getElementById('position-list');
const select = document.getElementById('position-select');

list.innerHTML = '';
select.innerHTML = '';

if (!election) return;

const positions = elections[election].positions || {};

for (let position in positions) {
const li = document.createElement('li');
li.textContent = position;

const removeBtn = document.createElement('button');  
removeBtn.textContent = 'Remove';  
removeBtn.style.marginLeft = '10px';  
removeBtn.style.backgroundColor = '#e53935';  
removeBtn.style.color = 'white';  
removeBtn.onclick = () => {  
  if (confirm(`Are you sure you want to delete position "${position}"?`)) {  
    delete elections[election].positions[position];  
    localStorage.setItem('elections', JSON.stringify(elections));  
    loadPositions();  
    document.getElementById('candidate-list').innerHTML = '';  
  }  
};  
  
li.appendChild(removeBtn);  
list.appendChild(li);  
  
const option = document.createElement('option');  
option.value = position;  
option.textContent = position;  
select.appendChild(option);

}

if (select.options.length > 0) {
select.selectedIndex = 0;
loadCandidates();
} else {
document.getElementById('candidate-list').innerHTML = '';
}
}

function addCandidate() {
const election = document.getElementById('election-select').value;
const position = document.getElementById('position-select').value;
const name = document.getElementById('candidate-name').value.trim();

if (!election) {
alert("Please select an election first.");
return;
}

if (!position) {
alert("Please select a position first.");
return;
}

if (!name) {
alert("Candidate name is required.");
return;
}

if (elections[election].positions[position].includes(name)) {
alert("Candidate already exists for this position.");
return;
}

elections[election].positions[position].push(name);
localStorage.setItem('elections', JSON.stringify(elections));
document.getElementById('candidate-name').value = '';
loadCandidates();
}

function loadCandidates() {
const election = document.getElementById('election-select').value;
const position = document.getElementById('position-select').value;
const list = document.getElementById('candidate-list');

list.innerHTML = '';

if (!election || !position) return;

const candidates = elections[election].positions[position] || [];

candidates.forEach((candidate, index) => {
const li = document.createElement('li');
li.textContent = candidate;

const removeBtn = document.createElement('button');  
removeBtn.textContent = 'Remove';  
removeBtn.style.marginLeft = '10px';  
removeBtn.style.backgroundColor = '#e53935';  
removeBtn.style.color = 'white';  
removeBtn.onclick = () => {  
  if (confirm(`Are you sure you want to remove candidate "${candidate}"?`)) {  
    elections[election].positions[position].splice(index, 1);  
    localStorage.setItem('elections', JSON.stringify(elections));  
    loadCandidates();  
  }  
};  
  
li.appendChild(removeBtn);  
list.appendChild(li);

});
}