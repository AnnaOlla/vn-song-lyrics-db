// User may enter the name romanized differently
function prepareInput(anyString) {
	return anyString.replaceAll(' ', '');
}

function switchSectionsVisibility(e) {
	const searchBar   = e.target;
	const searchValue = prepareInput(searchBar.value);
	const entities    = document.getElementsByClassName('list-entity');
	
	if (searchValue === '') {
		for (const entity of entities) {
			entity.style.display = '';
		}
		return;
	}
	
	for (const entity of entities) {
		const names = entity.getElementsByClassName('entity-name');
		let isMatch = false;
		
		for (const name of names) {
			const content = prepareInput(name.textContent);
			const re      = new RegExp(searchValue, 'i');
			
			isMatch = isMatch || re.test(content);
		}
		
		if (isMatch) {
			entity.style.display = '';
		} else {
			entity.style.display = 'none';
		}
	}
}

const searchBar = document.getElementById('search-bar');
searchBar.addEventListener('input', switchSectionsVisibility);
