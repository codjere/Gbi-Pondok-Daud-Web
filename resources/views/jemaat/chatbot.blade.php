@extends('layouts.app')

@section('title', 'Asisten GBI Pondok Daud')
@section('breadcrumb', 'Jemaat / Chatbot AI')

@push('styles')
<style>
    .chat-scroll { scroll-behavior: smooth; }
    .msg-user { background: #1B4332; color: white; border-radius: 18px 18px 4px 18px; }
    .msg-bot { background: white; color: #292524; border-radius: 18px 18px 18px 4px; border: 1px solid #e7e5e4; }
    .typing-dot { animation: bounce 1.2s infinite; }
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }
    @keyframes bounce { 0%, 80%, 100% { transform: translateY(0); } 40% { transform: translateY(-6px); } }
</style>
@endpush

@section('content')
<div class="mx-auto flex h-[calc(100vh-180px)] max-w-3xl flex-col">
    @include('partials.flash')

    <div class="mb-4 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-forest">Asisten GBI Pondok Daud</h2>
            <p class="text-xs text-stone-500">Chatbot AI · RAG + NLP · GBI Pondok Daud</p>
        </div>
        @if ($serviceOnline)
            <span class="rounded-full bg-emerald-50 px-3 py-1 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-200">
                <span class="mr-1 inline-block h-1.5 w-1.5 rounded-full bg-emerald-500"></span>AI Online
            </span>
        @else
            <span class="rounded-full bg-amber-50 px-3 py-1 text-[10px] font-bold text-amber-700">Mode Offline</span>
        @endif
    </div>

    <div id="chatBox" class="chat-scroll flex-1 space-y-4 overflow-y-auto rounded-2xl bg-cream/80 p-5 ring-1 ring-stone-200/80">
        <div class="msg-bot max-w-[85%] px-4 py-3 text-sm">
            Shalom! 🙏 Saya <strong>Asisten GBI Pondok Daud</strong>.<br><br>
            Tanyakan seputar jadwal ibadah, COOL, pelayanan, pembaptisan, pernikahan, atau minta renungan.<br><br>
            <span class="text-xs text-stone-400">Contoh: "Ibadah pemuda jam berapa?" · "Cara daftar pembaptisan?"</span>
        </div>

        @foreach ($messages as $msg)
            <div class="flex {{ $msg->role === 'user' ? 'justify-end' : 'justify-start' }}">
                <div class="{{ $msg->role === 'user' ? 'msg-user' : 'msg-bot' }} max-w-[85%] px-4 py-3 text-sm whitespace-pre-wrap">{{ $msg->content }}</div>
            </div>
        @endforeach

        <div id="typing" class="hidden">
            <div class="msg-bot inline-flex items-center gap-1 px-4 py-3">
                <span class="typing-dot h-2 w-2 rounded-full bg-stone-400"></span>
                <span class="typing-dot h-2 w-2 rounded-full bg-stone-400"></span>
                <span class="typing-dot h-2 w-2 rounded-full bg-stone-400"></span>
            </div>
        </div>
    </div>

    <form id="chatForm" class="mt-4 flex gap-2">
        <input type="text" id="chatInput" placeholder="Ketik pertanyaan Anda..."
               class="flex-1 rounded-2xl bg-white px-4 py-3.5 text-sm ring-1 ring-stone-200 outline-none focus:ring-2 focus:ring-forest/30"
               autocomplete="off" maxlength="1000">
        <button type="submit" id="sendBtn"
                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-forest text-white hover:bg-forest-mid">
            <i class="fa-solid fa-paper-plane text-sm"></i>
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const chatBox = document.getElementById('chatBox');
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    const typing = document.getElementById('typing');
    const sendUrl = @json(route('jemaat.chat.send'));
    const csrf = @json(csrf_token());
    let conversationId = @json($conversationId);

    function appendMsg(role, text) {
        const wrap = document.createElement('div');
        wrap.className = 'flex ' + (role === 'user' ? 'justify-end' : 'justify-start');
        const bubble = document.createElement('div');
        bubble.className = (role === 'user' ? 'msg-user' : 'msg-bot') + ' max-w-[85%] px-4 py-3 text-sm whitespace-pre-wrap';
        bubble.textContent = text;
        wrap.appendChild(bubble);
        chatBox.insertBefore(wrap, typing);
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const msg = chatInput.value.trim();
        if (!msg) return;

        appendMsg('user', msg);
        chatInput.value = '';
        typing.classList.remove('hidden');
        chatBox.scrollTop = chatBox.scrollHeight;

        try {
            const res = await fetch(sendUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: JSON.stringify({ message: msg, conversation_id: conversationId })
            });
            const data = await res.json();
            typing.classList.add('hidden');

            if (data.success) {
                conversationId = data.conversation_id;
                appendMsg('assistant', data.answer);
            } else {
                appendMsg('assistant', data.message || 'Maaf, terjadi kesalahan.');
            }
        } catch (err) {
            typing.classList.add('hidden');
            appendMsg('assistant', 'Gagal menghubungi server. Pastikan ai-service/start.bat berjalan.');
        }
    });
})();
</script>
@endpush
