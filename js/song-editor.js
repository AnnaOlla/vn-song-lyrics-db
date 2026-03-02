let discNumber = document.getElementById('disc-number');
let trackNumber = document.getElementById('track-number');

let nextDiscButton = document.getElementById('next-disc');
let previousDiscButton = document.getElementById('previous-disc');

const previousDiscTrackNumber = trackNumber.value;

if (previousDiscTrackNumber == 1) {
	nextDiscButton.disabled = 'true';
}
previousDiscButton.style.display = 'none';
	
function startNewDisc(e) {
	
	discNumber.value++;
	trackNumber.value = 1;
	
	nextDiscButton.style.display = 'none';
	previousDiscButton.style.display = '';
}

function returnToPreviousDisc(e) {
	discNumber.value--;
	trackNumber.value = previousDiscTrackNumber;
	
	nextDiscButton.style.display = '';
	previousDiscButton.style.display = 'none';
}

nextDiscButton.addEventListener('click', startNewDisc);
previousDiscButton.addEventListener('click', returnToPreviousDisc);

const main = document.querySelector('main');
const sections = document.getElementsByClassName('has-tooltip');
addEventListenersForTooltipWindow(main, sections);

const selectElements = document.getElementsByTagName('select');
for (const selectElement of selectElements) {
	addEventListenersToCustomSelect(selectElement);
}