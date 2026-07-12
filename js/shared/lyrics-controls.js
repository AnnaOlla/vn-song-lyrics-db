function changeFontSize(e) {
	const difference = parseInt(e.target.value);
	
	const headings2 = document.querySelectorAll('.lyrics-section h2');
	for (let heading2 of headings2) {
		heading2.style.fontSize = (24 + difference) + 'px';
	}
	
	const headings3 = document.querySelectorAll('.lyrics-section h3');
	for (let heading3 of headings3) {
		heading3.style.fontSize = (16 + difference) + 'px';
	}
	
	const textLines = document.querySelectorAll('.lyrics-section span.text-line');
	for (let textLine of textLines) {
		textLine.style.fontSize = (16 + difference) + 'px';
	}
	
	const textParts = document.querySelectorAll('.lyrics-section span.text-line span');
	for (let textPart of textParts) {
		textPart.style.fontSize = (16 + difference) + 'px';
	}
	
	const smallNotes = document.querySelectorAll('.lyrics-section a.note-small');
	for (let smallNote of smallNotes) {
		smallNote.style.fontSize = ((16 + difference) * 2 / 3) + 'px';
	}
	
	const bigNotes = document.querySelectorAll('.lyrics-section a.note-big');
	for (let bigNote of bigNotes) {
		bigNote.style.fontSize = (16 + difference) + 'px';
	}
}

function switchFurigana(e) {
	const display = e.target.value == 0 ? 'none' : '';
	
	const furiganaLabels = document.querySelectorAll('.lyrics-section rt');
	for (let furiganaLabel of furiganaLabels) {
		furiganaLabel.style.display = display;
	}
}

function switchNotes(e) {
	const display = e.target.value == 0 ? 'none' : '';
	
	const smallNotes = document.querySelectorAll('.lyrics-section a.note-small');
	for (let note of smallNotes) {
		note.style.display = display;
	}
	
	const bigNotes = document.querySelectorAll('.lyrics-section a.note-big');
	for (let note of bigNotes) {
		note.style.display = display;
	}
}

function switchColors(e) {
	const display = e.target.value != 0;
	
	const coloredParts = document.querySelectorAll('.lyrics-section span.text-line > span');
	for (let part of coloredParts) {
		if (display) {
			part.style.color = part.getAttribute('default-color');
		} else {
			const color = part.style.color ? part.style.color : '#FFFFFF';
			part.setAttribute('default-color', color);
			part.style.color = '#FFFFFF';
		}
	}
}
