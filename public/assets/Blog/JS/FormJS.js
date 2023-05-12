const checkbox = document.querySelector("#tagsChoice");
const inputContainer = document.querySelector("#input-container");
const addTagsInput = document.querySelector(".addTagsInput");
const selectedChips = document.querySelector(".selected-chips");
const blogImage = document.querySelector(".blogImageFile");
const blogImg = document.querySelector(".blogImg");
let addedTags = document.querySelector("#addedTags");
let selectedValues = [];

blogImage.addEventListener("change", () => {
	const file = blogImage.files[0];
	const reader = new FileReader();

	reader.addEventListener("load", function () {
		blogImg.src = reader.result;
	});

	if (file) {
		reader.readAsDataURL(file);
	}
});

checkbox.addEventListener("change", (event) => {
	if (event.target.checked) {
		inputContainer.style.display = "block";
	} else {
		inputContainer.style.display = "none";
	}
});

function addTags(selectedValues) {
	let str = "";
	selectedValues.forEach((value) => {
		if (!str.includes(value)) {
			str = str + `#${value}`;
		}
		addedTags.value = str;
	});
	// console.log("str: " + str);
	// console.log("addedTags: " + addedTags.value);
}

addTagsInput.addEventListener("keydown", function (event) {
	if (event.keyCode === 13) {
		event.preventDefault();
		const tag = addTagsInput.value.trim();
		if (tag !== "") {
			selectedValues.push(tag);
			console.log(selectedValues);
			addChip(tag);
			addTags(selectedValues);
			addTagsInput.value = "";
		}
	}
});

selectedChips.addEventListener("click", (event) => {
	if (event.target.classList.contains("close")) {
		const chip = event.target.parentNode;
		const chipValue = chip.getAttribute("data-value");
		const index = selectedValues.indexOf(chipValue);
		selectedValues.splice(index, 1);
		addTags(selectedValues);
		selectedChips.removeChild(chip);
		console.log(selectedValues);
	}
});

function addChip(value) {
	const chip = document.createElement("div");
	chip.classList.add("chip");
	chip.setAttribute("data-value", value);
	chip.innerHTML = `
		    <span>${value}</span>
		    <span class="close">&times;</span>
		  `;
	selectedChips.appendChild(chip);
}

document.getElementById("submitBtn").addEventListener("click", () => {
	const xhr = new XMLHttpRequest();
	xhr.open("POST", "../../src/Controller/BlogsController.php");
	xhr.setRequestHeader("Content-Type", "application/json");
	xhr.send(JSON.stringify({ addedTags: selectedValues }));

	xhr.onreadystatechange = function () {
		if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
			console.log(xhr.responseText);
		}
	};
});
