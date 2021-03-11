<?php

namespace LDRCore\Modelling\Connections\Traits;

trait HandlesTransactionsHooks
{
	/**
	 * Bag of current handlers
	 * @var array
	 */
	private $handlers = [
		'bc' => [],
		'ac' => [],
		'br' => [],
		'ar' => [],
	];
    /**
     * Commit the active database transaction.
     *
     * @return void
     *
     * @throws \Throwable
     */
	public function commit()
	{
		if ($this->transactionLevel() == 1) {
			$this->processHandlers($this->handlers['bc']);
			parent::commit();
			$this->processHandlers($this->handlers['ac']);
		} else {
			parent::commit();
		}
	}
    /**
     * Rollback the active database transaction.
     *
     * @param  int|null  $toLevel
     * @return void
     *
     * @throws \Throwable
     */
	public function rollBack($toLevel = null)
	{
		if ($this->transactionLevel() == 1 || $toLevel <= 1) {
			$this->processHandlers($this->handlers['br']);
			parent::rollBack($toLevel);
			$this->processHandlers($this->handlers['ar']);
		} else {
			parent::rollBack($toLevel);
		}
	}
	/**
	 * @param array $list
	 */
	private function processHandlers(array &$list)
	{
		foreach ($list as $k => $closure) {
			\call_user_func_array($closure, []);
			unset($list[$k]);
		}
	}
	/**
	 * Add a trigger to be executed before the master commit.
	 * @param \Closure $callback
	 */
	public final function beforeCommit($callback)
	{
		$this->handlers['bc'][] = $callback;
	}
	/**
	 * Add a trigger to be executed after the master commit.
	 * @param \Closure $callback
	 */
	public final function afterCommit($callback)
	{
		$this->handlers['ac'][] = $callback;
	}
	/**
	 * Add a trigger to be executed before the master rollback.
	 * @param \Closure $callback
	 */
	public final function beforeRollback($callback)
	{
		$this->handlers['br'][] = $callback;
	}
	/**
	 * Add a trigger to be executed after the master rollback.
	 * @param \Closure $callback
	 */
	public final function afterRollback($callback)
	{
		$this->handlers['ar'][] = $callback;
	}
}
