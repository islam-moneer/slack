<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class HomeController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }


    /**
     * Create slack channel
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'channel' => 'required'
        ]);

        $payload = [ 
            'query' => [
                'token' => env('SLACK_TOKEN'),
            ],
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded; application/json'],
            'form_params' => [
                'name' => $request->input('channel')
            ]
        ];
        
        $slack_request = $this->client->request('POST','channels.create',$payload);
        $response = json_decode($slack_request->getBody());

        if ( $response->ok == false ) {
            return redirect()->back()->with([
                'error' => 'Failed to create channel', 
                'log' => $slack_request->getBody()
            ]);
        }

        return redirect()->back()->with([
            'status' => $response->ok == true?'Channel created Successfully!':'Error while creating channel!'
            ]);
    }

    public function invite(Request $request) 
    {
        $this->validate($request, [
            'channels' => 'required',
            'user' => 'required'
        ]);

        $payload = [ 
            'query' => [
                'token' => env('SLACK_TOKEN'),
            ],
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded; application/json'],
            'form_params' => [
                'channel' => $request->input('channels'),
                'user' => $request->input('user')
            ]
        ];
        
        $slack_request = $this->client->request('POST','channels.invite',$payload);
        $response = json_decode($slack_request->getBody());

        if ( $response->ok == false ) {
            return redirect()->back()->with([
                'error' => 'Failed to invite user', 
                'log' => $slack_request->getBody()
            ]);
        }

        return redirect()->back()->with([
            'status' => $response->ok == true?'User invited to the channel!':'Error while sending invitation!'
            ]);
    }

    public function getUsers() {
        $service = new \App\Services\Slack();
        dd($service->getUsers());
    }
}
