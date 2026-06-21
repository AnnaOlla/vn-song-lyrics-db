// Dependencies:
// -- prepare-entity-name-for-filtering.js

function filterSections(e) {
	const filterBar   = e.target;
	const filterValue = prepareEntityNameForFiltering(filterBar.value);
	const entities    = document.getElementsByClassName('list-entity');
	
	for (const entity of entities) {
		const names = entity.getElementsByClassName('entity-name');
		let isMatch = false;
		
		for (const name of names) {
			const content = prepareEntityNameForFiltering(name.textContent);
			const re      = new RegExp(filterValue, 'i');
			
			isMatch = isMatch || re.test(content);
		}
		
		if (isMatch) {
			entity.style.display = '';
		} else {
			entity.style.display = 'none';
		}
	}
}
