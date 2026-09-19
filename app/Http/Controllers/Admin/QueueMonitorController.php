<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\QueueWorkerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QueueMonitorController extends Controller
{
    public function index(QueueWorkerService $queue): View
    {
        return view('Admin.Queue.A_queue_monitor', [
            'connection' => $queue->connection(),
            'isDatabase' => $queue->isDatabaseQueue(),
            'workerRunning' => $queue->isWorkerRunning(),
            'pendingCount' => $queue->pendingCount(),
            'failedCount' => $queue->failedCount(),
            'pendingJobs' => $queue->pendingJobs(),
            'failedJobs' => $queue->failedJobs(),
        ]);
    }

    public function start(QueueWorkerService $queue): RedirectResponse
    {
        $result = $queue->startWorker();

        return redirect()
            ->route('A_queue_monitor')
            ->with($result['ok'] ? 'success' : 'error', $result['message']);
    }

    public function stop(QueueWorkerService $queue): RedirectResponse
    {
        $result = $queue->stopWorker();

        return redirect()
            ->route('A_queue_monitor')
            ->with($result['ok'] ? 'success' : 'error', $result['message']);
    }

    public function retry(Request $request, QueueWorkerService $queue): RedirectResponse
    {
        $validated = $request->validate([
            'uuid' => ['required', 'uuid'],
        ]);

        $result = $queue->retryFailed((string) $validated['uuid']);

        return redirect()
            ->route('A_queue_monitor')
            ->with($result['ok'] ? 'success' : 'error', $result['message']);
    }

    public function retryAll(QueueWorkerService $queue): RedirectResponse
    {
        $result = $queue->retryAllFailed();

        return redirect()
            ->route('A_queue_monitor')
            ->with($result['ok'] ? 'success' : 'error', $result['message']);
    }
}
