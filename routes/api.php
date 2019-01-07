<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

Route::get('/commands', function (Request $request) {
    return config('nova-command-runner.commands');
});

Route::get('/history', function (Request $request) {
    $history = \Cache::get('nova-command-runner-history', []);
    array_walk($history, function (&$val) {
        $val['time'] = \Carbon\Carbon::createFromTimestamp($val['time'])->diffForHumans();
    });

    return $history;
});

Route::post('/commands/{index}/run', function ($index, Request $request) {
    $commands = config('nova-command-runner.commands');
    $command = $commands[$index] ?? null;
    if (!$command) {
        return ['status' => false, 'result' => 'Command not found!'];
    }

    $start = microtime(true);
    try {
        $buffer = new \Symfony\Component\Console\Output\BufferedOutput();
        \Artisan::call($command['run'], $command['options'] ?? [], $buffer);
        $result = $buffer->fetch();
        $status = true;
    } catch (\Exception $exception) {
        $result = $exception->getMessage();
        $status = false;
    }
    $duration = microtime(true) - $start;

    if ($historyLength = config('nova-command-runner.history')) {
        $history = \Cache::get('nova-command-runner-history', []);
        $history = array_slice($history, 0, $historyLength - 1);
        array_unshift($history, [
            'run'      => $command['run'],
            'options'  => $command['options'] ?? [],
            'status'   => $status ? 'success' : 'error',
            'result'   => $result,
            'time'     => now()->timestamp,
            'duration' => round($duration, 4),
        ]);
        \Cache::forever('nova-command-runner-history', $history);
    }

    return ['status' => $status, 'result' => $result];
});
