function autoresizeTextarea(e) {
	const textarea = e.target;
	const text     = textarea.value;
	textarea.rows  = (text.match(/\n/g) || []).length + 1;
	
	// It should be obvious that it is a textarea, not a one-liner
	if (textarea.rows < 2)
		textarea.rows = 2;
}

function addEventListenersToCustomTextarea(textarea) {
	textarea.addEventListener('input', autoresizeTextarea);
}

/* function main() */ {
	const textareas = document.querySelectorAll('textarea');
	
	for (const textarea of textareas) {
		textarea.addEventListener('input', autoresizeTextarea);
		emulateEvent(textarea, 'input');
	}
}
