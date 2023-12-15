<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<script src="script.js"></script>
		<link rel="stylesheet" href="main.css" />
		<title>My Open Data Assignment</title>
	</head>
	<body>
		<header>
			<h1>Content Management Blog</h1>
		</header>
		<main>
			<form id="searchBar">
				<label for="street">Enter a post</label>
				<br>
				<input name="street" id="streetInput">
				<button type="submit" id="searchButton">Search</button>
			</form>
			<h2 id="noResultsMessage"></h2>
			<div id="tableDiv">
				<table id="resultsTable" cellspacing="0" cellpadding="0">
					<thead>
						<tr>
							<th>Violation</th>
							<th>Full Fine</th>
							<th>Discounted Fine</th>
							<th>Date Issued</th>
						</tr>
					</thead>
					<tbody id="resultsData"></tbody>
				</table>
			</div>
		</main>
		<footer>
			<p>
				Parking ticket data provided by the City of Winnipeg Open Data catalogue at <a href="https://data.winnipeg.ca/">data.winnipeg.ca</a>.
			</p>
		</footer>
	</body>
</html>