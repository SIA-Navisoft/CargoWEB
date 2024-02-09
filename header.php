<!DOCTYPE html>
<html lang="lv">
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1,IE=9" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<META HTTP-EQUIV="refresh" CONTENT="86400;URL=<?=$companyWeb;?>">
<title><?=$companyName;?></title>

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/bootstrap-select.css">
<link rel="stylesheet" href="css/menu.css">
<link rel="stylesheet" href="css/footer.css">
<link rel="stylesheet" href="css/cbox.css">

<link rel="stylesheet" href="css/bootstrap-datetimepicker.css">

<script src="js/jquery-3.2.1.js"></script>
<script src="js/jquery-ui.js"></script> 
<script src="js/bootstrap.min.js"></script>

<script src="js/cbx.js"></script>
<script src="js/short.js"></script>



<script src="js/charts.js"></script>
<script src="js/bootstrap-select.js"></script>
<script src="js/numbersOnly.js"></script>
<script src="js/paging.js"></script>

<script src="js/Moment.js"></script>
<script src="js/bootstrap-datetimepicker.js"></script>
<script>
history.pushState(null, null, location.href);
    window.onpopstate = function () {
        history.go(1);
    };
</script>

<style>
#toTop {
	display: none;
	position: fixed;
	bottom: 20px;
	right: 30px;
	z-index: 99;
	border: 1;
	outline: none;
	background-color: #ddd;
	color: white;
	cursor: pointer;
	padding: 15px;
	border-radius: 10px;
}

#toTop:hover {
	background-color: white;
}
</style>

<style>
#pleaseWait
{  
	opacity:0.5;
	background-color:#000;
	color:#fff;
	position:fixed;
	width:100%;
	height:100%;
	top:0px;
	left:0px;
	z-index:1000;
}

.input-xs{
   height: 20px;
   line-height: 1.5;
   font-size: 12px;
   padding: 1px 5px;
   border-radius: 3px;
}
.form-group {
  margin-bottom: 0;
}

label {
  font-size: 12px;
  margin-bottom: 0;
}

.lb-md {
  font-size: 16px;
}

.lb-lg {
  font-size: 20px;
}

.table-sm {
  th,
  td {
    padding: 0;
  }
}

input[type], .bootstrap-select {
    height: 20px;
    line-height: 1.5;
    font-size: 12px;
    padding: 1px 5px;
    border-radius: 3px;
}

.table td, .table th {
	padding: 2px !important;
	font-size: 11px;
}

.icon-flipped {
-webkit-transform: rotate(180deg);
-moz-transform: rotate(180deg);
-ms-transform: rotate(180deg);
-o-transform: rotate(180deg);
transform: rotate(180deg);
filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=2);
}
</style>

<button onclick="topFunction()" id="toTop" title="Uz augšu!" class="btn btn-default btn-lg">
<span style="color: #00B5AD;" class="glyphicon glyphicon-circle-arrow-up" aria-hidden="true"></span>
</button>

<script>
// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
	if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
		document.getElementById("toTop").style.display = "block";
	} else {
		document.getElementById("toTop").style.display = "none";
	}
}

// When the user clicks on the button, scroll to the top of the document
function topFunction() {
	document.body.scrollTop = 0;
	document.documentElement.scrollTop = 0;
}
</script>


</head>
<body>

<nav class="navbar navbar-default navbar-sm navbar-inverse navbar-static-top" role="navigation">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
			data-target="#bs-example-navbar-collapse-1">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="welcome"><i class="glyphicon glyphicon-home"></i></a>
			<p class="navbar-text" style="color: white;"><b><?=$companyName;?></b> selfservice</p>
		</div>

		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

			<ul class="nav navbar-nav navbar-right">
				<li>
					<a style="color: white;"><?=$fullname;?></a>
				</li>

				<li>
					<a href="settings"><i class="glyphicon glyphicon-user"></i></a>
				</li>
				<li>
					<a href="logout"><i class="glyphicon glyphicon-off"></i></a>
				</li>			

			</ul>



		</div>
		
	</div>
	
</nav>

