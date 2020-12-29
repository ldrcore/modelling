<?php

namespace LDRCore\Modelling\Console\Command;

class ObserverMakeCommand extends \Illuminate\Console\ObserverMakeCommand
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Eloquent model observer with TriggableObserver base';
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->option('model')
                    ? __DIR__.'/stubs/observer.stub'
                    : __DIR__.'/stubs/observer.plain.stub';
    }
}
