<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ActionLog
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $date
 * @property string $action
 * @property string $param
 * @property integer $type
 * @property integer $status
 * @property string $comment
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class ActionLog extends Model {

    protected $table = 'action_log';

}
