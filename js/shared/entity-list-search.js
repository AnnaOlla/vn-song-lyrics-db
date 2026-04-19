function reloadEntityListPageWithNewParams(e) {
	const limitsBar = document.getElementById('limit-result-count-bar');
	const searchBar = document.getElementById('search-bar');
	
	const params = new URLSearchParams();
	
	if (limitsBar.value !== '')
		params.set('limit', limitsBar.value);
	
	if (limitsBar.value !== '')
		params.set('page', '1');
	
	if (searchBar.value !== '')
		params.set('search', searchBar.value);
	
	const url  = new URL(window.location.href);
	url.search = params.toString();
	
	// Redirect automatically
	window.location.href = url;
}
