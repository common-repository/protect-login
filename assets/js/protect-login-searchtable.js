/**
 * Helper to make Tables searchable
 *
 * @package Protect Login
 */

if ( false ) {
	console.log( 'File loaded' );
}

function protectLoginSearchtable(tableId, searchField) {
	var input  = searchField;
	var filter = input.value.toUpperCase();
	var table  = document.getElementById( tableId );
	var rows   = Array.from( table.getElementsByTagName( "tr" ) );

	rows.forEach(
		function (row) {
			protect_login_filterRow( row, filter );
		}
	);
}

function protect_login_filterRow(row, filter) {
	var cells         = Array.from( row.getElementsByTagName( "td" ) );
	var shouldDisplay = cells.some(
		function (cell) {
			var cellText = cell.textContent || cell.innerText;
			return cellText.toUpperCase().indexOf( filter ) > -1;
		}
	);

	row.style.display = shouldDisplay ? "" : "none";
}
