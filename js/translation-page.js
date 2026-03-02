function simultaneousMouseOver(parallelElement) {
	parallelElement.classList.add('hovered-simultaneously')
}

function simultaneousMouseOut(parallelElement) {
	parallelElement.classList.remove('hovered-simultaneously')
}

const leftRows  = document.querySelectorAll('article > section:last-child > section:nth-child(1) > span');
const rightRows = document.querySelectorAll('article > section:last-child > section:nth-child(2) > span');

for (let i = 0; i < leftRows.length; i++) {
	if (i >= rightRows.length)
		break;
	
	leftRows[i].addEventListener('mouseover', (e) => {
		simultaneousMouseOver(rightRows[i]);
	});
	
	leftRows[i].addEventListener('mouseout', (e) => {
		simultaneousMouseOut(rightRows[i]);
	});
}

for (let i = 0; i < rightRows.length; i++) {
	if (i >= leftRows.length)
		break;
	
	rightRows[i].addEventListener('mouseover', (e) => {
		simultaneousMouseOver(leftRows[i]);
	});
	
	rightRows[i].addEventListener('mouseout', (e) => {
		simultaneousMouseOut(leftRows[i]);
	});
}
