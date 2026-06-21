// Dependencies:
// -- prepare-entity-name-for-filtering.js

function customSearchableSelectFilterOptions(e) {
	const select   = e.target;
	const filterValue = prepareEntityNameForFiltering(select.value);
	
	const optionList  = select.nextElementSibling;
	const options     = optionList.children;
	
	for (const option of options) {
		const names = option.children;
		let isMatch = false;
		
		for (const name of names) {
			const content = prepareEntityNameForFiltering(name.textContent);
			const re      = new RegExp(filterValue, 'i');
			
			isMatch = isMatch || re.test(content);
		}
		
		if (isMatch) {
			option.style.display = '';
		} else {
			option.style.display = 'none';
		}
	}
}

function customSearchableSelectOnFocus(e) {
	const actualInput = e.target;
	const hiddenInput = actualInput.previousElementSibling;
	const optionList  = e.target.nextElementSibling;
	
	actualInput.setAttribute('data-current-value', actualInput.value ?? '');
	hiddenInput.setAttribute('data-current-value', hiddenInput.value ?? '');
	
	actualInput.value      = '';
	hiddenInput.value = '';
	
	actualInput.placeholder = actualInput.getAttribute('data-placeholder-filter') ?? '';
	
	customSearchableSelectFilterOptions(e);
	
	const bodyHeightBefore   = document.body.scrollHeight;
	optionList.style.display = 'block';
	const bodyHeightAfter    = document.body.scrollHeight;
	
	if (bodyHeightAfter > bodyHeightBefore) {
		const currentHeight = optionList.offsetHeight;
		const extraHeight   = bodyHeightAfter - bodyHeightBefore;
		
		optionList.style.maxHeight = currentHeight - extraHeight + "px";
	}
}

function customSearchableSelectOnBlur(e) {
	const actualInput    = e.target;
	const hiddenInput    = actualInput.previousElementSibling;
	const optionList     = actualInput.nextElementSibling;
	const clickedElement = e.relatedTarget;
	
	if (clickedElement !== null && clickedElement.parentElement === optionList && !clickedElement.hasAttribute('disabled')) {
		actualInput.value = clickedElement.children[0].innerText;
		hiddenInput.value = clickedElement.getAttribute('value');
		
		emulateEvent(hiddenInput, 'change');
	} else {
		actualInput.value = actualInput.getAttribute('data-current-value') ?? '';
		hiddenInput.value = hiddenInput.getAttribute('data-current-value') ?? '';
	}
	
	actualInput.removeAttribute('data-current-value');
	hiddenInput.removeAttribute('data-current-value');
	
	actualInput.placeholder = actualInput.getAttribute('data-placeholder-select') ?? '';
	
	optionList.style.display = 'none';
	actualInput.blur();
}

function customSearchableSelectOnInput(e) {
	customSearchableSelectFilterOptions(e);
}

/* function main() */ {
	const selects = document.querySelectorAll('.custom-searchable-select');
	
	for (const select of selects) {
		const actualInput = select.children[1];
		
		actualInput.addEventListener('focus', customSearchableSelectOnFocus);
		actualInput.addEventListener('blur',  customSearchableSelectOnBlur);
		actualInput.addEventListener('input', customSearchableSelectOnInput);
	}
}
