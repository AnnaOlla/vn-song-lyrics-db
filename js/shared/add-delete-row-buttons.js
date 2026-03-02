// Dependencies:
//   - /js/shared/emulate-event.js
//   - /js/shared/custom-select.js

function addInputRow(e, entityClass) {
	const thisRow = e.target.parentNode;
	const newRow  = thisRow.cloneNode(true);
	const nextRow = thisRow.nextElementSibling;
	const section = thisRow.parentNode;
	
	section.insertBefore(newRow, nextRow);
	
	const selectElements = newRow.getElementsByTagName('select');
	for (selectElement of selectElements) {
		selectElement.selectedIndex = 0;
		selectElement.disabled = '';
		addEventListenersToCustomSelect(selectElement);
	}
	
	const buttonElements = newRow.getElementsByTagName('button');
	for (buttonElement of buttonElements) {
		buttonElement.disabled = '';
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
		const selectElements = section.getElementsByTagName('select');
		for (selectElement of selectElements) {
			selectElement.selectedIndex = 0;
		}
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
