<?

require "../../init.php";

if (!isset($_POST['keyword'])) {
	die("");
}

$arr = Array();

switch($_POST['method']){
	case 'encrypt':
		$text = $_POST['keyword'];
		$text = $database->secureInput($text);

		// encrypt it
		$encryptedText = $functions->encryptIt($text);

		// return the results
		array_push($arr, $encryptedText);
		echo json_encode($arr, JSON_HEX_APOS);
		break;
	case 'decrypt':
		$text = $_POST['keyword'];
		$text = $database->secureInput($text);

		// decrypt it
		$decryptedText = $functions->decryptIt($text);

		// return the results
		array_push($arr, $decryptedText);
		echo json_encode($arr, JSON_HEX_APOS);
		break;
}

