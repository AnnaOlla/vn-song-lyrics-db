function selectFileWithCustomInput(e) {
	const fileInput = e.target;
	const fakeInput = e.target.nextElementSibling;
	
	// Only one file is allowed
	if (fileInput.files.length !== 1) {
		fileInput.value = '';
		fakeInput.textContent = fakeInput.getAttribute('text-file-not-selected');
		fakeInput.classList.remove('has-file');
		return;
	}
	
	// Max. size: 512 kilobytes
	if (fileInput.files[0].size > 1024 * 512) {
		fileInput.value = '';
		fakeInput.textContent = fakeInput.getAttribute('text-file-too-big');
		fakeInput.classList.remove('has-file');
		return;
	}
	
	fakeInput.textContent = fileInput.files[0].name;
	fakeInput.classList.add('has-file');
}

/* function main() */ {
	const fileInputs = document.querySelectorAll('input[type="file"]');
	
	for (const fileInput of fileInputs) {
		fileInput.addEventListener('change', selectFileWithCustomInput);
	}
}
