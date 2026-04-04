function autoresizeTextarea(e) {
	const textarea = e.target;
	const text     = textarea.value;
	textarea.rows  = (text.match(/\n/g) || []).length + 1;
}

function addEventListenersToCustomTextarea(textarea) {
	textarea.addEventListener('input', autoresizeTextarea);
}
