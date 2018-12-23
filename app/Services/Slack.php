<?php 
Namespace App\Services;

use GuzzleHttp\Client;

class Slack
{
	protected $client;

	function __construct()
	{
		$this->client =  new Client(['base_uri' =>'https://slack.com/api/']);
	}

	/**
	 * Return Channels of slack team
	 */
	public function getChannels()
	{
		$payload = [ 
            'query' => [
                'token' => env('SLACK_TOKEN'),
            ],
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
        ];
        $response = $this->client->request('GET','conversations.list',$payload);
        $channels = json_decode($response->getBody());
        return $channels;
	}

	/**
	 * Return users of slack team
	 */
	public function getUsers()
	{
		$payload = [ 
            'query' => [
                'token' => env('SLACK_TOKEN'),
            ],
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
        ];
        $response = $this->client->request('GET','users.list',$payload);
        $users = json_decode($response->getBody());
        return $users;
	}
}