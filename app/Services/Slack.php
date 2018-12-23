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
	 * @return object
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
	 * @return object
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

	/**
	 * Get users as array ['id', 'name']
	 * @return array
	 */
	public function getUsersNames() {
		$usersObject = $this->getUsers();
		if ($usersObject->ok == true) {
			$users = $usersObject->members;
			foreach ($users as $user) {
				$usersNames[$user->id] = $user->name;
			}
			return ['data' => $usersNames, 'ok' => true];
		}
		return ['ok' => false, 'data' => [$usersObject]];
	}

	/**
	 * Get channels as array ['id', 'name']
	 * @return array
	 */
	public function getChannelsNames() {
		$channelsObject = $this->getChannels();
		if ($channelsObject->ok == true) {
			$channels = $channelsObject->channels;
			foreach ($channels as $channel) {
				$channelsNames[$channel->id] = $channel->name;
			}
			return ['data' => $channelsNames, 'ok' => true];
		}
		return ['ok' => false, 'data' => [$channelsObject]];
	}
}