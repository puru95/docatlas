<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\UserInteraction;
use Illuminate\Support\Facades\Log;

class OpenAIAssistantService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.openai.com/v1';

    public function __construct()
    {

        $this->apiKey = config('services.privateai.key');
    }

    protected function headers()
    {
        return [
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2' // Required for Assistants API
        ];
    }

    public function createThread()
    {
        $response = Http::withHeaders($this->headers())
            ->post("{$this->baseUrl}/threads");

        Log::info($response);

        return $response->json()['id'] ?? null;
    }

    public function sendMessage(string $threadId, string $message)
    {
        $response = Http::timeout(1000100)->withHeaders($this->headers())
            ->post("{$this->baseUrl}/threads/{$threadId}/messages", [
                'role' => 'user',
                'content' => $message,
            ]);
        Log::info("sendMessage - $response");
        return $response->json();
    }

    public function runAssistant(string $threadId, string $assistantId, string $type)
    {
        $payload = [
            'assistant_id' => $assistantId,
        ];
    
        if ($type === 'json') {
            $payload['instructions'] = 'Please respond in a structured JSON format with the treatment plan.';
        }
        
        $response = Http::withHeaders($this->headers())
            ->post("{$this->baseUrl}/threads/{$threadId}/runs", [
                'assistant_id' => $assistantId,
                // 'instructions' => 'response in json format',
            ]);
        Log::info("runAssistant - $response");
        return $response->json();
    }

    public function checkRunStatus(string $threadId, string $runId)
    {
        $response = Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/threads/{$threadId}/runs/{$runId}");
        Log::info("checkRunStatus - $response");
        return $response->json();
    }

    public function getMessages(string $threadId)
    {
        $response = Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/threads/{$threadId}/messages");
        Log::info("getMessages - $response");
        return $response->json()['data'] ?? [];
    }

    public function chat(string $userInput, string $assistantId, ?string $threadId = null, $type='text')
    {
        Log::info("openAI start");
        // Create new thread if not existing
        $threadId = $threadId ?: $this->createThread();
        Log::info("ThreadId - $threadId");
        // Send user message
        $this->sendMessage($threadId, $userInput);

        // Run assistant
        $run = $this->runAssistant($threadId, $assistantId, $type);

        // Polling until the run completes (you may want to use jobs for long runs)
        do {
            sleep(3);
            $runStatus = $this->checkRunStatus($threadId, $run['id']);
        } while ($runStatus['status'] !== 'completed');

        // Get all messages
        $messages = $this->getMessages($threadId);
        // Save to DB
        // foreach ($messages as $msg) {
        //     if ($msg['role'] === 'assistant') {
        //         UserInteraction::create([
        //             'user_id' => auth()->id(),
        //             'thread_id' => $threadId,
        //             'question' => $userInput,
        //             'answer' => $msg['content'][0]['text']['value'] ?? '',
        //             'is_final_plan' => str_contains(strtolower($msg['content'][0]['text']['value']), 'treatment plan'),
        //         ]);
        //     }
        // }
// dd($messages);
        return [
            'thread_id' => $threadId,
            'messages' => $messages,
        ];
    }
}
