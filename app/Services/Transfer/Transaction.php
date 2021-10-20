<?php

namespace App\Services\Transfer;

use App\Constants\TransactionConstant;
use App\Models\Transaction as TransactionModel;
use App\Models\Wallet;
use App\Repositories\Transaction\Contracts\TransactionRepositoryInterface;
use App\Repositories\Wallet\Contracts\WalletRepositoryInterface;

class Transaction
{
    /**
     * @var Wallet
     */
    private $payerWallet;

    /**
     * @var Wallet
     */
    private $payeeWallet;

    /**
     * @var float
     */
    private $value;

    /**
     * @var TransactionModel
     */
    private $transaction;

    /**
     * @var TransactionRepositoryInterface
     */
    private $transactionRepository;

    /**
     * @var WalletRepositoryInterface
     */
    private $walletRepository;

    /**
     * @param TransactionRepositoryInterface $transactionRepository
     * @param WalletRepositoryInterface $walletRepository
     *
     * @return void
     */
    public function __construct(
        TransactionRepositoryInterface $transactionRepository,
        WalletRepositoryInterface $walletRepository
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->walletRepository      = $walletRepository;
    }

    /**
     * @param Wallet $payerWallet
     *
     * @return self
     */
    public function setPayerWallet(Wallet $payerWallet): self
    {
        $this->payerWallet = $payerWallet;

        return $this;
    }

    /**
     * @param Wallet $payeeWallet
     *
     * @return self
     */
    public function setPayeeWallet(Wallet $payeeWallet): self
    {
        $this->payeeWallet = $payeeWallet;

        return $this;
    }

    /**
     * @param float $value
     *
     * @return self
     */
    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return self
     */
    public function requested(): self
    {
        $this->transaction = $this->transactionRepository->create([
            'payer_wallet_id' => $this->payerWallet->id,
            'payee_wallet_id' => $this->payeeWallet->id,
            'value'           => $this->value,
        ]);

        return $this;
    }

    /**
     * @return self
     */
    public function completed(): self
    {
        $this->transactionRepository->update($this->transaction, [
            'status' => TransactionConstant::STATUS_COMPLETED
        ]);

        return $this;
    }

    /**
     * @return self
     */
    public function withdrawWalletPayer(): self
    {
        $this->walletRepository->update($this->payerWallet, [
            'balance' => $this->payerWallet->balance - $this->value
        ]);

        return $this;
    }

    /**
     * @return self
     */
    public function depositWalletPayee(): self
    {
        $this->walletRepository->update($this->payeeWallet, [
            'balance' => $this->payeeWallet->balance + $this->value
        ]);

        return $this;
    }

    /**
     * @return self
     */
    public function failed(): self
    {
        $this->transactionRepository->update($this->transaction, [
            'status' => TransactionConstant::STATUS_FAILED
        ]);

        return $this;
    }

    /**
     * @return TransactionModel
     */
    public function get(): ?TransactionModel
    {
        return $this->transaction;
    }
}
