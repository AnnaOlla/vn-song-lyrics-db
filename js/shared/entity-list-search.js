function reloadEntityListPageWithNewParams(e) {
	const limitsBar = document.getElementById('limit-result-count-bar');
	const searchBar = document.getElementById('search-bar');
	
	let params = [];
	
	if (limitsBar.value !== '')
		params.push('limit=' + encodeURIComponent(limitsBar.value));
	
	if (limitsBar.value !== '')
		params.push('page=' + encodeURIComponent('1'));
	
	if (searchBar.value !== '')
		params.push('search=' + encodeURIComponent(searchBar.value));
	
	if (params.length !== 0)
		params = '?' + params.join('&');
	else
		params = '';
	
	const url = window.location.origin + window.location.pathname + params;
	
	// Redirect automatically
	window.location.href = url;
}
