<?php

namespace Agenciafmd\Hubspot\Jobs;

use Http;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Message;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Log;

class SendConversionsToHubspot implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function handle(): void
    {
        if (!config('laravel-hubspot.portal_id') || !config('laravel-hubspot.form_id')) {
            return;
        }

        $this->sendConversion($this->data);
    }

    private function sendConversion(array $data = []): void
    {
        $portalId = config('laravel-hubspot.portal_id');
        $formGuid = config('laravel-hubspot.form_id');
        $url = "https://api.hsforms.com/submissions/v3/integration/submit/{$portalId}/{$formGuid}";

    $logger = Log::build([
        'driver' => 'daily',
        'path' => storage_path('logs/hubspot.log'),
        'days' => 14,
    ]);

    try {
        $response = Http::timeout(10)
            ->post($url, $data);

        if ($response->successful()) {
            $logger->info('Dados enviados com sucesso para HubSpot', [
                'status' => $response->status(),
                'payload' => $data,
            ]);
        } else {
            $logger->warning('Erro ao enviar dados para HubSpot', [
                'status' => $response->status(),
                'response' => $response->body(),
                'payload' => $data,
            ]);
        }
    } catch (\Exception $exception) {
        $logger->error('Exceção ao enviar dados para HubSpot', [
            'message' => $exception->getMessage(),
            'payload' => $data,
        ]);
    }

        if (($response->getStatusCode() !== 200) && (config('laravel-hubspot.error_email'))) {
            Mail::raw($response->getBody(), function (Message $message) {
                $message->to(config('laravel-hubspot.error_email'))
                    ->subject('[HubSpot][' . config('app.url') . '] - Falha na integração - ' . now()->format('d/m/Y H:i:s'));
            });
        }
    }
}
