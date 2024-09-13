<?php

namespace App\Actions\Oh2BeesTask;

use App\Data\Oh2BeesTaskArgs;
use App\Enums\ActivityTypes;
use App\Jobs\Oh2BeesTask;
use Spatie\Activitylog\Models\Activity;

/**
 * The initial step to run a `Oh2BeesTask`: a remote SSH process
 * with monitoring/tracking/trace feature. Such thing is made
 * possible using an Activity model and some attributes.
 */
class PrepareOh2BeesTask
{
    protected Activity $activity;

    protected Oh2BeesTaskArgs $remoteProcessArgs;

    public function __construct(Oh2BeesTaskArgs $remoteProcessArgs)
    {
        $this->remoteProcessArgs = $remoteProcessArgs;

        if ($remoteProcessArgs->model) {
            $properties = $remoteProcessArgs->toArray();
            unset($properties['model']);

            $this->activity = activity()
                ->withProperties($properties)
                ->performedOn($remoteProcessArgs->model)
                ->event($remoteProcessArgs->type)
                ->log('[]');
        } else {
            $this->activity = activity()
                ->withProperties($remoteProcessArgs->toArray())
                ->event($remoteProcessArgs->type)
                ->log('[]');
        }
    }

    public function __invoke(): Activity
    {
        $job = new Oh2BeesTask(
            activity: $this->activity,
            ignore_errors: $this->remoteProcessArgs->ignore_errors,
            call_event_on_finish: $this->remoteProcessArgs->call_event_on_finish,
            call_event_data: $this->remoteProcessArgs->call_event_data,
        );
        if ($this->remoteProcessArgs->type === ActivityTypes::COMMAND->value) {
            ray('Dispatching a high priority job');
            dispatch($job)->onQueue('high');
        } else {
            dispatch($job);
        }
        $this->activity->refresh();

        return $this->activity;
    }
}
