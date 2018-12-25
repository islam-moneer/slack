<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class SlackController extends Controller
{
    private $client;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->client =  new Client(['base_uri' =>'https://slack.com/api/']);
    }

	/**
	 * Start a chat view
	 * @param type $id 
	 * @return view
	 */
	public function startConversation($id)
    {
    	// Get conversation history
    	$payload = [ 
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded;']
        ];
        $response = $this->client->request('GET','conversations.history?channel=' . $id . '&token=' . env('SLACK_TOKEN'),$payload);
        $history = json_decode($response->getBody());
        if ($history->ok = true) {
        	$history = array_reverse($history->messages);
        } else {
        	$history = '';
        }
        // connect to slack RTM
        $payload = [ 
            'query' => [
                'token' => env('SLACK_TOKEN'),
            ],
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
        ];
        $response = $this->client->request('GET','rtm.connect',$payload);
        $channels = json_decode($response->getBody());

        if ( $channels->ok == true ) {
            $url = $channels->url;
            return view('chat')->with(compact('url', 'id', 'history'));
        } else {
            return view('chat')->with(['errors' => 'failed to connect to the server']);
        }
    }


    public function send(Request $request, $id) 
    {
    	// $id -> refers to channel
    	// $request -> contain message and user id
        $payload = [ 
            'query' => [
                'token' => env('SLACK_TOKEN'),
            ],
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded; application/json'],
            'form_params' => [
                'channel' => $request->input('channel'),
                'text' => $request->input('message'),
                'as_user' => false
            ]
        ];
        
        $slack_request = $this->client->request('POST','chat.postMessage',$payload);
        $response = json_decode($slack_request->getBody());

        if ( $response->ok == false ) {
            return redirect()->back()->with([
                'error' => 'Failed to invite user', 
                'log' => $slack_request->getBody()
            ]);
        } else {
        	return response()->json(['data' => $response, 'status' => true]);
        }
    }

    public function getChannelHistory($channel) 
    {
		$payload1 = [ 
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded;']
        ];
        $response = $this->client->request('GET','conversations.history?channel=' . $channel . '&token=' . env('SLACK_TOKEN'),$payload1);
        $history = json_decode($response->getBody());
    }
}
