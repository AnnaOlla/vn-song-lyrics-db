function setInputProperties(e) {
	const nameSelect = e.target;
	const columnId = nameSelect.getAttribute('data-column');
	
	const dataType = nameSelect.options[nameSelect.selectedIndex].getAttribute('data-type');
	const required = nameSelect.options[nameSelect.selectedIndex].getAttribute('data-required');
	
	const inputs = document.querySelectorAll('[data-column-id="' + columnId + '"]');
	
	for (const input of inputs) {
		input.name = dataType;
		
		if (required === 'true')
			input.required = true;
		else
			input.required = false;
		
		if (dataType === 'transliterated-name[]')
			input.pattern = "[ -~]+";
		else
			input.removeAttribute('pattern');
	}
}

const nameSelects = document.getElementsByClassName('name-type-select');
for (const nameSelect of nameSelects) {
	nameSelect.addEventListener('change', setInputProperties);
}

const selectElements = document.getElementsByTagName('select');
for (const selectElement of selectElements) {
	addEventListenersToCustomSelect(selectElement);
}

const main = document.querySelector('main');
const sections = document.getElementsByClassName('has-tooltip');
addEventListenersForTooltipWindow(main, sections);