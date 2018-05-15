let MIN_LENGTH = 1;

document.getElementById("searchUser").onkeyup = function () {
    update()
};

let firstName = document.getElementById("fName");
let lastName = document.getElementById("lName");
let age = document.getElementById("age");
let email = document.getElementById("email");
let group = document.getElementById("group");
let quantity = document.getElementById("quantity");
let errorMSG = document.getElementById("errorMSG");
let errorBox = document.getElementById("errorBox");

function clearFields() {
    $('#content').fadeOut(0);
    $('#errorBox').fadeOut(100);
    firstName.innerHTML = "";
    lastName.innerHTML = "";
    email.innerHTML = "";
    age.innerHTML = "";
    group.innerHTML = "";
}

function update() {

    let x = document.getElementById("username").value;

    // clear the default fields
    clearFields();

    // send the ajax request
    $.get(
        "Query/getUserInfo.php",
        {keyword: x},
        function (data) {
            // parse the data to JSON
            let results = jQuery.parseJSON(data);

            // loop throw each query
            $(results).each(function (key, value) {

                // check for any errors
                if (value['error']) {
                    errorMSG.innerText = value['error'];
                    $('#errorBox').fadeIn(500);
                    $('#content').fadeOut(0);
                    return;
                }

                $(value).each(function (key1, value1) {
                    $(value1).each(function (key2, value2) {
                        firstName.append(value2['fName']);
                        lastName.append(value2['lName']);
                        age.append(value2['age']);
                        email.append(value2['email']);
                        group.append(value2['group']);
                        $('#content').fadeIn(1000);
                    });
                });
            });
        }
    );
}