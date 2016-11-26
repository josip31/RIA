<?php

use Phalcon\MVC\Micro;
use Phalcon\Http\Response;
$app=new Micro();

$app->get("/get-pass/{username}",
	  function($a){
		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,"punis.riteh.hexis.hr/send-pass");
		curl_setopt($ch,CURLOPT_POST,1);	
		curl_setopt($ch,CURLOPT_POSTFIELDS,"username=$a");
		$rez=curl_exec($ch);
		curl_close($ch);
	}
);

$app->get("/find/{user}/{pass}",
	 function($user,$pass){
		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,"punis.riteh.hexis.hr/send-data");
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,"username=$user&password=$pass");
		#curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		$rez=curl_exec($ch);
		#echo $rez."<br>";
		curl_close($ch);
	}
);

$app->get("/get-signature",
	function(){
		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,"http://iivakic.riteh.hexis.hr/get-signature");
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,"username=punis");
		$rez=curl_exec($ch);
		curl_close($ch);
	}
);

$app->get("/rjesi/{x}/{o}/{y}",
	function($x,$o,$y){
		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,"punis.riteh.hexis.hr/solver");
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,"x=$x&y=$y&operation=$o");
		$rez=curl_exec($ch);
		curl_close($ch);
	}
);

$app->post("/solver",
	function() use($app){
	 $x=$app->request->getPost("x");
	 $y=$app->request->getPost("y");
	 $oper=$app->request->getPost("operation");
	 $rez=0;

	 if($oper=="z"){$rez=$x+$y;}
	 elseif($oper=="o"){$rez=$x-$y;}
	 elseif($oper=="m"){$rez=$x*$y;}
	 elseif($oper=="d"){$rez=$x/$y;}

	$app->response->setContent($rez);
	$app->response->setStatusCode(200,"OK");
	$app->response->send();
	
	}
);


$app->post("/send-data",
	  function() use($app) {
		$user=$app->request->getPost("username");
		$pass=$app->request->getPost("password");
		$acc=false;
		$data=array(1=>"odg 1",2=>"odg 2",3=>"odg 3");
		if($user=="punis" && $pass=="1000" ||  $user=="admin" && $pass=="2000"){$acc=true;}
		if($acc){
			$app->response->setStatusCode(200,"OK");
			$app->response->setContent(json_encode($data));
		}
		else{
			$app->response->setStatusCode("401","No access");
			$app->response->setContent("Access denied");
		}
		$app->response->send();
	}
);



$app->post("/send-pass",
	  function () use($app){
		$username = $app->request->getPost("username");
		$pass="";
		if($username=="punis"){$pass="0000";}
		elseif($username=="admin"){$pass="1000";}
		elseif($username=="ria"){$pass="2000";}
		if (strlen($pass) > 0){
			$app->response->setStatusCode(200,"OK");
			$app->response->setContent($pass);
			$app->response->send();
		}
		else{
			$odg=new Response();
			$odg->setStatusCode(404,"User not found");
			$odg->send();
		}

	}
);


$app->notFound(function() use ($app){
	$app->response->setStatusCode(404,"Budalo");
	$app->response->sendHeaders();
	echo "<h1>Ruta ne postoji<h1>";
});

$app->handle();

