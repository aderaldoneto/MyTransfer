<?php

namespace App\Services;

use App\Enums\UserType;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Balance;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class TransferService
{
    private const AUTHORIZE_URL = 'https://util.devi.tools/api/v2/authorize';
    private const NOTIFY_URL = 'https://util.devi.tools/api/v1/notify';

    /**
     * @param User $sender   Usuário que envia (remetente)
     * @param User $receiver Usuário que recebe (destinatário)
     * @param int  $amount   Valor em centavos
     */
    public function transfer(User $sender, User $receiver, int $amount): Transaction
    {
        $this->validateTransfer($sender, $receiver, $amount);

        $this->validateExternalAuthorization();

        // operação no banco
        $transaction = DB::transaction(function () use ($sender, $receiver, $amount) {

            $senderBalance = Balance::where('user_id', $sender->id)
                ->lockForUpdate()
                ->firstOrFail();

            $receiverBalance = Balance::where('user_id', $receiver->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($senderBalance->amount < $amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Saldo insuficiente para realizar a transferência!',
                ]);
            }

            $senderBalance->amount -= $amount;
            $senderBalance->save();

            $receiverBalance->amount += $amount;
            $receiverBalance->save();

            return Transaction::create([
                'sender_id'       => $sender->id,
                'receiver_id'     => $receiver->id,
                'type'            => TransactionType::TRANSFER->value,
                'amount'          => $amount,
                'status'          => TransactionStatus::COMPLETED->value,
                'authorization_id'=> null, 
                'notified_at'     => null,
            ]);
        });

        $this->notifyReceiver($transaction, $receiver);

        return $transaction;
    }


    protected function validateTransfer(User $sender, User $receiver, int $amount): void
    {
        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'Saldo insuficiente para realizar a transferência!',
            ]);
        }

        if ($sender->id === $receiver->id) {
            throw ValidationException::withMessages([
                'receiver_id' => 'Não pode enviar para si mesmo!',
            ]);
        }

        if ($sender->type === UserType::EMPRESA) {
            throw ValidationException::withMessages([
                'sender_id' => 'Empresas não podem enviar transferências!',
            ]);
        }
    }

    /**
     * @throws RuntimeException
     */
    protected function validateExternalAuthorization(): void
    {
        $response = Http::get(self::AUTHORIZE_URL);

        if (!$response->ok()) {
            throw new RuntimeException('Falha em consultar serviço autorizador externo!');
        }

        $data = $response->json();
        \Log::info('Retorno do externo: ', $data);

    }

    protected function notifyReceiver(Transaction $transaction, User $receiver): void
    {

        try {

            $payload = [
                'email'   => $receiver->email,
                'subject' => 'Você recebeu uma transferência!',
                'message' => sprintf(
                    'Você recebeu uma transferência de R$ %s.',
                    number_format($transaction->amount / 100, 2, ',', '.')
                ),
            ];

            $response = Http::post(self::NOTIFY_URL, $payload);

            if (! $response->ok()) {
                throw new RuntimeException('Falha ao notificar o destinatário!');
            }

            $transaction->update([
                'notified_at' => now(),
            ]);

        } catch (\Throwable $e) {

            Log::warning('Falha ao enviar notificação de transferência', [
                'transaction_id' => $transaction->id,
                'error'          => $e->getMessage(),
            ]);

        }
    }
}
