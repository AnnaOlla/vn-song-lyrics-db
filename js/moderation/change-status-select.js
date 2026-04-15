async function sendStatus(e) {
	// If the select has the 'data-entity-uri' then the relationship must be updated
	let entityUri = e.target.getAttribute('data-entity-uri');
	let url = '';
	
	if (entityUri) {
		const currentUriPart   = window.location.pathname.split('/');
		const language         = currentUriPart[1];
		
		const firstEntityType  = currentUriPart[2];
		const firstEntityName  = currentUriPart[3];
		
		const selectedUriPart  = entityUri.split('/');
		const secondEntityType = selectedUriPart[2];
		const secondEntityName = selectedUriPart[3];
		
		url = '/' + language + '/' + firstEntityType + '-' + secondEntityType + '-relation' +
		                       '/' + firstEntityName + '/' + secondEntityName + '/change-status';
	} else {
		url = window.location.pathname + '/change-status';
	}
	
	const statusValue = e.target.value;
	
	const formData = new FormData();
	formData.append('status', statusValue);
	
	const response = await fetch(url, {
		method: "POST",
		body: formData
	});
	
	if (response.ok) {
		e.target.style.backgroundColor = '#7CBF5F';
	} else {
		const echo = await response.text();
		alert(response.status + ': ' + echo);
		e.target.style.backgroundColor = '#BF5F5F';
	}
}

/* function main() */ {
	const statusSelects = document.querySelectorAll('select.status-select');
	
	for (statusSelect of statusSelects) {
		statusSelect.addEventListener('change', sendStatus);
	}
}
