// Beautifier:
// It is more convinient if all sections will start from the same height
// Because they have non-static headings, it should be handled manually
function setSameHeightForHeadings1(e) {
	const headings = document.getElementsByTagName('h1');
	
	const leftHeading = headings[0];
	const rightHeading = headings[1];
	
	leftHeading.style.boxSizing = 'border-box';
	rightHeading.style.boxSizing = 'border-box';
	
	leftHeading.style.height = 'auto';
	rightHeading.style.height = 'auto';
	
	const leftHeight = leftHeading.getBoundingClientRect().height;
	const rightHeight = rightHeading.getBoundingClientRect().height;
	
	if (leftHeight < rightHeight) {
		leftHeading.style.height = rightHeight + 'px';
	} else if (rightHeight < leftHeight) {
		rightHeading.style.height = leftHeight + 'px';
	}
	// if equal, do nothing
}

window.addEventListener('resize', setSameHeightForHeadings1);

const textareas = document.getElementsByTagName('textarea');
for (const textarea of textareas) {
	addEventListenersToCustomTextarea(textarea);
	emulateEvent(textarea, 'input');
}

const selectElements = document.getElementsByTagName('select');
for (const selectElement of selectElements) {
	addEventListenersToCustomSelect(selectElement);
}

const main = document.querySelector('main');
const sections = document.getElementsByClassName('has-tooltip');
addEventListenersForTooltipWindow(main, sections);