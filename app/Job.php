<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Job
 *
 * @property integer $id
 * @property string $queue
 * @property string $payload
 * @property boolean $attempts
 * @property boolean $reserved
 * @property integer $reserved_at
 * @property integer $available_at
 * @property \Carbon\Carbon $created_at
 */
class Job extends Model
{
    //
}
