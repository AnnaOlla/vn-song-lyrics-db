/* function main()*/ {
	addEventListenersToAddDeleteButtons(document, 'game-select');
	addEventListenersToAddDeleteButtons(document, 'album-select');
	addEventListenersToAddDeleteButtons(document, 'artist-select');
	addEventListenersToAddDeleteButtons(document, 'character-select');
	
	const mainArea = document.querySelector('main');
	mainArea.addEventListener('mouseleave', setDefaultTooltip);
	
	const sections = document.getElementsByClassName('has-tooltip');
	for (section of sections) {
		section.addEventListener('mouseenter', setTooltip);
	}
}
