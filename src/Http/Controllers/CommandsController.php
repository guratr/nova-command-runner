<?php

namespace Guratr\CommandRunner\Http\Controllers;

class CommandsController
{
    public function index()
    {
        return config('nova-command-runner.commands');
    }

    public function run($index)
    {
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
    }
}