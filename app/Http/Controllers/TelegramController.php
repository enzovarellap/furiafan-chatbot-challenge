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

            if ($update->isType('message')) {
                $message = $update->getMessage();
                $chatId = $message->getChat()->getId();
                $text = trim($message->getText());

                if ($text === '/start') {
                    $this->sendStartMessage($chatId);
                    return response('OK', 200);
                }

                if ($text === '/help') {
                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => "📌 Comandos disponíveis:\n\n/start - Iniciar o bot\n/help - Ver ajuda"
                    ]);
                    return response('OK', 200);
                }

                $this->sendStartMessage($chatId);

                return response('OK', 200);
            }

            if ($update->isType('callback_query')) {
                $callback = $update->getCallbackQuery();
                $data = $callback->getData();
                $chatId = $callback->getMessage()->getChat()->getId();

                switch ($data) {
                    case 'proximo_jogo':
                        $this->sendNextGameInfo($chatId);
                        break;

                    case 'elenco':
                        Telegram::sendMessage([
                            'chat_id' => $chatId,
                            'text' => "🎯 Elenco Atual da FURIA:\n- KSCERATO\n- yuurih\n- molodoy\n- YEKINDAR\n- FalleN"
                        ]);
                        break;

                    case 'ultimos_resultados':
                        Telegram::sendMessage([
                            'chat_id' => $chatId,
                            'text' => "📊 Últimos Resultados:\n- FURIA 0x2 The MongolZ\n- FURIA 0x2 VirtusPro\n- FURIA 1x2 Complexity"
                        ]);
                        break;

                    case 'votar_mvp':
                        $this->sendMvpPoll($chatId);
                        break;

                    case 'receber_notificacoes':
                        Telegram::sendMessage([
                            'chat_id' => $chatId,
                            'text' => "🔔 Você se inscreveu para receber notificações!"
                        ]);
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

    protected function sendNextGameInfo($chatId)
    {
        $dataJogo = Carbon::parse('2025-05-10 10:00:00');
        $tempoRestante = now()->diffForHumans($dataJogo, ['parts' => 2, 'short' => true]);
        $tempoRestante = str_replace('before', ' ', $tempoRestante);

        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => "🎮 Próximo Jogo:\nFURIA vs The MongolZ\n🗓️ 10/05 às 10h\nFormato: BO3\n⏳ Começa em $tempoRestante!"
        ]);
    }

    protected function sendMvpPoll($chatId)
    {
        Telegram::sendPoll([
            'chat_id' => $chatId,
            'question' => "🔥 Quem foi o MVP da última partida?",
            'options' => ['FalleN', 'yuurih', 'KSCERATO', 'YEKINDAR', 'molodoy'],
            'is_anonymous' => false,
        ]);
    }
    protected function sendStartMessage($chatId): void
    {
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => "🔥 Bem-vindo ao FURIA Fan Chat!\n\nEscolha uma opção abaixo:",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => 'Próximo Jogo', 'callback_data' => 'proximo_jogo']],
                    [['text' => 'Elenco Atual', 'callback_data' => 'elenco']],
                    [['text' => 'Últimos Resultados', 'callback_data' => 'ultimos_resultados']],
                    [['text' => 'Votar MVP 🔥', 'callback_data' => 'votar_mvp']],
                    [['text' => 'Receber Notificações 🔔', 'callback_data' => 'receber_notificacoes']]
                ]
            ], JSON_THROW_ON_ERROR)
        ]);
    }
}
