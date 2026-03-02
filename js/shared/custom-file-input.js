function selectFileWithCustomInput(e) {
	const fileInput = e.target;
	const fakeInput = e.target.nextElementSibling;
	
	const fileNotSelected = fakeInput.getAttribute('text-file-not-selected');
	const fileTooBigError = fakeInput.getAttribute('text-file-too-big');
	
	// Only one file is allowed
	if (fileInput.files.length !== 1) {
		fileInput.value = '';
		fakeInput.textContent = fileNotSelected;
		fakeInput.classList.remove('has-file');
		return;
	}
	
	// Max. size: 512 kilobytes
	if (fileInput.files[0].size > 1024 * 512) {
		fileInput.value = '';
		fakeInput.textContent = fileTooBigError;
		fakeInput.classList.remove('has-file');
		return;
	}
	
	fakeInput.textContent = fileInput.files[0].name;
	fakeInput.classList.add('has-file');
}

function addEventListenerToCustomFileInput(fileInput) {
	fileInput.addEventListener('change', selectFileWithCustomInput);
}
