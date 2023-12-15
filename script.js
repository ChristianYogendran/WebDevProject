/*
 * This function takes the json and builds html rows to append to the table.
 */
function buildTicketTable(tickets, streetName) {
    const noResultsMessage = document.getElementById("noResultsMessage");
    const ticketTable = document.getElementById("resultsTable");
    //Clear any old table rows
    const ticketTableRows = document.getElementById("resultsData");
    ticketTableRows.innerHTML = '';
    //Display a message when no rows are returned by the query and hide the table
    if (tickets.length == 0) {
        noResultsMessage.innerHTML = `WOW! No tickets have been issued on ${streetName}! (but your spelling might be wrong)`;
        noResultsMessage.style.display = "block";
        ticketTable.style.display = "none";
    }//END IF
    else {
        //hide message and show the table
        noResultsMessage.style.display = "none";
        ticketTable.style.display = "block";
        for (let ticket of tickets) {
            //make a new row using the data from json
            var newRow = document.createElement('tr');
            var violation = document.createElement('td');
            var fullFine = document.createElement('td');
            var discountedFine = document.createElement('td');
            var dateIssued = document.createElement('td');
            violation.innerHTML = ticket.violation;
            fullFine.innerHTML = ticket.full_fine;
            discountedFine.innerHTML = ticket.discounted_fine;
            dateIssued.innerHTML = ticket.issue_date.substring(0, 10);
            //append data to row, then append row to table
            newRow.appendChild(violation);
            newRow.appendChild(fullFine);
            newRow.appendChild(discountedFine);
            newRow.appendChild(dateIssued);
            ticketTableRows.appendChild(newRow);
        }//END FOR
    }//END ELSE
}//END FUNCTION buildTicketTable

/*
 * This function handles the event of clicking the search button. 
 * If the search box isn't empty, it will try to fetch data from the City of Winnipeg Open Data site.
 */
function searchForTickets(event) {
    event.preventDefault();
    //Clear any old table rows
    document.getElementById("resultsData").innerHTML = '';
    //Get and trim the value in the search box
    const streetName = document.getElementById("streetInput").value.trim();
    //Check if it's empty
    if (streetName !== '') {
        //fetch data using a fancy SoQL query embedded in the _GET, then send that data to another function in an AJAXy way.
        fetch(encodeURI('https://data.winnipeg.ca/resource/bhrt-29rb.json?' + `$where=lower(street) LIKE lower('%${streetName}%')` + '&$order=issue_date DESC&$limit=100'))
            .then(response => response.json())
            .then(tickets => buildTicketTable(tickets, streetName));
    }//END IF
    else {
        const noResultsMessage = document.getElementById("noResultsMessage");
        noResultsMessage.innerHTML = 'OOPS! You forgot to type the street name!';
        noResultsMessage.style.display = "block";
        document.getElementById("resultsTable").style.display = "none";
    }
}//END FUNCTION searchForTickets

/*
 * Handles the load event of the document.
 */
function load() {
    //Hide the table and noResultsMessage by default
    document.getElementById("noResultsMessage").style.display = "none";
    document.getElementById("resultsTable").style.display = "none";
	//Add event listener for the form submit
	document.getElementById("searchBar").addEventListener("submit", searchForTickets);
}//END FUNCTION load

// Add document load event listener
document.addEventListener("DOMContentLoaded", load);