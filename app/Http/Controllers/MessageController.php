<?php

namespace App\Http\Controllers;

use Pusher\Pusher;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use BaklySystems\LaravelMessenger\Models\Message;
use BaklySystems\LaravelMessenger\Facades\Messenger;
use BaklySystems\LaravelMessenger\Models\Conversation;

class MessageController extends Controller
{
    private $client;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['web', 'auth']);
        $this->client =  new Client(['base_uri' =>'https://slack.com/api/']);
    }

    /**
     * Get messenger page.
     *
     * @param  int  $withId
     * @return Response
     */
    public function laravelMessenger($withId)
    {
        Messenger::makeSeen(auth()->id(), $withId);
        $withUser = config('messenger.user.model', 'App\User')::findOrFail($withId);
        $messages = Messenger::messagesWith(auth()->id(), $withUser->id);
        $threads  = Messenger::threads(auth()->id());

        return view('messenger::messenger', compact('withUser', 'messages', 'threads'));
    }

    /**
     * Get messenger page.
     *
     * @param  int  $withId
     * @return Response
     */
    public function channelMessenger($withId)
    {
        // Get messages from slack
        $payload = [ 
            'query' => [
                'token' => env('SLACK_TOKEN'),
                'channel' => $withId
            ],
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
        ];
        
        $slack_request = $this->client->request('GET','conversations.history',$payload);
        $response = json_decode($slack_request->getBody());
        $response = $response->messages;

        return view('messenger::slack', compact('response', 'withId'));
    }

    /**
     * Create a new message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, Message::rules());

        $authId = auth()->id();
        $withId = $request->withId;
        $conversation = Messenger::getConversation($authId, $withId);

        if (! $conversation) {
            $conversation = Messenger::newConversation($authId, $withId);
        }

        $message = Messenger::newMessage($conversation->id, $authId, $request->message);

        // Pusher
        $pusher = new Pusher(
            config('messenger.pusher.app_key'),
            config('messenger.pusher.app_secret'),
            config('messenger.pusher.app_id'),
            [
                'cluster' => config('messenger.pusher.options.cluster')
            ]
        );
        $pusher->trigger('messenger-channel', 'messenger-event', [
            'message'    => $message,
            'senderId'   => $authId,
            'withId'     => $withId
        ]);

        return response()->json([
            'success' => true,
            'message' => $message
        ], 200);
    }
    /**
     * Create a new message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function storeSlack(Request $request)
    {
        $this->validate($request, [
            'message' => 'required',
            'withId' => 'required',
            'user' => 'required'
        ]);
        $payload = [ 
            'query' => [
                'token' => env('SLACK_TOKEN'),
            ],
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded; application/json'],
            'form_params' => [
                'channel' => $request->input('withId'),
                'text' => $request->input('message')
            ]
        ];
        
        $slack_request = $this->client->request('POST','chat.postMessage',$payload);
        $response = json_decode($slack_request->getBody());
        // dd($request->all());
        // $authId = auth()->id();
        // $withId = $request->withId;
        // $conversation = Messenger::getConversation($authId, $withId);

        // if (! $conversation) {
        //     $conversation = Messenger::newConversation($authId, $withId);
        // }

        // $message = Messenger::newMessage($conversation->id, $authId, $request->message);

        if ( $response->ok == true ) {
            // Pusher
            $pusher = new Pusher(
                config('messenger.pusher.app_key'),
                config('messenger.pusher.app_secret'),
                config('messenger.pusher.app_id'),
                [
                    'cluster' => config('messenger.pusher.options.cluster')
                ]
            );
            $pusher->trigger('messenger-channel', 'messenger-event', [
                'message'    => $request->input('message'),
                'senderId'   => $request->input('user'),
                'withId'     => $request->input('withId')
            ]);
        }
        

        return response()->json([
            'success' => true,
            'message' => '$message'
        ], 200);
    }

    /**
     * Load threads view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response.
     */
    public function loadThreads(Request $request)
    {
        if ($request->ajax()) {
            $withUser = config('messenger.user.model', 'App\User')::findOrFail($request->withId);
            $threads  = Messenger::threads(auth()->id());
            $view     = view('messenger::partials.threads', compact('threads', 'withUser'))->render();

            return response()->json($view, 200);
        }
    }

    /**
     * Load more messages.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response.
     */
    public function moreMessages(Request $request)
    {
        $this->validate($request, ['withId' => 'required|integer']);

        if ($request->ajax()) {
            $messages = Messenger::messagesWith(
                auth()->id(),
                $request->withId,
                $request->take
            );
            $view = view('messenger::partials.messages', compact('messages'))->render();

            return response()->json([
                'view'          => $view,
                'messagesCount' => $messages->count()
            ], 200);
        }
    }

    /**
     * Make a message seen.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function makeSeen(Request $request)
    {
        Messenger::makeSeen($request->authId, $request->withId);

        return response()->json(['success' => true], 200);
    }

    /**
     * Delete a message.
     *
     * @param  int  $id
     * @return Response.
     */
    public function destroy($id)
    {
        $confirm = Messenger::deleteMessage($id, auth()->id());

        return response()->json(['success' => true], 200);
    }
}
