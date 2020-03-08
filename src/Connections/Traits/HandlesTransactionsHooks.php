<?php

namespace LDRCore\Modelling\Connections\Traits;

trait HandlesTransactionsHooks
{
	private $beforeCommits = [];
	
	private $afterCommits = [];
	
	private $beforeRollbacks = [];
	
	private $afterRollbacks = [];
	
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
	
	private function processHandlers(array $list)
	{
		foreach ($list as $closure) {
			\call_user_func_array($closure, []);
		}
	}
	
	public function beforeCommit(\Closure $c)
	{
		$this->beforeCommits[] = $c;
	}
	
	public function afterCommit(\Closure $c)
	{
		$this->afterCommits[] = $c;
	}
	
	public function beforeRollback(\Closure $c)
	{
		$this->beforeRollbacks[] = $c;
	}
	
	public function afterRollback(\Closure $c)
	{
		$this->afterRollbacks[] = $c;
	}
}