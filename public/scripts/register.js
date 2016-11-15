/**
 * Created by Rickard on 2016-10-23.
 */

function register() {
    var form = new FormData();

    var inputs = document.querySelector('.input');
    for (var input in inputs) {
        form.append(input.name, input.value);
    }

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
    request.send(form);
    console.log("Sent reg request!");
    //location.href = "/download";
    return false; //Disable normal submission
}

function login() {
    var form = new FormData();
    var params = '';
    params = params + 'email=' + document.getElementsByName('email')[0].value;
    params = params + '&password=' + document.getElementsByName('password')[0].value;

    var request = new XMLHttpRequest();
    request.open("post", "/login", true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.onreadystatechange = function () {
        console.log("Readystate changed: " + request.readyState + " - Status: " + request.status);
        if (request.readyState == XMLHttpRequest.DONE) {
            if (request.status == 200) {
                //location.href = "/download";
                console.log("Registered")
                //location.href = "/download";
            } else {
                //location.href = "/register";
                result = JSON.parse(request.responseText);
                console.log("Error (" + request.status +"): " + result.error);
            }
        }
    };
    console.log(JSON.stringify(form));
    request.send(params);
    console.log("Sent reg request!");
    return false; //Disable normal submission
}