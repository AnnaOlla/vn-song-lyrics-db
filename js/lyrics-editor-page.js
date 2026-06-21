function switchElementsOnLyricsPage(e) {
	const languageSelect = document.getElementById('language-select');
	const lyricsArea     = document.getElementById('lyrics-area');
	const notesArea      = document.getElementById('notes-area');
	
	let currentElement = e.target;
	let currentSection = currentElement.parentNode.parentNode;
	
	// The loop disables/enables all rows
	// and stops before the last row.
	// The row with submit and cancel
	// must be untouched
	
	const disabled = (currentElement.value !== '');
	const display  = (currentElement.value !== '') ? 'none' : '';
	
	languageSelect.disabled = disabled;
	lyricsArea.disabled     = disabled;
	notesArea.disabled      = disabled;
	
	while (currentSection.nextElementSibling.nextElementSibling) {
		currentSection = currentSection.nextElementSibling;
		currentSection.style.display = display;
	}
}

/* function main() */ {
	addEventListenersToAddDeleteButtons(document, 'artist-select character-select')
	
	const copyLyricsSelect = document.getElementById('original-song-select');
	copyLyricsSelect.addEventListener('change', switchElementsOnLyricsPage);
	emulateEvent(copyLyricsSelect, 'change');
	
	const mainArea = document.querySelector('main');
	mainArea.addEventListener('mouseleave', setDefaultTooltip);
	
	const sections = document.getElementsByClassName('has-tooltip');
	for (section of sections) {
		section.addEventListener('mouseenter', setTooltip);
	}
}
