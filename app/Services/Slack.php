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
}