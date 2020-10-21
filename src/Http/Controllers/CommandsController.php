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
            $result = $this->execute($command);
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

    protected function execute($command)
    {
        $buffer = new \Symfony\Component\Console\Output\BufferedOutput();

        if(!($command['queue'] ?? false)) {
            \Artisan::call($command['run'], $command['options'] ?? [], $buffer);
            return $buffer->fetch();
        }

        ['connection' => $connection, 'queue' => $queue] = array_merge(
            ['connection' => config('queue.default'), 'queue' => 'default'],
            is_array($command['queue']) ? $command['queue'] : []
        );

        \Artisan::queue($command['run'], $command['options'] ?? [], $buffer)
            ->onConnection($connection)
            ->onQueue($queue);

        return $buffer->fetch();
    }
}