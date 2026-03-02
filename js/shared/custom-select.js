function customSelectOnFocus(e) {
	const select = e.target;
	const fake = e.target.nextElementSibling;
	
	const rectangleBefore = select.getBoundingClientRect();
	
	fake.style.display = 'inline-block';
	fake.style.width   = rectangleBefore.width  + 'px';
	fake.style.height  = rectangleBefore.height + 'px';
	
	const optionCount = select.children.length;
	const maxOptionCount = 10;
	
	select.multiple = true;
	select.size = (optionCount < maxOptionCount ? optionCount : maxOptionCount);
	
	const rectangleAfter = select.getBoundingClientRect();
	
	// Put the select in the same position as it had
	
	select.style.position = 'absolute';
	select.style.left     = rectangleBefore.left + window.scrollX + 'px';
	select.style.top      = rectangleBefore.top + window.scrollY + 'px';
	select.style.width    = rectangleBefore.width + 'px';
	select.style.height   = rectangleAfter.height + 'px';
	
	select.style.marginTop    = '0px';
	select.style.marginBottom = '0px';
}

function customSelectOnBlur(e) {
	const select = e.target;
	const fake = e.target.nextElementSibling;
	
	select.multiple = '';
	select.size = 1;
	
	select.style.position = '';
	select.style.top      = '';
	select.style.left     = '';
	select.style.width    = '';
	select.style.height   = '';
	
	select.style.marginTop    = '';
	select.style.marginBottom = '';
	
	fake.style.display = '';
}

function customSelectOnChange(e) {
	const select = e.target;
	const fake = e.target.nextElementSibling;
	
	select.multiple = '';
	select.size = 1;
	
	select.style.position = '';
	select.style.top      = '';
	select.style.left     = '';
	select.style.height   = '';
	
	select.style.marginTop    = '';
	select.style.marginBottom = '';
	
	select.blur();
	
	fake.style.display = '';
}

function addEventListenersToCustomSelect(selectElement) {
	selectElement.addEventListener('focus', customSelectOnFocus);
	selectElement.addEventListener('blur', customSelectOnBlur);
	selectElement.addEventListener('change', customSelectOnChange);
}
