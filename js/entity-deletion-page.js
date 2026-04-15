/* function main()*/ {
	const submitCheckbox = document.getElementById('confirmation-button');

	submitCheckbox.addEventListener('click', (e) => {
		switchElementAvailability(e, 'submission-button');
	});
}
