/* function main() */ {
	const fontSizeSelect = document.getElementById('font-size-select');
	fontSizeSelect.addEventListener('change', changeFontSize);
	
	const showFuriganaSelect = document.getElementById('show-furigana-select');
	showFuriganaSelect.addEventListener('change', switchFurigana);
	
	const showNotesSelect = document.getElementById('show-notes-select');
	showNotesSelect.addEventListener('change', switchNotes);
	
	const showColorsSelect = document.getElementById('show-colors-select');
	showColorsSelect.addEventListener('change', switchColors);
}
