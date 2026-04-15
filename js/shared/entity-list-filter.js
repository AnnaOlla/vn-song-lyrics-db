// User may enter the name romanized differently, remove all whitespaces
function prepareInputForFiltering(anyString) {
	return anyString.replaceAll(/\s/g, '');
}

function filterSections(e) {
	const filterBar   = e.target;
	const filterValue = prepareInputForFiltering(filterBar.value);
	const entities    = document.getElementsByClassName('list-entity');
	
	for (const entity of entities) {
		const names = entity.getElementsByClassName('entity-name');
		let isMatch = false;
		
		for (const name of names) {
			const content = prepareInputForFiltering(name.textContent);
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
