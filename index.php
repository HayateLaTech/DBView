<?php	
	session_start();

	$mode = "";

	if (isset($_GET['destroy'])) {
		session_destroy();
		header('Location: '.$_SERVER['PHP_SELF']);
		die();
	}
	
	if (isset($_POST['host']) && isset($_POST['user']) && isset($_POST['pass'])) {
		$_SESSION['host'] = $_POST['host'];
		$_SESSION['user'] = $_POST['user'];
		$_SESSION['pass'] = $_POST['pass'];
		header('Location: '.$_SERVER['PHP_SELF']);
		die();
	}

  if (!isset($_GET['db'])) {
    if (isset($_SESSION['host']) && isset($_SESSION['user']) && isset($_SESSION['pass'])) {
      $mode = "DatabaseList";
      $mysqli = new mysqli($_SESSION['host'], $_SESSION['user'], $_SESSION['pass']);
		
			if ($mysqli->connect_errno) {
				die('<h2>Error: Couldn\'t establish database connection! (Wrong Credentials?)</h2><a href="?destroy=yes">Reload</a>');
			}

			$list = $mysqli->query("SHOW DATABASES");
    } else {
      $mode = "Splash";
    }    
  } else {
    $db = $_GET['db'];
		$mode = "TableList";
		
		$mysqli = new mysqli($host, $user, $pass);
		$mysqli->select_db($db);
		$list = $mysqli->query("SHOW TABLES");
		
	}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Table-View</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css">
  
    <style>
      .splash {
        background-color: white;
				position: absolute;
				top: 50%;
				left: 50%;
				transform: translate(-50%, -50%);
				width: 400px;
				height: 330px;
				border: black 1px solid;
				border-radius: 10px;
				text-align: center;
      }

			.form-control {
				text-align: center;
				border-left-width: 0;
				border-right-width: 0;
				border-radius: 0;
			}

			table {
				position: relative;
				width: 80% !important;
				margin: 3% 10% 0 10%;
				text-align: center;
			}

			table th {
				text-align: center;
			}

			table td {
				opacity: 0;
			}

			.selectDiv {
				z-index: 2;
				position: absolute;
				top: 1%;
				left: 50%;
				transform: translate(-50%, -0%);
				text-align: center;
				width: 600px;
			}

			select {
				width: 80%;
				border-right-width: 1px;
				border-left-width: 1px;
				margin-left: 5%;
				float: left;
			}

			.destroyDiv {
				z-index: 2;
				position: absolute;
				text-align: center;
				right:0;
				top: 0;
				background: red;
				border: 1px black solid;
				border-bottom-left-radius: 5px;
				width: 25px;
			}

			iframe {
				position: absolute;
				display: none;
				opacity: 0;
				width: 100%;
				height: 100%;
				border: none;
				padding: 0;
				margin: 0;				
			}
    </style>

		
  </head>
  <body>

		<?php if ($mode == "Splash") { ?>
			<div class="splash">
				<h1>DB-Connection</h1>
				<hr/>
				<form method="POST">
					<input type="text" class="form-control" placeholder="host-url" name="host" />
					<br/>
					<input type="text" class="form-control" placeholder="user" name="user" />
					<br/>
					<input type="password" class="form-control" placeholder="password" name="pass" />
					<hr/>
					<input type="submit" class="btn btn-primary" value="Verify"/>
				</form>
			</div>
		<?php } ?>
		<?php if ($mode == "DatabaseList") { ?>
			<table id="dbtable" class="table table-striped">
				<tr><th>DataBase</th><th>Actions</th></tr>
				<?php
					while($row = mysqli_fetch_assoc($list)) {
						echo "<tr><td>".$row['Database']."</td>";
						echo '<td><a href="?db='.$row['Database'].'" class="btn btn-primary">Show Tables</a></td></tr>';
					}
					mysqli_data_seek($list, 0);
				?>
			</table>
			<div class="selectDiv">
				<select id="select" class="form-control-sm">
					<?php
						while($row = mysqli_fetch_assoc($list)) {
							echo '<option value="'.$row['Database'].'">'.$row['Database'].'</option>';
						}
					?>
				</select>
				<button class="btn btn-success btn-sm" onclick="showTable()">View</button>
			</div>
		<?php } ?>
		<?php if ($mode == "TableList") { ?>
			<table class="table table-striped">
				<tr><th>Table</th><th>Count</th></tr>
				<?php
				while($row = mysqli_fetch_array($list)) {
						echo '<tr><td>'.$row[0].'</td>';
						$res = $mysqli->query("SELECT COUNT(*) FROM ".$row[0]);
						echo '<td>'.$res->fetch_row()[0].'</td></tr>';
				}
				?>
			</table>
		<?php } ?>
		<?php if (isset($_SESSION['host']) && !isset($_GET['db'])) { ?>
			<div class="destroyDiv">
				<a href="?destroy=yes"><font style="color: black;">X</font></a>
			</div>
		<?php } ?>


		<iframe id="frame"></iframe>


    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.20.2/TweenMax.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js"></script>

		<script>
			TweenMax.staggerFromTo("table td", 1, {x: "+100px", opacity: "0"}, {x: "0px", opacity: "1"}, 0.1);

			function showTable() {
				var e = document.getElementById("select");
				var url = "?db=" + e.options[e.selectedIndex].value;
				document.getElementById("dbtable").style.display = "none";
				document.getElementById("frame").style.display = "inline";
				document.getElementById("frame").src = url;
				TweenMax.to("iframe", 2, {opacity: 1});
			}
		</script>
  </body>
</html>
