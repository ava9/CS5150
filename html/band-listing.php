<?

#
# Example PHP server-side script for generating
# responses suitable for use with jquery-tokeninput
#

# Connect to the database
require_once "../php/config.php";

$conn = $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

# Perform the query
$query = sprintf("SELECT bands.Name FROM `bandstoporchfests` INNER JOIN bands on bands.BandID = bandstoporchfests.BandID WHERE PorchfestID = 1 AND bands.Name LIKE '%%%s%%' LIMIT 5", mysql_real_escape_string($_GET["q"]));
$arr = array();
$result = $mysqli->query($query);

# Collect the results
while($obj = $result->fetch_assoc()) {
    $arr[] = array("id" => $obj["Name"], "name" => $obj["Name"]);
}

# JSON-encode the response
$json_response = json_encode($arr);


# Return the response
echo $json_response;

?>
