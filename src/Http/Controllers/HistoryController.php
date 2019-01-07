<?php

namespace Guratr\CommandRunner\Http\Controllers;

class HistoryController
{
    public function index()
    {
        $history = \Cache::get('nova-command-runner-history', []);
        array_walk($history, function (&$val) {
            $val['time'] = \Carbon\Carbon::createFromTimestamp($val['time'])->diffForHumans();
        });

        return $history;
    }
}