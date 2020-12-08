<?php

namespace LDRCore\Modelling\Eloquent;

use LDRCore\Modelling\Eloquent\Traits\Deletable;
use LDRCore\Modelling\Eloquent\Traits\Updatable;
use LDRCore\Modelling\Query\Traits\Joinable;

class Builder extends \Illuminate\Database\Eloquent\Builder
{
	use Joinable, Updatable, Deletable;
}
