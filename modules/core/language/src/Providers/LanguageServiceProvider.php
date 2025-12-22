<?php

namespace Modules\Language\Providers;

use App\Providers\BaseServiceProvider;


class LanguageServiceProvider extends BaseServiceProvider
{
    public array $bindings = [
        \Modules\Language\Repositories\LanguageReadRepositoryInterface::class => \Modules\Language\Repositories\LanguageReadRepository::class,
        \Modules\Language\Repositories\LanguageWriteRepositoryInterface::class => \Modules\Language\Repositories\LanguageWriteRepository::class,
    ];
	public function register(): void
	{
        parent::register();
	}
	
	public function boot(): void
	{
        $this->name = 'language';
        parent::boot();
        $this->commands([
            \Modules\Language\Commands\InsertDefaultLangCommand::class,
        ]);



	}
}
