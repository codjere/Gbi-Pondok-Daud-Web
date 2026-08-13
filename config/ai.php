<?php

return [

    'service_url' => env('AI_SERVICE_URL', 'http://127.0.0.1:5002'),

    'timeout' => (int) env('AI_SERVICE_TIMEOUT', 30),

    'openai_api_key' => env('OPENAI_API_KEY', ''),

    'top_k' => (int) env('AI_RECOMMEND_TOP_K', 3),

];
