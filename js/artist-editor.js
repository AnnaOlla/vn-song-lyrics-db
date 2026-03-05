const fileInput = document.querySelector('input[type="file"]');
addEventListenerToCustomFileInput(fileInput);

const selectElements = document.getElementsByTagName('select');
for (const selectElement of selectElements) {
	addEventListenersToCustomSelect(selectElement);
}

const main = document.querySelector('main');
const sections = document.getElementsByClassName('has-tooltip');
addEventListenersForTooltipWindow(main, sections);