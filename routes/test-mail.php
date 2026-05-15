<?php
// Test file untuk cek email
Route::get('/test-mail', function() {
    try {
        $details = ['name' => 'Test User', 'link' => 'http://localhost/test'];
        \Illuminate\Support\Facades\Mail::send('emails.invite_operator', $details, function($message) {
            $message->to('winarbuk@gmail.com')
                    ->subject('Test Email - Berliano Leather');
        });
        
        if (\Illuminate\Support\Facades\Mail::failures()) {
            return "Mail failures: " . json_encode(\Illuminate\Support\Facades\Mail::failures());
        }
        
        return "Email berhasil dikirim!";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage() . "\n" . $e->getTraceAsString();
    }
});