/**
 * Created by Rickard on 2016-10-23.
 */

function register() {
    var formElem = document.getElementById("regForm");
    var form = new FormData(formElem);

    var request = new XMLHttpRequest();
    request.open("post", "/user/create", true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.onreadystatechange = function () {
        console.log("Readystate changed: " + request.readyState + " - Status: " + request.status);
        if (request.readyState == XMLHttpRequest.DONE) {
            if (request.status == 201) {
                //location.href = "/download";
                console.log("Registered")
            } else {
                //location.href = "/register";
                result = JSON.parse(request.responseText);
                console.log("Error (" + request.status +"): " + result.error);
            }
        }
    };
    console.log(JSON.stringify(form.values()));
    request.send(formElem);
    console.log("Sent reg request!");
}