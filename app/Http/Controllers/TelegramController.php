<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TelegramController extends Controller
{
    public function handle(Request $request)
    {
        try {
            $update = Telegram::getWebhookUpdate();

            Log::info('Telegram Update Received', [
                'update' => $update->toArray()
            ]);

            // Trata mensagens normais
            if ($update->isType('message')) {
                $message = $update->getMessage();
                $chatId = $message->getChat()->getId();
                $text = trim($message->getText());

                if ($text === '/start') {
                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => "🔥 Bem-vindo ao FURIA Fan Chat!\n\nEscolha uma opção abaixo:",
                        'reply_markup' => json_encode([
                            'inline_keyboard' => [
                                [['text' => 'Próximo Jogo', 'callback_data' => 'proximo_jogo']],
                                [['text' => 'Elenco Atual', 'callback_data' => 'elenco']],
                                [['text' => 'Últimos Resultados', 'callback_data' => 'ultimos_resultados']],
                                [['text' => 'Votar MVP 🔥', 'callback_data' => 'votar_mvp']],
                            ]
                        ])
                    ]);
                    return response('OK', 200);
                }

                if ($text === '/help') {
                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => "📌 Comandos disponíveis:\n\n/start - Iniciar o bot\n/help - Ver ajuda"
                    ]);
                    return response('OK', 200);
                }

                // Fallback para mensagens genéricas
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => "Você disse: $text\nA FURIA te saúda! 🔥"
                ]);

                return response('OK', 200);
            }

            // Trata callback dos botões inline
            if ($update->isType('callback_query')) {
                $callback = $update->getCallbackQuery();
                $data = $callback->getData();
                $chatId = $callback->getMessage()->getChat()->getId();

                switch ($data) {
                    case 'proximo_jogo':
                        $this->sendProximoJogo($chatId);
                        break;

                    case 'elenco':
                        Telegram::sendMessage([
                            'chat_id' => $chatId,
                            'text' => "🎯 Elenco Atual da FURIA:\n- KSCERATO\n- yuurih\n- arT\n- chelo\n- FalleN"
                        ]);
                        break;

                    case 'ultimos_resultados':
                        Telegram::sendMessage([
                            'chat_id' => $chatId,
                            'text' => "📊 Últimos Resultados:\n- FURIA 2x1 MOUZ\n- FURIA 0x2 Heroic\n- FURIA 2x0 9z"
                        ]);
                        break;

                    case 'votar_mvp':
                        $this->enviarEnqueteMVP($chatId);
                        break;
                }

                return response('OK', 200);
            }

            return response('Ignored update', 200);
        } catch (\Exception $e) {
            Log::error('Telegram Webhook Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response('Error processing webhook', 500);
        }
    }

    protected function sendProximoJogo($chatId)
    {
        $dataJogo = Carbon::parse('2025-05-03 18:00:00');
        $tempoRestante = now()->diffForHumans($dataJogo, ['parts' => 2, 'short' => true]);

        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => "🎮 Próximo Jogo:\nFURIA vs Team Liquid\n🗓️ 03/05 às 18h\nFormato: BO3\n⏳ Começa em $tempoRestante!"
        ]);
    }

    protected function enviarEnqueteMVP($chatId)
    {
        Telegram::sendPoll([
            'chat_id' => $chatId,
            'question' => "🔥 Quem foi o MVP da última partida?",
            'options' => ['FalleN', 'yuurih', 'KSCERATO', 'chelo', 'arT'],
            'is_anonymous' => false,
        ]);
    }
}
