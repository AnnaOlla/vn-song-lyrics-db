function setTooltip(e) {
	const tooltipWindow  = document.getElementById('tooltip-window');
	const tooltipHeading = tooltipWindow.children[0];
	const tooltipContent = tooltipWindow.children[1];
	
	const headingDataset = document.getElementById('tooltip-headings');
	const contentDataset = document.getElementById('tooltip-contents');
	
	hoveredItem = e.target;
	const id = hoveredItem.getAttribute('tooltip-id');
	
	tooltipHeading.innerHTML = headingDataset.children[id].innerHTML;
	tooltipContent.innerHTML = contentDataset.children[id].innerHTML;
}

function setDefaultTooltip(e) {
	const tooltipWindow  = document.getElementById('tooltip-window');
	const tooltipHeading = tooltipWindow.children[0];
	const tooltipContent = tooltipWindow.children[1];

	const headingDataset = document.getElementById('tooltip-headings');
	const contentDataset = document.getElementById('tooltip-contents');
	
	hoveredItem = e.target;
	
	tooltipHeading.innerHTML = headingDataset.children[0].innerHTML;
	tooltipContent.innerHTML = contentDataset.children[0].innerHTML;
}
