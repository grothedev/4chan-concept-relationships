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

    	} else if (ctype_alpha($board)){ //analyzing board
    		$posts = $this->getPosts($board);

    	} else if ($board == '*') { //analyzing whole site
    		echo 'site. under construction';
    	} else {
    		echo 'You entered ' . $board . ', which i don\'t know what to do with. <a href = "feedback">Is this an error?</a>';
    	}

    	$counts = $this->getCounts($posts);
        $words = array_keys($counts);

        foreach ($counts as $word => $c){
            echo $word . ': ' . $c . '<br>';
            //echo $word . '<br>';
        }
        echo '[end]';
    }
    
    private function getCounts($posts){
    	$counts = array();

    		for ($i = 0; $i < sizeof($posts); $i++){
    			echo 'comment ' . $i . ':<br>';

                //replace line break tags
                $posts[$i] = str_replace('<br>', ' ', $posts[$i]);

    			$words = explode(' ', $posts[$i]);

    			//removing unnecessary words and >
    			$removedWords = ['a', 'the'];
    			var_dump($words);
    			for ($j = 0; $j < sizeof($words); $j++){ 

    				filter_var($words[$j], FILTER_SANITIZE_STRING);
    				$words[$j] = strip_tags($words[$j]);

    				if (strpos($words[$j], 'class') !== FALSE || strpos($words[$j], 'href') !== FALSE){
    					unset($words[$j]);
    					continue;
    				} else if (strpos($words[$j], '>>') !== FALSE || strpos($words[$j], '<') !== FALSE){
    					unset($words[$j]);
    					continue;
    				}
    				if (strpos($words[$j], '>')){
    					$words[$j] = substr($words[$j], 1);
    				}
    				if (in_array($words[$j], $removedWords)){
    					unset($words[$j]);
    					continue;
    				}


    				//dealing with punctuation, case, special char, all that shit    				
    				$words[$j] = strtolower($words[$j]);
                    if (strpos($words[$j], urlencode("http://")) === FALSE || strpos($words[$j], urlencode('https://')) == FALSE){
    				    $words[$j] = preg_replace('/[^a-z0-9]+/i', '', $words[$j]);
    			     } else {
                        echo $words[$j] . ' is a url';
                     }

                     echo '<br>';
                }
    			$words = array_values($words);
    			//generating counts
    			for ($j = 0; $j < sizeof($words); $j++){
    				echo htmlspecialchars($words[$j]) . '<br>';
    				if (array_key_exists($words[$j], $counts)){
    					$counts[$words[$j]]++;
    				} else {
    					$counts[$words[$j]] = 1;
    				}
    			}

    			//var_dump($counts);
                
    		}	
            return $counts;
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
    			for ($i = 0; $i < sizeof($postsArray); $i++){
    				if (property_exists($postsArray[$i]	, 'com')){
    					array_push($posts, $postsArray[$i]->com);
    				}
    			}
    			return $posts;
    	} else if (ctype_alpha($board)){

    		$threadIds = array();

    		
			for ($i = 1; $i < 11; $i++){
				$result = $client->get($board . '/' . $i . '.json');
				$bodyString = $result->getBody()->getContents();
				$obj = json_decode($bodyString);
				for ($j = 0; $j < sizeof($obj->threads); $j++){ //get all thread ids from all pages
					array_push($threadIds, $obj->threads[$j]->posts[0]->no);
				}
			}
			for ($i = 0; $i < sizeof($threadIds); $i++){
				array_push($posts, $this->getPosts($board, $threadIds[$i]));
				sleep(1); //api rules: no more than one request per second
				var_dump($posts[sizeof($posts) - 1]);
			}
			return $posts;
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
