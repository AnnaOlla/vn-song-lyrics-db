function overflowsBodyVertically(element) {
	const absoluteBottom = element.getBoundingClientRect().bottom + window.scrollY;
	return absoluteBottom > document.body.scrollHeight;
}

function customSelectOnFocus(e) {
	const select = e.target;
	const fake   = e.target.nextElementSibling;
	
	const optionCount    = select.children.length;
	const maxOptionCount = 8;
	
	// Browsers show a warning if multiple-select is empty
	if (optionCount === 0)
	{
		select.blur();
		return;
	}
	
	// Put the fake to the same position
	const rectangleBefore = select.getBoundingClientRect();
	fake.style.display    = 'inline-block';
	fake.style.width      = rectangleBefore.width  + 'px';
	fake.style.height     = rectangleBefore.height + 'px';
	
	// Select is inline-block by default
	// But if it is a section it gives extra height: even 'null' is considered to have height
	fake.style.fontSize      = 0;
	fake.style.lineHeight    = 0;
	fake.style.verticalAlign = 'middle';
	
	select.multiple = true;
	select.size     = (optionCount < maxOptionCount ? optionCount : maxOptionCount);
	
	select.style.position = 'absolute';
	select.style.left     = rectangleBefore.left + window.scrollX + 'px';
	select.style.top      = rectangleBefore.top + window.scrollY + 'px';
	select.style.width    = rectangleBefore.width + 'px';
	// Do not set height: it'll be calculated automatically depending on options count
	
	select.style.marginTop    = '0px';
	select.style.marginBottom = '0px';
	select.style.marginLeft   = '0px';
	select.style.marginRight  = '0px';
	
	while (overflowsBodyVertically(select) && select.size > 2) {
		select.size--;
	}
}

function customSelectOnBlur(e) {
	const select = e.target;
	const fake   = e.target.nextElementSibling;
	
	select.multiple = '';
	select.size = 1;
	
	select.style.position = '';
	select.style.top      = '';
	select.style.left     = '';
	select.style.width    = '';
	select.style.height   = '';
	
	select.style.marginTop    = '';
	select.style.marginBottom = '';
	select.style.marginLeft   = '';
	select.style.marginRight  = '';
	
	fake.style.display = '';
}

function customSelectOnChange(e) {
	const select = e.target;
	customSelectOnBlur(e);
	select.blur();
}

/* function main() */ {
	const selects = document.querySelectorAll('select');
	
	for (const select of selects) {
		select.addEventListener('focus',  customSelectOnFocus);
		select.addEventListener('blur',   customSelectOnBlur);
		select.addEventListener('change', customSelectOnChange);
	}
}
