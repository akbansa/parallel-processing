# Laravel Parallel Processing

An easy way to run multiple commands in parallel in laravel.

## Install

Install via composer

    composer require akbansa/parallel-processor
## Usage

Import `Akbansa\LaravelParallelProcessor\ParallelProcessor` where you want to use this parallel processor.

    $processor = new ParallelProcessor($tasks, $options);
    $processor->start();

    while (!$processor->isFinished()) {
        sleep(1);
    }
    
`$tasks` can be an array or collection of tasks that needs to be run and each item of this `$tasks` should have `command` as it's one of the property that need to be run.

For example

    $tasks = [
        ["id" => 1, "command" => "php artisan send:email abc@example.com"],
        ["id" => 2, "command" => "php artisan send:email example@domain.com"]
    ]
    
`$options` is an optional parameter in which you can pass parameters in an array as follow:

    1. $parallelCount => This can be used to define the number parallel processes to be executed (default: 3)
    2. $estimateProcessTime => average estimate time for process in second (default 1)
    
## License

The MIT License. Please see the [License File](LICENSE.md)
