<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\JobLog
 *
 * @property integer $id
 * @property string $date
 * @property string $action
 * @property string $param
 * @property integer $status
 * @property string $comment
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class JobLog extends Model {

    protected $table = 'job_log';

}
