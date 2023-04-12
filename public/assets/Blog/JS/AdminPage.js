const blogLink = document.querySelector("#blogLink");
const blogCategoryLink = document.querySelector("#blogCategoryLink");
const tagsLink = document.querySelector("#tagsLink");
const HomeLink = document.querySelector("#homeLink");

const blogsSection = document.querySelector("#blogs");
const blogCategorySection = document.querySelector("#blogCategory");
const tagsSection = document.querySelector("#tags");
const homeSection = document.querySelector("#home");

// Default
blogLink.classList.remove("active");
blogCategoryLink.classList.remove("active");
tagsLink.classList.remove("active");

blogsSection.classList.add("d-none");
blogCategorySection.classList.add("d-none");
tagsSection.classList.add("d-none");

//when clicked
HomeLink.addEventListener("click", () => {
	HomeLink.classList.add("active");
	blogLink.classList.remove("active");
	blogCategoryLink.classList.remove("active");
	tagsLink.classList.remove("active");

	homeSection.classList.remove("d-none");
	blogsSection.classList.add("d-none");
	blogCategorySection.classList.add("d-none");
	tagsSection.classList.add("d-none");
});

blogLink.addEventListener("click", () => {
	HomeLink.classList.remove("active");
	blogLink.classList.add("active");
	blogCategoryLink.classList.remove("active");
	tagsLink.classList.remove("active");

	homeSection.classList.add("d-none");
	blogsSection.classList.remove("d-none");
	blogCategorySection.classList.add("d-none");
	tagsSection.classList.add("d-none");
});

blogCategoryLink.addEventListener("click", () => {
	HomeLink.classList.remove("active");
	blogLink.classList.remove("active");
	blogCategoryLink.classList.add("active");
	tagsLink.classList.remove("active");

	homeSection.classList.add("d-none");
	blogsSection.classList.add("d-none");
	blogCategorySection.classList.remove("d-none");
	tagsSection.classList.add("d-none");
});

tagsLink.addEventListener("click", () => {
	HomeLink.classList.remove("active");
	blogLink.classList.remove("active");
	blogCategoryLink.classList.remove("active");
	tagsLink.classList.add("active");

	homeSection.classList.add("d-none");
	blogsSection.classList.add("d-none");
	blogCategorySection.classList.add("d-none");
	tagsSection.classList.remove("d-none");
});
