function loadSearchEntityPage(e) {
	const entityBar = document.getElementById('search-entity-select');
	const searchBar = document.getElementById('search-bar');
	
	if (searchBar.value === '') {
		return;
	}
	
	const params = new URLSearchParams();
	params.set('limit', '10');
	params.set('page', '1');
	params.set('search', searchBar.value);
	
	const url  = new URL(window.location.href + '/' + entityBar.value);
	url.search = params.toString();
	
	// Redirect automatically
	window.location.href = url;
}

/* function main() */ {
	const searchButton = document.getElementById('search-bar-button');
	searchButton.addEventListener('click', loadSearchEntityPage);
}
