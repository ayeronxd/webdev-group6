<?php
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="container">
  <h2>Admin Dashboard</h2>
  <div id="admin-panel">
    <a href="elections.php"><button>Manage Elections</button></a>
    <a href="positions.php"><button>Manage Positions</button></a>
    <a href="candidates.php"><button>Manage Candidates</button></a>
    <a href="results.php"><button>View Results</button></a>
    <a href="admin_logout.php"><button>Logout</button></a>
  </div>
</div>
</body>
</html>