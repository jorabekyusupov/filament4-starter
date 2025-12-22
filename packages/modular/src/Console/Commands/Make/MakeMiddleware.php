<?php

namespace Jora\Modular\Console\Commands\Make;

use Illuminate\Routing\Console\MiddlewareMakeCommand;

class MakeMiddleware extends MiddlewareMakeCommand
{
	use Modularize;
}
