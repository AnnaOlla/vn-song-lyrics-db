// Dependencies:
//   - /js/shared/emulate-event.js
//   - /js/inputs/custom-select.js

function addInputRow(e, entityClass) {
	const thisRow = e.target.parentNode;
	const newRow  = thisRow.cloneNode(true);
	const nextRow = thisRow.nextElementSibling;
	const section = thisRow.parentNode;
	
	section.insertBefore(newRow, nextRow);
	
	const selects = newRow.getElementsByTagName('select');
	for (select of selects) {
		select.selectedIndex = 0;
		select.disabled = '';
		
		select.addEventListener('focus',  customSelectOnFocus);
		select.addEventListener('blur',   customSelectOnBlur);
		select.addEventListener('change', customSelectOnChange);
	}
	
	const buttons = newRow.getElementsByTagName('button');
	for (button of buttons) {
		button.disabled = '';
	}
	
	addEventListenersToAddDeleteButtons(newRow, entityClass);
}

function deleteInputRow(e, entityClass) {
	const thisRow = e.target.parentNode;
	const section = thisRow.parentNode;
	
	// Should be divided by 2, because each row has 2 buttons
	const buttons = document.getElementsByClassName(entityClass);
	
	if (buttons.length / 2 > 1) {
		thisRow.remove();
	} else {
		const select = section.querySelector('select');
		select.selectedIndex = 0;
	}
}

function addEventListenersToAddDeleteButtons(buttonContainer, entityClass) {
	const addButtons = buttonContainer.getElementsByClassName(entityClass + ' add-input-row');
	
	for (addButton of addButtons) {
		addButton.addEventListener('click', (e) => {
			addInputRow(e, entityClass);
		});
	}
	
	const deleteButtons = buttonContainer.getElementsByClassName(entityClass + ' delete-input-row');
	
	for (deleteButton of deleteButtons) {
		deleteButton.addEventListener('click', (e) => {
			deleteInputRow(e, entityClass);
		});
	}
}
