<?php

namespace Bundle\PaymentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Credit implements CreditInterface
{
    protected $creditedAmount;
    protected $creditingAmount;
    protected $id;
    protected $paymentInstruction;
    protected $transactions;
    protected $reversingAmount;
    protected $state;
    protected $targetAmount;
    
    public function __construct(PaymentInstructionInterface $paymentInstruction, $amount)
    {
        $this->creditedAmount = 0.0;
        $this->creditingAmount = 0.0;
        $this->paymentInstruction = $paymentInstruction;
        $this->transactions = new ArrayCollection;
        $this->reversingAmount = 0.0;
        $this->state = self::STATE_NEW;
        $this->targetAmount = $amount;
        
        $this->paymentInstruction->addCredit($this);
    }
    
    public function addTransaction(FinancialTransaction $transaction)
    {
        $this->transactions->add($transaction);
        $transaction->setCredit($this);
    }
    
    public function getCreditedAmount()
    {
        return $this->creditedAmount;    
    }
    
    public function getCreditingAmount()
    {
        return $this->creditingAmount;
    }
    
    public function getCreditTransaction()
    {
        foreach ($this->transactions as $transaction) {
            if (FinancialTransactionInterface::TRANSACTION_TYPE_CREDIT === $transaction->getTransactionType()) {
                return $transaction;
            }
        }
        
        return null;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getPaymentInstruction()
    {
        return $this->paymentInstruction;
    }
    
    public function getPendingTransaction()
    {
        foreach ($this->transactions as $transaction) {
            if (FinancialTransactionInterface::STATE_PENDING === $transaction->getState()) {
                return $transaction;
            }
        }
        
        return null;
    }
    
    public function getReverseCreditTransactions()
    {
        return $this->transactions->filter(function($transaction) {
            return FinancialTransactionInterface::TRANSACTION_TYPE_REVERSE_CREDIT === $transaction->getTransactionType();
        });
    }
    
    public function getReversingAmount()
    {
        return $this->reversingAmount;
    }
    
    public function getState()
    {
        return $this->state;
    }
    
    public function getTargetAmount()
    {
        return $this->targetAmount;
    }
    
    public function hasPendingTransaction()
    {
        return null !== $this->getPendingTransaction();
    }
    
    public function setCreditedAmount($amount)
    {
        $this->creditedAmount = $amount;
    }
    
    public function setCreditingAmount($amount)
    {
        $this->creditingAmount = $amount;
    }
    
    public function setReversingAmount($amount)
    {
        $this->reversingAmount = $amount;
    }    
}