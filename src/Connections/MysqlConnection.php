<?php

namespace LDRCore\Modelling\Connections;

use LDRCore\Modelling\Connections\Traits\HandlesTransactionsHooks;

class MysqlConnection extends \Illuminate\Database\MySqlConnection
{
	use HandlesTransactionsHooks;
}