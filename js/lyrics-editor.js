function switchElements(e) {
	const languageSelect = document.querySelector('.language-select');
	const lyricsArea     = document.querySelector('.lyrics-area');
	const notesArea      = document.querySelector('.notes-area');
	
	let currentElement = e.target;
	let currentSection = currentElement.parentNode;
	
	if (currentElement.selectedIndex !== 0) {
		languageSelect.disabled = true;
		lyricsArea.disabled = true;
		notesArea.disabled = true;
		
		// The loop disables all rows
		// and stops before the last row:
		// The row with submit and cancel
		// must be untouched
		
		while (currentSection.nextElementSibling.nextElementSibling) {
			currentSection = currentSection.nextElementSibling;
			currentSection.style.display = 'none';
		}
	} else {
		languageSelect.disabled = false;
		lyricsArea.disabled = false;
		notesArea.disabled = false;
		
		// The loop enables all rows
		// and stops before the last row:
		// The row with submit and cancel
		// must be untouched
		
		while (currentSection.nextElementSibling.nextElementSibling) {
			currentSection = currentSection.nextElementSibling;
			currentSection.style.display = '';
		}
	}
}

const copyLyricsSelect = document.querySelector('.original-song-select');
copyLyricsSelect.addEventListener('change', switchElements);
emulateEvent(copyLyricsSelect, 'change');

addEventListenersToAddDeleteButtons(document, 'artist-select character-select');

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