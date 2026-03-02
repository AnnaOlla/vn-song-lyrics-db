// JS would not be needed 
// if I figured out how to use field-sizing: content
// without knowing the width

function autoresizeTextarea(e) {
	const element = e.target;
	const scrollBarHeight = element.offsetHeight - element.clientHeight;
	
	// Works correctly if "box-sizing: border-box" is set
	element.style.height = 'auto';
	element.style.height = element.scrollHeight + scrollBarHeight + 'px';
}

function addEventListenersToCustomTextarea(textarea) {
	textarea.addEventListener('input', autoresizeTextarea);
}
