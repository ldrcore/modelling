<?php

namespace LDRCore\Modelling\Query;

use LDRCore\Modelling\Query\Traits\Joinable;

class Builder extends \Illuminate\Database\Query\Builder
{
	use Joinable;
}
