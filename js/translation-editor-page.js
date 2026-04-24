function setSameHeightForH1(e) {
	const headings = document.getElementsByTagName('h1');
	
	const leftHeading  = headings[0];
	const rightHeading = headings[1];
	
	leftHeading.style.boxSizing  = 'border-box';
	rightHeading.style.boxSizing = 'border-box';
	
	leftHeading.style.height  = 'auto';
	rightHeading.style.height = 'auto';
	
	const leftHeight  = leftHeading.getBoundingClientRect().height;
	const rightHeight = rightHeading.getBoundingClientRect().height;
	
	if (leftHeight < rightHeight) {
		leftHeading.style.height = rightHeight + 'px';
	} else if (rightHeight < leftHeight) {
		rightHeading.style.height = leftHeight + 'px';
	}
	// if equal, do nothing
}

/* function main() */ {
	window.addEventListener('onload', setSameHeightForH1);
	window.addEventListener('resize', setSameHeightForH1);
	
	const mainArea = document.querySelector('main');
	mainArea.addEventListener('mouseleave', setDefaultTooltip);
	
	const sections = document.getElementsByClassName('has-tooltip');
	for (section of sections) {
		section.addEventListener('mouseenter', setTooltip);
	}
}
