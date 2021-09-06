
var url = new URL(window.location.href);
const order = url.searchParams.get('order') ?? 'scene';

const httpRequest = new XMLHttpRequest();
var elem =  document.getElementById('getlist');

httpRequest.onprogress = function(event) {
    elem.innerHTML = event.currentTarget.response;
}

httpRequest.onload = function () {
    if (httpRequest.readyState == httpRequest.DONE && httpRequest.status == 200)
    {
        document.getElementById('progress').style.display = 'none';
        setEventHandlers();
    }
}

httpRequest.open('GET', './getlist.php?order=' + order, true);
httpRequest.send(null);

function setEventHandlers ()
{
    var items = elem.getElementsByClassName("toggleitem");

    for(let item of items) {
        item.addEventListener("click", function() {
            this.classList.toggle("active");
            let content = this.nextElementSibling;
            if (content.style.display === "block")
            {
                content.style.display = "none";
            }
            else
            {
                content.style.display = "block";
            }
        });
    }
}