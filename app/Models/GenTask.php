<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class GenTask extends Model
{
    use HasFactory;

    protected $table = 'gen_tasks';

    public const STATUS_PENDING    = 'Đang chờ';
    public const STATUS_DONE       = 'Hoàn thành';
    public const STATUS_DONE_LATE  = 'Hoàn thành trễ';

    protected $fillable = [
        'code',
        'workName',
        'workerID',
        'workID',
        'workDate',
        'dateEnd',
        'completed_at',
        'TaskpropID',
        'plotID',
        'active',
        'type',
        'productID',
        'workStatus',
        'description',
        'priority',
        'status',
    ];
    protected $casts = [
        'dateEnd'      => 'date',
        'completed_at' => 'date',
    ];

    public function setDateEndAttribute($value): void
    {
        $this->attributes['dateEnd'] = $value ? Carbon::parse($value)->endOfDay() : null;
    }

    public static function completedStatuses(): array
    {
        return [self::STATUS_DONE, self::STATUS_DONE_LATE];
    }

    // ===== Scopes =====
    public function scopeCompleted(Builder $q): Builder
    {
        return $q->whereIn('workStatus', self::completedStatuses());
    }

    public function scopePending(Builder $q): Builder
    {
        return $q->where('workStatus', self::STATUS_PENDING);
    }

    public function scopeCompletedOnTime(Builder $q): Builder
    {
        return $q->where(function (Builder $w) {
            $w->where('workStatus', self::STATUS_DONE)
                ->orWhere(function (Builder $x) {
                    $x->whereIn('workStatus', self::completedStatuses())
                        ->whereNotNull('completed_at')
                        ->whereColumn('completed_at', '<=', 'dateEnd');
                });
        });
    }

    public function scopeCompletedLate(Builder $q): Builder
    {
        return $q->where(function (Builder $w) {
            $w->where('workStatus', self::STATUS_DONE_LATE)
                ->orWhere(function (Builder $x) {
                    $x->whereIn('workStatus', self::completedStatuses())
                        ->whereNotNull('completed_at')
                        ->whereNotNull('dateEnd')
                        ->whereColumn('completed_at', '>', 'dateEnd');
                });
        });
    }

    public function scopePendingOnTrack(Builder $q): Builder
    {
        return $q->pending()->where(function (Builder $w) {
            $w->whereNull('dateEnd')
                ->orWhere('dateEnd', '>=', now());
        });
    }

    public function scopePendingOverdue(Builder $q): Builder
    {
        // Quá hạn
        return $q->pending()->whereNotNull('dateEnd')->where('dateEnd', '<', now());
    }

    public function work()
    {
        return $this->belongsTo(Work::class, 'workID', 'id');
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'workerID', 'id');
    }

    public function plot()
    {
        return $this->belongsTo(Plot::class, 'plotID', 'id');
    }

    public function pickings()
    {
        return $this->hasMany(Picking::class, 'taskID', 'id');
    }
    public function taskProductProposals()
    {
        return $this->hasMany(TaskProductProposal::class, 'taskID', 'id');
    }
    public function plants()
    {
        return $this->belongsToMany(Plant::class, 'gen_task_plant', 'taskID', 'plantID');
    }
}
