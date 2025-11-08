<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use PhpMqtt\Client\Facades\MQTT;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\Exceptions\MqttClientException;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Api\ApiController;

class MqttSubcriberJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        // MqttSubcriberJob::dispatch();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $mqtt = MQTT::connection();
            Cache::forever('mqtt_connected', 'Terhubung dengan Broker MQTT');
            
            try {
                $topic = env('MQTT_TOPIC_SUBS', 'iot/SubsMessage');
                $mqtt->subscribe($topic, function (string $topic, string $message) use ($mqtt) {
                    $decodedMessage = json_decode($message, true);
            
                    if ($decodedMessage) {
                        app(ApiController::class)->handleMqttData($decodedMessage);
                    }
                }, 0);
            
                $mqtt->loop(true);
                sleep(1);
            
            } finally {
                Cache::put('mqtt_connected', 'Terputus dengan Broker MQTT', 60);
                $mqtt->disconnect();
            }
            
        } catch (MqttClientException $e) {
            Log::error('An error occurred while subscribing to MQTT topic: ' . $e->getMessage());
        }
    }
}
