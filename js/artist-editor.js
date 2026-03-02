const fileInput = document.querySelector('input[type="file"]');
addEventListenerToCustomFileInput(fileInput);

const main = document.querySelector('main');
const sections = document.getElementsByClassName('has-tooltip');
addEventListenersForTooltipWindow(main, sections);