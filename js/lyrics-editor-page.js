function switchElementsOnLyricsPage(e) {
	const languageSelect = document.getElementById('language-select');
	const lyricsArea     = document.getElementById('lyrics-area');
	const notesArea      = document.getElementById('notes-area');
	
	let currentElement = e.target;
	let currentSection = currentElement.parentNode;
	
	// The loop disables/enables all rows
	// and stops before the last row.
	// The row with submit and cancel
	// must be untouched
	
	const disabled = (currentElement.selectedIndex !== 0);
	const display  = (currentElement.selectedIndex !== 0) ? 'none' : '';
	
	languageSelect.disabled = disabled;
	lyricsArea.disabled     = disabled;
	notesArea.disabled      = disabled;
	
	while (currentSection.nextElementSibling.nextElementSibling) {
		currentSection = currentSection.nextElementSibling;
		currentSection.style.display = display;
	}
}

/* function main() */ {
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
