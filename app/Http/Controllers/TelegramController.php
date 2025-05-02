<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    public function handle(Request $request)
    {
        try {
            if (!$request->has('message')) {
                Log::warning('Invalid Telegram webhook request - no message');
                return response('Invalid request', 400);
            }

            $update = Telegram::getWebhookUpdate();

            Log::info('Telegram Update Processed', [
                'update' => $update->toArray()
            ]);

            $message = $update->getMessage();
            $chatId = $message->getChat()->getId();
            $text = $message->getText();

            if ($text === '/start') {
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => "🔥 Bem-vindo ao FURIA Fan Chat!\n\nUse /help para ver os comandos disponíveis."
                ]);
                return response('OK', 200);
            }

            if ($text === '/help') {
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => "Comandos disponíveis:\n\n/start - Iniciar o bot\n/help - Ver ajuda"
                ]);
                return response('OK', 200);
            }

            // Responde à mensagem
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => "Você disse: $text\nA FURIA te saúda! 🔥"
            ]);

            return response('OK', 200);
        } catch (\Exception $e) {
            Log::error('Telegram Webhook Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response('Error processing webhook', 500);
        }
    }
}

