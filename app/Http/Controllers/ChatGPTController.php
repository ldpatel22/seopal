<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class ChatGPTController extends Controller
{
    protected $httpClient;
   
    public function __construct()
    {
        $this->httpClient = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . env('CHATGPT_API_KEY'),
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function askToChatGpt( $task )
    {
        $message = $task;
        $response = $this->httpClient->post('chat/completions', [
            'json' => [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are'],
                    ['role' => 'user', 'content' => $message],
                ],
            ],
        ]);

        return json_decode($response->getBody(), true)['choices'][0]['message']['content'];
    }
}