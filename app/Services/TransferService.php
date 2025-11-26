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
    private const NOTIFY_URL    = 'https://util.devi.tools/api/v1/notify';

    /**
     * Realiza uma transferência entre usuários.
     *
     * @param  User  $sender   Usuário que envia (remetente)
     * @param  User  $receiver Usuário que recebe (destinatário)
     * @param  int   $amount   Valor em centavos
     */
    public function transfer(User $sender, User $receiver, int $amount): Transaction
    {
        // 1. Regras de negócio básicas (tipo, mesmo usuário, valor > 0, etc.)
        $this->validateTransfer($sender, $receiver, $amount);

        // 2. Autorização externa mockada
        $this->ensureAuthorized();

        // 3. Operação no banco (débito, crédito, registro da transação)
        $transaction = DB::transaction(function () use ($sender, $receiver, $amount) {

            // Busca saldo do remetente com lock pessimista
            $senderBalance = Balance::where('user_id', $sender->id)
                ->lockForUpdate()
                ->first();

            // Se não existir, cria com saldo 0 (fallback de segurança)
            if (! $senderBalance) {
                $senderBalance = Balance::create([
                    'user_id' => $sender->id,
                    'amount'  => 0,
                ]);
            }

            // Busca saldo do destinatário com lock
            $receiverBalance = Balance::where('user_id', $receiver->id)
                ->lockForUpdate()
                ->first();

            if (! $receiverBalance) {
                $receiverBalance = Balance::create([
                    'user_id' => $receiver->id,
                    'amount'  => 0,
                ]);
            }

            // Valida saldo suficiente
            if ($senderBalance->amount < $amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Saldo insuficiente para realizar a transferência!',
                ]);
            }

            // Debita remetente
            $senderBalance->amount -= $amount;
            $senderBalance->save();

            // Credita destinatário
            $receiverBalance->amount += $amount;
            $receiverBalance->save();

            // Registra transação
            return Transaction::create([
                'sender_id'        => $sender->id,
                'receiver_id'      => $receiver->id,
                'type'             => TransactionType::TRANSFER->value,
                'amount'           => $amount,
                'status'           => TransactionStatus::COMPLETED->value,
                'authorization_id' => null,
                'notified_at'      => null,
            ]);
        });

        // 4. Notificação (best effort – erro é apenas logado)
        $this->notifyReceiver($transaction, $receiver);

        return $transaction;
    }

    /**
     * Valida regras de negócio da transferência.
     */
    protected function validateTransfer(User $sender, User $receiver, int $amount): void
    {
        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'Informe um valor maior que zero!',
            ]);
        }

        if ($sender->id === $receiver->id) {
            throw ValidationException::withMessages([
                'receiver_id' => 'Não é possível enviar para você mesmo!',
            ]);
        }

        if ($sender->type === UserType::EMPRESA) {
            throw ValidationException::withMessages([
                'sender_id' => 'Empresas não podem enviar transferências!',
            ]);
        }
    }

    /**
     * Garante que o serviço externo autorizou a operação.
     *
     * @throws RuntimeException
     */
    protected function ensureAuthorized(): void
    {
        $response = Http::get(self::AUTHORIZE_URL);

        if (! $response->ok()) {
            throw new RuntimeException('Falha em consultar serviço autorizador externo!');
        }

        $data = $response->json();

        Log::info('Retorno do serviço autorizador externo', $data);

        // Mock costuma retornar { "data": { "authorization": true } }
        $authorized = data_get($data, 'data.authorization') ?? data_get($data, 'authorization');

        if (! $authorized) {
            throw new RuntimeException('Transação não autorizada pelo serviço externo!');
        }
    }

    /**
     * Tenta notificar o destinatário da transferência.
     *
     * Qualquer erro aqui é apenas logado, não afeta a transação.
     */
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
