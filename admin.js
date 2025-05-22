function getUsers() {
  var data = localStorage.getItem('users');
  var users = {};
  if (!data) return users;
  var pairs = data.split(';');
  for (var i = 0; i < pairs.length; i++) {
    var pair = pairs[i];
    if (pair) {
      var idx = pair.indexOf(':');
      if (idx !== -1) {
        var user = pair.substring(0, idx);
        var pass = pair.substring(idx + 1);
        users[user] = pass;
      }
    }
  }
  return users;
}

function saveUsers(users) {
  var arr = [];
  for (var user in users) {
    arr.push(user + ':' + users[user]);
  }
  localStorage.setItem('users', arr.join(';'));
}

//LOCAL STORAGE MUNA HABANG WALA PANG DATABASE

function getElections() {
  var data = localStorage.getItem('elections');
  var elections = {};
  if (!data) return elections;
  var items = data.split('|');
  for (var i = 0; i < items.length; i++) {
    var item = items[i];
    if (item) {
      var idx = item.indexOf(':');
      if (idx !== -1) {
        var name = item.substring(0, idx);
        var posStr = item.substring(idx + 1);
        var posArr = posStr ? posStr.split(',') : [];
        elections[name] = { positions: {} };
        for (var j = 0; j < posArr.length; j++) {
          if (posArr[j]) elections[name].positions[posArr[j]] = [];
        }
      }
    }
  }
  return elections;
}

function saveElections(elections) {
  var arr = [];
  for (var name in elections) {
    var posArr = [];
    var positions = elections[name].positions || {};
    for (var pos in positions) {
      posArr.push(pos);
    }
    arr.push(name + ':' + posArr.join(','));
  }
  localStorage.setItem('elections', arr.join('|'));
}

function getCandidates() {
  var data = localStorage.getItem('candidates');
  var candidates = {};
  if (!data) return candidates;
  var items = data.split('|');
  for (var i = 0; i < items.length; i++) {
    var item = items[i];
    if (item) {
      var idx1 = item.indexOf('|');
      var idx2 = item.indexOf(':');
      if (idx1 !== -1 && idx2 !== -1 && idx2 > idx1) {
        var election = item.substring(0, idx1);
        var position = item.substring(idx1 + 1, idx2);
        var names = item.substring(idx2 + 1).split(',');
        if (!candidates[election]) candidates[election] = {};
        candidates[election][position] = [];
        for (var j = 0; j < names.length; j++) {
          if (names[j]) candidates[election][position].push(names[j]);
        }
      }
    }
  }
  return candidates;
}

function saveCandidates(candidates) {
  var arr = [];
  for (var election in candidates) {
    for (var position in candidates[election]) {
      arr.push(election + '|' + position + ':' + candidates[election][position].join(','));
    }
  }
  localStorage.setItem('candidates', arr.join('|'));
}

var users = getUsers();
var elections = getElections();
var candidates = getCandidates();

function signup() {
  var user = document.getElementById('username').value;
  var pass = document.getElementById('password').value;
  if (users[user]) {
    alert('User already exists.');
    return;
  }
  users[user] = pass;
  saveUsers(users);
  alert('Signed up! You can log in now.');
}

function login() {
  var user = document.getElementById('username').value;
  var pass = document.getElementById('password').value;
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
  var name = document.getElementById('election-name').value;
  if (!name) {
    alert('Election name required.');
    return;
  }
  if (!elections[name]) {
    elections[name] = { positions: {} };
  }
  saveElections(elections);
  document.getElementById('election-name').value = '';
  loadElections();
}

function loadElections() {
  var list = document.getElementById('election-list');
  var select = document.getElementById('election-select');
  list.innerHTML = '';
  select.innerHTML = '';

  for (var name in elections) {
    var option = document.createElement('option');
    option.value = name;
    option.textContent = name;
    select.appendChild(option);

    var li = document.createElement('li');
    li.textContent = name;

    var removeBtn = document.createElement('button');
    removeBtn.textContent = 'Remove';
    removeBtn.style.marginLeft = '10px';
    removeBtn.style.backgroundColor = '#e53935';
    removeBtn.style.color = 'white';
    removeBtn.onclick = (function(n) {
      return function() {
        if (confirm('Are you sure you want to delete election "' + n + '"?')) {
          delete elections[n];
          saveElections(elections);
          if (candidates[n]) {
            delete candidates[n];
            saveCandidates(candidates);
          }
          loadElections();
          document.getElementById('position-list').innerHTML = '';
          document.getElementById('candidate-list').innerHTML = '';
        }
      };
    })(name);

    li.appendChild(removeBtn);
    list.appendChild(li);
  }

  if (select.options.length > 0) {
    select.selectedIndex = 0;
    loadPositions();
  }
}

function addPosition() {
  var election = document.getElementById('election-select').value;
  var name = document.getElementById('position-name').value.trim();

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
  saveElections(elections);
  document.getElementById('position-name').value = '';
  loadPositions();
}

function loadPositions() {
  var election = document.getElementById('election-select').value;
  var list = document.getElementById('position-list');
  var select = document.getElementById('position-select');

  list.innerHTML = '';
  select.innerHTML = '';

  if (!election) return;

  var positions = elections[election].positions || {};

  for (var position in positions) {
    var li = document.createElement('li');
    li.textContent = position;

    var removeBtn = document.createElement('button');
    removeBtn.textContent = 'Remove';
    removeBtn.style.marginLeft = '10px';
    removeBtn.style.backgroundColor = '#e53935';
    removeBtn.style.color = 'white';
    removeBtn.onclick = (function(pos) {
      return function() {
        if (confirm('Are you sure you want to delete position "' + pos + '"?')) {
          delete elections[election].positions[pos];
          saveElections(elections);
          if (candidates[election] && candidates[election][pos]) {
            delete candidates[election][pos];
            saveCandidates(candidates);
          }
          loadPositions();
          document.getElementById('candidate-list').innerHTML = '';
        }
      };
    })(position);

    li.appendChild(removeBtn);
    list.appendChild(li);

    var option = document.createElement('option');
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
  var election = document.getElementById('election-select').value;
  var position = document.getElementById('position-select').value;
  var name = document.getElementById('candidate-name').value.trim();

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

  if (!candidates[election]) candidates[election] = {};
  if (!candidates[election][position]) candidates[election][position] = [];

  if (candidates[election][position].indexOf(name) !== -1) {
    alert("Candidate already exists for this position.");
    return;
  }

  candidates[election][position].push(name);
  saveCandidates(candidates);
  document.getElementById('candidate-name').value = '';
  loadCandidates();
}

function loadCandidates() {
  var election = document.getElementById('election-select').value;
  var position = document.getElementById('position-select').value;
  var list = document.getElementById('candidate-list');

  list.innerHTML = '';

  if (!election || !position) return;

  var arr = [];
  if (candidates[election] && candidates[election][position]) {
    arr = candidates[election][position];
  }

  for (var i = 0; i < arr.length; i++) {
    (function(candidate, index) {
      var li = document.createElement('li');
      li.textContent = candidate;

      var removeBtn = document.createElement('button');
      removeBtn.textContent = 'Remove';
      removeBtn.style.marginLeft = '10px';
      removeBtn.style.backgroundColor = '#e53935';
      removeBtn.style.color = 'white';
      removeBtn.onclick = function() {
        if (confirm('Are you sure you want to remove candidate "' + candidate + '"?')) {
          candidates[election][position].splice(index, 1);
          saveCandidates(candidates);
          loadCandidates();
        }
      };

      li.appendChild(removeBtn);
      list.appendChild(li);
    })(arr[i], i);
  }
}