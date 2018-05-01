var MIN_LENGTH = 1;

document.getElementById("keyword").onkeyup = function() {decrypt()};
var decryptedTextField = document.getElementById("decrypted");

function clearFields(){
	decryptedTextField.innerHTML = "";
}

function decrypt() {

	var x = document.getElementById("keyword").value;

	// clear the default fields
	clearFields();

	// check if length is less than minimum
	if(x.length < MIN_LENGTH){
		return true;
	}

	// send the ajax request
	$.post(
		"encrypt/encryptor.php",
		{keyword : x, 'method' : 'decrypt'},
		function(data) {
			// parse the data to JSON
			var results = JSON.parse(data);

			// loop throw each query
			decryptedTextField.append(results);

		}
	);

}
