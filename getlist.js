const url = new URL(window.location.href);
const order = url.searchParams.get("order") ?? "scene";

const httpRequest = new XMLHttpRequest();
const listContainer = document.getElementById("getlist");

httpRequest.onprogress = function (event) {
  const response = event.currentTarget.response;
  listContainer.innerHTML = response;
};

httpRequest.onload = function (event) {
  if (
    httpRequest.readyState === httpRequest.DONE &&
    httpRequest.status === 200
  ) {
    if (document.getElementById("done")) {
      document.getElementById("progress").style.display = "none";
    }

    document.title = document.getElementById("title").textContent;
    const items = listContainer.getElementsByClassName("toggleitem");
    for (let item of items) {
      item.addEventListener("click", function () {
        this.classList.toggle("active");
        const content = this.nextElementSibling;
        if (content.style.display === "block") {
          content.style.display = "none";
        } else {
          content.style.display = "block";
        }
      });
    }

  }
};

httpRequest.open("GET", "getlist.php?order=" + order, true);
httpRequest.send(null);
