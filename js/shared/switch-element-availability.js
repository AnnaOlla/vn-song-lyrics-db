function switchElementAvailability(e, elementId) {
	let elementPointed = document.getElementById(elementId);
	elementPointed.disabled = !elementPointed.disabled;
}
