<?php



use Phalcon\Mvc\Micro;


$conn = new mysqli("localhost", "punis", "josip","punis");
$first=false;
#Ovo je krairanje baze za 2. dio , nije dio kolokvija
if ($first) {
$first=false;
if ($conn->connect_error) {
   die("Connection failed: " . $conn->connect_error);
}
else{
	$sql="create table if not exists login_cred(username varchar(16),signature varchar(16));";
	if ($conn->query($sql) === TRUE) {
	   #sql="DELETE FROM login_cred;"
	   echo "Table MyGuests created successfully";
	   $sql="insert into login_cred(username,signature) values('punis','pass0001');";
	   $conn->query($sql);
	   if ($conn->query($sql) === TRUE) {
 #   		echo "New record created successfully";
	} else {
    		echo "Error: " . $sql . "<br>" . $conn->error;
	}	

	} else {
    	echo "Error creating table: " . $conn->error;
	}
}

}


$app = new Micro();

$app->get(
    "/get-signature",
    function () {
        $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"http://iivakic.riteh.hexis.hr/get-signature/");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,"username=punis");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$server_output = curl_exec ($ch);
	curl_close ($ch);
	echo $server_output;
		
    }
);

$app->get(
	"/get-question/{id}",
	function($id){
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL,"http://iivakic.riteh.hexis.hr/get-questions/");
        	curl_setopt($ch, CURLOPT_POST, 1);
        	curl_setopt($ch, CURLOPT_POSTFIELDS,"username=punis&signature=$id");
        	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        	$server_output = curl_exec ($ch);
        	curl_close ($ch);
        	echo $server_output;
	}
);

$app->get(
	"/answer/{id}",
	function($id){
		$odgovori=array('1'=>'odg','2'=>'odg 2','3'=>'odg');
		$json_odg=json_encode($odgovori);
                $ch=curl_init();
                curl_setopt($ch, CURLOPT_URL,"http://iivakic.riteh.hexis.hr/answer/");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS,"username=punis&signature=$id&odgovori=$json_odg");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $server_output = curl_exec ($ch);
                curl_close ($ch);
                echo $server_output;

	}

);

$app->get(
    "/get-signature-dummy",
    function () {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"http://punis.riteh.hexis.hr/copy-get-signature");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,"username=punis");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        echo $server_output;

    }
);


$app->get(
    "/get-question-dummy/{pass}",
    function ($pass) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"http://punis.riteh.hexis.hr/copy-get-question");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,"username=punis&signature=$pass");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        echo $server_output;

    }
);




$app->post(
	"/copy-get-answer",
	function() use ($app){
		$user = $app->request->getPost("username");
		$pass = $app->request->getPost("signature");
		$conn = new mysqli("localhost", "punis", "josip","punis");
		if ($conn->connect_error) {
			  die("Connection failed: " . $conn->connect_error);
		}
		else
		{
			$sql="select distinct signature from login_cred where username='$user';";
			$result = $conn->query($sql); 
			$found=false;
			if ($result->num_rows > 0) {
			
			    while($row = $result->fetch_assoc()) {
				if($row['signature']===$pass){
						$found=true;
				}
        			}		
			}
			if ($found){
				echo "Match";
			}
			else{
				echo "No match";
				}
			}

		}
	}
);





$app->handle();



?>


