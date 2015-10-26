<?php

namespace BitWasp\Bitcoin\Transaction\Mutator;

use BitWasp\Bitcoin\Collection\Transaction\TransactionOutputCollection;
use BitWasp\Bitcoin\Transaction\TransactionOutputInterface;

class OutputCollectionMutator
{
    /**
     * @var TransactionOutputInterface[]
     */
    private $outputs;

    /**
     * @param TransactionOutputCollection $outputs
     */
    public function __construct(TransactionOutputCollection $outputs)
    {
        $this->outputs = $outputs->all();
    }

    /**
     * @param int $i
     * @return \BitWasp\Bitcoin\Transaction\TransactionOutputInterface
     */
    public function getOutput($i)
    {
        if (!isset($this->outputs[$i])) {
            throw new \OutOfRangeException('Output does not exist');
        }

        return $this->outputs[$i];
    }

    /**
     * @param int|string $i
     * @return OutputMutator
     */
    public function outputMutator($i)
    {
        return new OutputMutator($this->getOutput($i));
    }

    /**
     * @return TransactionOutputCollection
     */
    public function get()
    {
        return new TransactionOutputCollection($this->outputs);
    }

    /**
     * @param int|string $start
     * @param int|string $length
     * @return $this
     */
    public function slice($start, $length)
    {
        $end = count($this->outputs);
        if ($start > $end || $length > $end) {
            throw new \RuntimeException('Invalid start or length');
        }

        $this->outputs = array_slice($this->outputs, $start, $length);
        return $this;
    }

    /**
     * @return $this
     */
    public function null()
    {
        $this->slice(0, 0);
        return $this;
    }

    /**
     * @param TransactionOutputInterface $output
     * @return $this
     */
    public function add(TransactionOutputInterface $output)
    {
        $this->outputs[] = $output;
        return $this;
    }

    /**
     * @param int $i
     * @param TransactionOutputInterface $output
     * @return $this
     */
    public function set($i, TransactionOutputInterface $output)
    {
        $this->outputs[$i] = $output;
        return $this;
    }

    /**
     * @param int $i
     * @param TransactionOutputInterface $output
     * @return $this
     */
    public function update($i, TransactionOutputInterface $output)
    {
        $this->getOutput($i);
        $this->set($i, $output);
        return $this;
    }

    /**
     * @param int $i
     * @param \Closure $closure
     * @return $this
     */
    public function applyTo($i, \Closure $closure)
    {
        $mutator = $this->outputMutator($i);
        $closure($mutator);
        $this->update($i, $mutator->get());
        return $this;
    }
}