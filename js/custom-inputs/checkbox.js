function customCheckboxOnChange(e) {
	e.target.blur();
}

/* function main() */ {
	const checkboxes = document.querySelectorAll('.custom-checkbox-input');
	
	for (const checkbox of checkboxes) {
		checkbox.addEventListener('change', customCheckboxOnChange);
	}
}
