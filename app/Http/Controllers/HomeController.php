<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class HomeController extends Controller
{
    public function index(){
    	return view('home');
    }


    public function getData(Request $input){
    	$this->validate($input, [
    		'board' => 'required'
    	]);

    	$board = $input['board'];
    	$thread = $input['thread'];

    	if (is_numeric($thread)){ //analyzing single thread
    		$posts = $this->getPosts($board, $thread);
    		
    		for ($i = 0; $i < sizeof($posts); $i++){
    			echo 'comment ' . $i . ':<br>';
    			echo $posts[$i] . '<br><br>';

    			$words = explode($posts[$i], ' ');
    			//go through each word, generate counts and relationships

    		}	

    	} else if (ctype_alpha($board)){ //analyzing board
    		echo 'board. under construction';
    	} else if ($board == '*') { //analyzing whole site
    		echo 'site. under construction';
    	} else {
    		echo 'You entered ' . $board . ', which i don\'t know what to do with. <a href = "feedback">Is this an error?</a>';
    	}

    }
    

    //this function takes the board and thread, and
    //returns an array of the text of all posts(OPs and comments)
    private function getPosts($board, $thread = null){
    	
    	$client = new Client(['base_uri' => 'https://a.4cdn.org']);
		$posts = Array();

    	if (is_numeric($thread)){
    			$result = $client->get($board . '/thread/' . $thread . '.json');
    			$bodyString = $result->getBody()->getContents();
    			$postsObj = json_decode($bodyString);
    			$postsArray = $postsObj->posts;
    			$comArray = Array();
    			for ($i = 0; $i < sizeof($postsArray); $i++){
    				array_push($comArray, $postsArray[$i]->com);
    			}
    			return $comArray;
    	} else if (ctype_alpha($board)){
			for ($i = 1; $i < 11; $i++){
				
			}
		}

		return;
    }


    /*
		old processing stuff

		$client = new Client(['base_uri' => 'https://a.4cdn.org']);
    	$result = $client->get('g/catalog.json');
    	$body = $result->getBody();
    	echo '<pre>' . htmlspecialchars($body->getContents()) . '</pre>';
    	$bodyJson = json_decode($body->getContents());

    	$threads = Array();
    	for ($i = 0; $i < 10; $i++){
    		array_push($threads, $bodyJson[$i]->threads);
    	}

    	echo '<pre>' . var_dump() . '</pre>';
    */

}
