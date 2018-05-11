var MIN_LENGTH = 1;

window.onload = function () {
    clearFields();

    update();
};

let fName = document.getElementById("fName");
let lName = document.getElementById("lName");
let user_age = document.getElementById("age");
let user_email = document.getElementById("email");

function clearFields() {
    fName.innerHTML = "{first_name}";
    lName.innerHTML = "{last_name}";
    user_age.innerHTML = "{age}";
    user_email.innerHTML = "{email}";
}

function update() {

    // send the ajax request
    $.get(
        "Query/getUserInfo.php",
        {},
        function (data) {
            // parse the data to JSON
            let results = jQuery.parseJSON(data);

            // loop throw each query
            $(results).each(function (key, value) {
                $(value).each(function (key1, value1) {
                    $(value1).each(function (key2, value2) {

                        fName.innerHTML = (value2['fName']);
                        lName.innerHTML = (value2['lName']);
                        user_age.innerHTML = (value2['age']);
                        user_email.innerHTML = (value2['email']);

                    });
                });
            });
        }
    );

}
