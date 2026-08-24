<?php

namespace App\Http\Controllers\Jemaat;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatbotController extends Controller
{
    public function __construct(
        private ChatbotService $chatbotService
    ) {}

    public function index()
    {
        $conversation = ChatConversation::where('user_id', Auth::id())
            ->latest()
            ->first();

        $messages = $conversation
            ? $conversation->messages()->orderBy('created_at')->get()
            : collect();

        return view('jemaat.chatbot', [
            'messages' => $messages,
            'conversationId' => $conversation?->id,
            'serviceOnline' => $this->chatbotService->isAvailable(),
        ]);
    }

    public function send(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'conversation_id' => ['nullable', 'exists:chat_conversations,id'],
        ]);

        $conversation = isset($data['conversation_id'])
            ? ChatConversation::where('user_id', Auth::id())
                ->whereKey($data['conversation_id'])
                ->firstOrFail()
            : ChatConversation::create([
                'user_id' => Auth::id(),
                'title' => substr($data['message'], 0, 50),
            ]);

        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $data['message'],
        ]);

        $history = $conversation->messages()
            ->orderBy('created_at')
            ->take(10)
            ->get()
            ->map(fn ($m) => ['role' => $m->role, 'content' => $m->content])
            ->all();

        $result = $this->chatbotService->chat($data['message'], $history);

        if ($result === null) {
            return response()->json([
                'success' => false,
                'message' => 'Chatbot tidak tersedia saat ini.',
            ], 503);
        }

        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => $result['answer'],
            'sources' => $result['sources'] ?? [],
        ]);

        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->id,
            'answer' => $result['answer'],
            'sources' => $result['sources'] ?? [],
            'intent' => $result['intent'] ?? 'umum',
        ]);
    }
}
