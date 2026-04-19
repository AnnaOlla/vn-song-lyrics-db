/* function main() */ {
	const discNumber  = document.getElementById('disc-number');
	const trackNumber = document.getElementById('track-number');
	
	const nextDiscButton     = document.getElementById('next-disc');
	const previousDiscButton = document.getElementById('previous-disc');
	
	const previousDiscTrackNumber = trackNumber.value;
	
	if (previousDiscTrackNumber == 1) {
		nextDiscButton.disabled = 'true';
	}
	previousDiscButton.style.display = 'none';
	
	function startNewDisc(e) {
		discNumber.value++;
		trackNumber.value = 1;
		
		nextDiscButton.style.display     = 'none';
		previousDiscButton.style.display = '';
	}
	
	function returnToPreviousDisc(e) {
		discNumber.value--;
		trackNumber.value = previousDiscTrackNumber;
		
		nextDiscButton.style.display     = '';
		previousDiscButton.style.display = 'none';
	}
	
	nextDiscButton.addEventListener('click', startNewDisc);
	previousDiscButton.addEventListener('click', returnToPreviousDisc);
	
	const mainArea = document.querySelector('main');
	mainArea.addEventListener('mouseleave', setDefaultTooltip);
	
	const sections = document.getElementsByClassName('has-tooltip');
	for (section of sections) {
		section.addEventListener('mouseenter', setTooltip);
	}
}
