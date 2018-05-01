var MIN_LENGTH = 1;

window.onload = function () {
    encrypt();
};

function encrypt() {

    var request = new XMLHttpRequest();
    var url = "http://192.168.1.18/als/demo/apiTest.php?method=login&key=SELF&username=lovemst&password=123&appID=1&appKey=1";
    request.open('GET', url);
    request.responseType = 'text';

    request.onload = function () {
        document.write(request.response);
    };

    request.send();
}
