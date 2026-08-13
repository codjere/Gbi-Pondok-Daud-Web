<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Face Recognition Service (FaceNet)
    |--------------------------------------------------------------------------
    |
    | URL layanan Python FastAPI untuk ekstraksi embedding wajah.
    | Jalankan: face-service/start.bat
    |
    */

    'service_url' => env('FACE_SERVICE_URL', 'http://127.0.0.1:5001'),

    'threshold' => (float) env('FACE_THRESHOLD', 0.60),

    'samples_required' => (int) env('FACE_SAMPLES_REQUIRED', 5),

    'timeout' => (int) env('FACE_SERVICE_TIMEOUT', 30),

];
