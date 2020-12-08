<?php

namespace LDRCore\Modelling\Connections\Traits;

trait HandlesTransactionsHooks
{
	private $beforeCommits = [];
	
	private $afterCommits = [];
	
	private $beforeRollbacks = [];
	
	private $afterRollbacks = [];
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
			$this->processHandlers($this->beforeCommits);
			parent::commit();
			$this->processHandlers($this->afterCommits);
		} else {
			parent::commit();
		}
	}
	
	public function rollBack($toLevel = null)
	{
		if ($this->transactionLevel() == 1) {
			$this->processHandlers($this->beforeRollbacks);
			parent::rollBack($toLevel);
			$this->processHandlers($this->afterRollbacks);
		} else {
			parent::rollBack($toLevel);
		}
	}
	
	private function processHandlers(array &$list)
	{
		foreach ($list as $k => $closure) {
			\call_user_func_array($closure, []);
			unset($k);
		}
	}
	
	public final function beforeCommit(\Closure $c)
	{
		$this->beforeCommits[] = $c;
	}
	
	public final function afterCommit(\Closure $c)
	{
		$this->afterCommits[] = $c;
	}
	
	public final function beforeRollback(\Closure $c)
	{
		$this->beforeRollbacks[] = $c;
	}
	
	public final function afterRollback(\Closure $c)
	{
		$this->afterRollbacks[] = $c;
	}
}
