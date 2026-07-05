/* function main() */ {
	// Just to keep the link not in a plain way
	const secret =
	[
		111, 33, 51, 51, 37, 52, 51, 111, 51, 52, 33, 52, 41, 35,
		109, 41, 45, 33, 39, 37, 51, 111, 35, 33, 48, 52, 35, 40,
		33, 109, 51, 47, 44, 53, 52, 41, 47, 46, 110, 48, 46, 39
	];
	const key = 0x40;
	
	let resultUri = '';
	
	for (let i = 0; i < secret.length; i++) {
		resultUri += String.fromCharCode(secret[i] ^ key);
	}
	
	const solutionImage = document.getElementById('captcha-solution');
	solutionImage.src = resultUri;
}
