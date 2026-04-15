/* function main() */ {
	const limitResultCountBar = document.getElementById('limit-result-count-bar');
	limitResultCountBar.addEventListener('change', reloadEntityListPageWithNewParams);

	const searchBarButton = document.getElementById('search-bar-button');
	searchBarButton.addEventListener('click', reloadEntityListPageWithNewParams);
	
	const filterBar = document.getElementById('filter-bar');
	filterBar.addEventListener('input', filterSections);
}
