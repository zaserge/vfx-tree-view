/*
MIT License

Copyright (c) 2021 zaserge@gmail.com

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

*/

const url = new URL(window.location.href);
const order = url.searchParams.get("order") ?? "scene";

const httpRequest = new XMLHttpRequest();
const listContainer = document.getElementById("getlist");

httpRequest.open("GET", "getlist.php?order=" + order, true);

httpRequest.onprogress = function (event) {
  const response = event.currentTarget.response;
  listContainer.innerHTML = response;
};

httpRequest.onload = function (event) {
  if (
    httpRequest.readyState === httpRequest.DONE &&
    httpRequest.status === 200
  ) {
    const progressElem = document.getElementById("progress");
    if (progressElem !== null) {
      progressElem.style.display = "none";

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
  }
};

httpRequest.send(null);
