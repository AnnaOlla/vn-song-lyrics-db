function switchElementAvailability(e, elementId) {
	const elementPointed = document.getElementById(elementId);
	elementPointed.disabled = !elementPointed.disabled;
}
