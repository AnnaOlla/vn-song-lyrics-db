function insertUppercaseIntoCaptchaInput(event) {
	// Supported codes by the Juliamo font:
	// [\x0020-\x007F]
	// [\x0108-\x0109]
	// [\x011C-\x011D]
	// [\x0124-\x0125]
	// [\x0134-\x0135]
	// [\x015C-\x015D]
	// [\x016C-\x016D]
	
	event.target.value = event.target.value.replace(/[^A-Za-z0-9]/g, '').toUpperCase();
}

/* function main() */ {
	const captchaInputs = document.querySelectorAll('input[type="text"].captcha-input');
	
	for (const captchaInput of captchaInputs) {
		captchaInput.addEventListener('input', insertUppercaseIntoCaptchaInput);
	}
}
