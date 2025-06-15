<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Eloquent model for the application database table entry.
 *
 * Starting to add at least some typehints:
 * @property int $physical_chairs The number of assigned physical chairs.
 */
class Application extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $appends = [
        "status"
    ];

    protected $with = [
        'requestedTable',
        'assignedTable',
    ];

    protected $casts = [
        "type" => ApplicationType::class,
        "is_power" => "boolean",
        "is_afterdark" => "boolean",
        "is_wallseat" => "boolean",
        "canceled_at" => "datetime",
        "waiting_at" => "datetime",
        "checked_in_at" => "datetime",
        "checked_out_at" => "datetime",
        "offer_sent_at" => "datetime",
        "offer_accepted_at" => "datetime",
    ];

    protected $attributes = [
        "table_type_requested" => 2,
        // By default, set chairs to a negative value, indicating "not set yet".
        "physical_chairs" => -1,
    ];

    protected static function booted()
    {
        static::saving(function (Application $model) {
            $model->enforcePhysicalChairsMaximum();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requestedTable()
    {
        return $this->belongsTo(TableType::class, 'table_type_requested');
    }

    public function assignedTable()
    {
        return $this->belongsTo(TableType::class, 'table_type_assigned');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'application_id');
    }

    public function type(): Attribute
    {
        return Attribute::make(
            set: function (ApplicationType|string $value) {
                $type = ($value instanceof ApplicationType) ? $value : ApplicationType::from($value);
                switch ($type) {
                    case ApplicationType::Dealer:
                        // Dealer has no parent
                        return [
                            'parent_id' => null,
                            'type' => $type,
                        ];
                    case ApplicationType::Share:
                    case ApplicationType::Assistant:
                        // Assistant and share has no table
                        return [
                            'type' => $type,
                            'table_type_requested' => null,
                            'table_type_assigned' => null,
                        ];
                    default:
                        return;
                }
            }
        );
    }

    public function tableNumber(): Attribute
    {
        $application = $this;
        return Attribute::make(
            set: function (string|null $value) use ($application): string|null {
                if ($application->type === ApplicationType::Assistant) {
                    return null;
                }
                return $value;
            },
            get: function (string|null $value) use ($application): string|null {
                if ($application->type === ApplicationType::Assistant) {
                    return $application->parent()->first()->table_number;
                }
                return $value;
            }
        );
    }

    /**
     * Get full dealership name of either "display_name (userName)" or just "userName" if no display_name has been set.
     */
    public function getFullName(): string
    {
        if (empty($this->display_name)) {
            return $this->user->name;
        } else {
            return "{$this->display_name} ({$this->user->name})";
        }
    }

    public function getStatus()
    {
        return ApplicationStatus::for($this);
    }

    public function isActive()
    {
        return $this->getStatus() !== ApplicationStatus::Canceled;
    }

    public function checkIn()
    {
        if ($this->status === ApplicationStatus::TableAccepted) {
            $this->status = ApplicationStatus::CheckedIn;
            $this->save();
            return true;
        }
        return false;
    }

    public function checkOut()
    {
        if ($this->status === ApplicationStatus::CheckedIn) {
            $this->status = ApplicationStatus::CheckedOut;
            $this->save();
            return true;
        }
        return false;
    }

    public function cancel()
    {
        $this->canceled_at = now();
        $this->save();
    }

    public function shares()
    {
        return $this->children()->whereNull('canceled_at')->where('type', ApplicationType::Share);
    }

    public function assistants()
    {
        return $this->children()->whereNull('canceled_at')->where('type', ApplicationType::Assistant);
    }

    /**
     * Get an associative array of the seat assignment, counted per type.
     *
     * If set, the assigned table type is used as base for the calculation, else the requested table type is used.
     * New: You can provide another table type to calculate based of that one instead of the assigned/requested one.
     *
     * @param TableType|null $checkAlternativeTableType If not null, check for this table type instead.
     * @return array
     */
    public function getSeats(TableType|null $checkAlternativeTableType = null): array
    {
        /** @var TableType */
        $tableType = $checkAlternativeTableType ?? $this->assignedTable ?? $this->requestedTable;
        if (is_null($tableType) || !$this->isActive()) {
            return [
                'table' => 0,
                'dealers' => 0,
                'assistants' => 0,
                'free' => 0,
                'additional' => null,
                'physical_chairs' => 0,
            ];
        }

        $totalSeats = $tableType->seats;

        $dealers = $this->shares()->count() + 1;
        $assistants = $this->assistants()->count();
        $additional = null;

        if ($totalSeats - $dealers <= 0 && $assistants === 0) {
            // Single assistant is available even if table is filled with dealers.
            $additional = 'assistant';
        }

        if ($totalSeats - $dealers === 1 && $assistants === 1) {
            // Single assistant doesn't consume dealer seat and results in free dealer-only seat.
            $additional = 'dealer';
        }

        $free = $totalSeats - $dealers - $assistants;
        if ($free < 0 && $assistants === 1) {
            // Free seat count should not be negative because of minimum assistant.
            $free += 1;
        }

        return [
            'table' => $totalSeats,
            'dealers' => $dealers,
            'assistants' => $assistants,
            'free' => $free,
            'additional' => $additional,
            'physical_chairs' => $this->physical_chairs,
        ];
    }

    /**
     * Test if the application in its current state will have a valid seat count if the given table type were assigned.
     *
     * This check internally uses @see Application::getSeats() to apply the same counting rules.
     *
     * @param TableType $newTableType The table type to check for.
     * @return bool True if changing to this table type does not violate seats assignment.
     */
    public function canChangeTableTypeTo(TableType $newTableType): bool
    {
        return $this->getSeats($newTableType)['free'] >= 0;
    }

    /**
     * Adjust the number of physical chairs and apply hard rules.
     * To just enforce the limits without changing otherwise, call without argument.
     *
     * @param int $delta Number of chairs to add (positive) or remove (negative).
     * @return array Contains old and new count as well as an optional message.
     */
    public function changePhysicalChairsBy(int $delta = 0): array
    {
        /** @var int $oldChairCount */
        $oldChairCount = $this->physical_chairs > 0 ? $this->physical_chairs : 0;
        $newChairCount = $oldChairCount + $delta;

        /** @var TableType $tableType */
        $tableType = $this->assignedTable ?? $this->requestedTable;

        if (!$tableType) {
            return [
                'old' => $oldChairCount,
                'new' => $oldChairCount,
                'success' => false,
                'message' => 'Cannot assign chairs without a table!',
            ];
        }

        /** @var int $maximumChairs */
        $maximumChairs = $tableType->seats;

        // Default message: Indicate chair change
        switch ($delta) {
            case 1:
                $message = 'Added one chair to your table.';
                break;
            case -1:
                $message = 'Removed one chair to your table.';
                break;
            default:
                $message = sprintf('%s %d chairs to your table.',
                        $delta < 0 ? 'Removed' : 'Added', abs($delta));
                break;
        }

        // Allow at maximum as many chairs as fit for this table
        if ($newChairCount > $maximumChairs) {
            $newChairCount = $maximumChairs;
            $message = sprintf('Maximum number of chairs reached for table size!');
        }

        // Allow at minimum no chairs.
        if ($newChairCount < 0) {
            $newChairCount = 0;
            $message = sprintf('Cannot have less than zero chairs!');
        }

        // Note: Notification Emails are sent by \App\Observers\ApplicationObserver
        $this->update([
            'physical_chairs' => $newChairCount,
        ]);
        return [
            'old' => $oldChairCount,
            'new' => $newChairCount,
            'success' => ($newChairCount == ($oldChairCount + $delta)),
            'message' => $message,
        ];
    }

    /**
     * Local event handler to limit the chair maximum according to the table type.
     * Called as assigned in static::booted above.
     */
    private function enforcePhysicalChairsMaximum(): void
    {
        // Ignore if not set yet
        if ($this->physical_chairs < 0) return;

        /** @var TableType $tableType */
        // Since this is called inside a lifecycle hook, the pseudo properties are NOT updated!
        // This won't work in that case: $tableType = $this->assignedTable ?? $this->requestedTable;
        // Instead we MUST re-fetch the fresh objects, but we limit that based on the dirty flag to reduce overhead:
        if ($this->isDirty(['table_type_assigned','table_type_requested'])) {
            $tableType = TableType::find($this->table_type_assigned) ?? TableType::find($this->table_type_requested);
        } else {
            // If the above are not dirty, we first see if physical chairs are dirty, else we skip for optimization:
            if (!$this->isDirty('physical_chairs')) return;
            // Else we can use the already fetched variable values
            $tableType = $this->assignedTable ?? $this->requestedTable;
        }

        if ($tableType) {
            /** @var int $maximumChairs */
            $maximumChairs = $tableType->seats;

            if ($maximumChairs > 0 && $this->physical_chairs > $maximumChairs) {
                /* Only enforce if:
                 * - a table type is set and has a valid seat count
                 * - the physical chairs are set to a valid value and
                 * - the chair count exceeds the seat count
                 * This should in turn trigger the notification mail from the observer
                 */
                $this->physical_chairs = $maximumChairs;
            }
        }
    }

    /**
     * Adjust the number of physical chairs and apply hard rules.
     *
     * @param int $newCount Number of chairs that shall result from this action.
     * @return array Contains old and new count as well as an optional message.
     */
    public function setPhysicalChairsTo(int $newCount): array
    {
        /** @var int $oldChairCount */
        $oldChairCount = ($this->physical_chairs > 0) ? $this->physical_chairs : 0;
        return $this->changePhysicalChairsBy($newCount - $oldChairCount);
    }

    /**
     * This should be called whenever a share joins or leaves the dealership.
     * It adjusts the physical chair count by up to one chair as long as
     * the default assignment of one chair per dealer/share is not reached.
     *
     * Normally, upon one share joining, call with $adjustBy = 1.
     * Upon one share leaving, call with $adjustBy = -1.
     * To execute the default assignment in case no chairs are yet configured, can call without argument.
     * In the latter case, if a valid chair count is already set up, nothing will change.
     *
     * @param int $adjustBy Number of chairs to add (remove if negative).
     */
    public function applyPhysicalChairsDefaultAdjustment(int $adjustBy = 0)
    {
        $defaultChairCount = $this->shares()->count() + 1;
        $chairCount = $this->physical_chairs;

        if ($chairCount < 0) {
            // If not yet set to a valid value, use default.
            $chairCount = $defaultChairCount;
        } elseif ($chairCount != $defaultChairCount) {
            // If we have not reached the default, apply adjustment
            // RULE 1: When adding shares, always try to add as many chairs on top, up to maximum
            // (maximum will be handled by setPhysicalChairsTo).
            $chairCount += $adjustBy;
            // RULE 2: When removing shares, remove as many shares but keep the default as minimum
            // (but if there were less than default to begin with, do not re-add them either!)
            if ($adjustBy < 0 && $chairCount < $defaultChairCount) {
                $chairCount = $defaultChairCount;
            }
        }
        $this->setPhysicalChairsTo($chairCount);
    }

    public function getStatusAttribute()
    {
        return $this->getStatus();
    }

    public function setStatusAttribute(ApplicationStatus|string $status)
    {
        if (is_string($status)) {
            $status = ApplicationStatus::tryFrom($status);
        }

        // Saving with ApplicationStatus::Canceled should always lead to a reset regardless if the status was already Canceled.
        if ($status === ApplicationStatus::Canceled) {
            $this->update([
                'offer_accepted_at' => null,
                'offer_sent_at' => null,
                'table_number' => null,
                'parent_id' => null,
                'waiting_at' => null,
                'type' => ApplicationType::Dealer,
                'canceled_at' => now(),
            ]);
            return;
        }

        // Don't reset timestamps when application is saved without status change!
        if ($this->getStatus() === $status) {
            return;
        }

        // TableAssigned is technically identical to open just with a table number assigned.
        if ($status === ApplicationStatus::TableAssigned) {
            $this->update([
                'offer_accepted_at' => null,
                'offer_sent_at' => null,
                'waiting_at' => null,
                'canceled_at' => null,
            ]);
        }

        if ($status === ApplicationStatus::Open) {
            $this->update([
                'offer_accepted_at' => null,
                'offer_sent_at' => null,
                'table_number' => null,
                'waiting_at' => null,
                'canceled_at' => null,
            ]);
        }

        if ($status === ApplicationStatus::Waiting) {
            $this->update([
                'offer_accepted_at' => null,
                'offer_sent_at' => null,
                'table_number' => null,
                'waiting_at' => now(),
                'canceled_at' => null,
            ]);
        }

        if ($status === ApplicationStatus::TableOffered) {
            $this->update([
                'offer_accepted_at' => null,
                'offer_sent_at' => now(),
                'waiting_at' => null,
                'canceled_at' => null,
            ]);
        }

        if ($status === ApplicationStatus::TableAccepted) {
            $this->update([
                'offer_accepted_at' => now(),
                'waiting_at' => null,
                'canceled_at' => null,
                'checked_in_at' => null,
            ]);
        }

        if ($status === ApplicationStatus::CheckedIn) {
            $this->update([
                'checked_in_at' => now(),
                'checked_out_at' => null,
                'canceled_at' => null,
            ]);
        }

        if ($status === ApplicationStatus::CheckedOut) {
            $this->update([
                'checked_out_at' => now(),
                'canceled_at' => null,
            ]);
        }
    }

    public static function findByUserId(string|null $user_id): Application|null
    {
        return self::where('user_id', $user_id)->first();
    }

    public static function getEligibleParents()
    {
        return self::leftJoin('users', 'user_id', '=', 'users.id')
            ->where('type', '=', 'dealer')
            ->whereNull('canceled_at')
            ->select(
                'applications.id AS id',
                'users.name AS name'
            )->get();
    }

    public static function getAllApplicationsForExport(): \Illuminate\Support\Collection
    {
        $keywords = Profile::query()->toBase()
            ->leftJoin('keyword_profile', 'keyword_profile.profile_id', '=', 'profiles.id')
            ->leftJoin('keywords', 'keywords.id', '=', 'keyword_profile.keyword_id')
            ->leftJoin('categories', 'keywords.category_id', '=', 'categories.id')
            ->groupBy('profiles.id')
            ->select(DB::raw('GROUP_CONCAT(`keywords`.`name` SEPARATOR \',\') AS `keywords`, GROUP_CONCAT(`categories`.`name` SEPARATOR \',\') AS `categories`, `profiles`.`id` AS `profile_id`'));

        $applications = self::query()->toBase()
            ->leftJoin('profiles', 'applications.id', '=', 'profiles.application_id')
            ->leftJoin('users', 'user_id', '=', 'users.id')
            ->leftJoin('table_types AS t1', 'table_type_requested', '=', 't1.id')
            ->leftJoin('table_types AS t2', 'table_type_assigned', '=', 't2.id')
            ->leftJoinSub($keywords, 'profile_keywords', function (JoinClause $join) {
                $join->on('profiles.id', '=', 'profile_keywords.profile_id');
            })
            ->select(
                'applications.id AS app_id',
                'users.name AS user_name',
                'users.email AS email',
                'users.reg_id AS reg_id',
                'type AS app_type',
                'parent_id',
                'display_name',
                'applications.website AS app_website',
                'table_number',
                'merchandise',
                'invite_code_shares',
                'invite_code_assistants',
                'additional_space_request',
                'wanted_neighbors',
                'comment',
                'is_afterdark',
                'is_power',
                'is_wallseat',
                't1.name AS table_type_requested',
                't2.name AS table_type_assigned',
                'applications.created_at AS app_created_at',
                'applications.updated_at AS app_updated_at',
                'waiting_at',
                'offer_sent_at',
                'offer_accepted_at',
                'checked_in_at',
                'checked_out_at',
                'canceled_at',
                'profiles.*',
                'profile_keywords.keywords',
                'profile_keywords.categories'
            )
            ->get();

        return $applications;
    }

    public static function getAllApplicationsForAppExport(): \Illuminate\Support\Collection
    {
        $keywords = Profile::query()->toBase()
            ->leftJoin('keyword_profile', 'keyword_profile.profile_id', '=', 'profiles.id')
            ->leftJoin('keywords', 'keywords.id', '=', 'keyword_profile.keyword_id')
            ->leftJoin('categories', 'keywords.category_id', '=', 'categories.id')
            ->groupBy('profiles.id')
            ->select(DB::raw('GROUP_CONCAT(CONCAT_WS(\'::\', `categories`.`name`, `keywords`.`name`) SEPARATOR \'$$\') AS `categorized_keywords`, `profiles`.`id` AS `profile_id`'));

        $applications = self::query()->toBase()
            ->leftJoin('profiles', 'applications.id', '=', 'profiles.application_id')
            ->leftJoin('users', 'applications.user_id', '=', 'users.id')
            ->leftJoin('applications as parents', 'applications.parent_id', '=', 'parents.id')
            ->leftJoinSub($keywords, 'profile_keywords', function (JoinClause $join) {
                $join->on('profiles.id', '=', 'profile_keywords.profile_id');
            })
            ->select(
                'applications.id AS Reg No.',
                'users.name AS Nick',
                'applications.display_name AS Display Name',
                DB::raw("'' as 'Merchandise'"),
                DB::raw("CASE WHEN attends_thu = 1 THEN 'X' ELSE '' END AS 'Attends Thu'"),
                DB::raw("CASE WHEN attends_fri = 1 THEN 'X' ELSE '' END AS 'Attends Fri'"),
                DB::raw("CASE WHEN attends_sat = 1 THEN 'X' ELSE '' END AS 'Attends Sat'"),
                DB::raw("'X' as 'Allows Use of Data'"),
                DB::raw("CASE WHEN applications.is_afterdark = 1 THEN 'X' ELSE '' END AS 'After Dark'"),
                // TODO: Temporary fix for EF27 since "Table Number" is not supported by the app backend & apps
                DB::raw("TRIM('\n' FROM CONCAT(IFNULL(CONCAT('Table ', IFNULL(parents.table_number, applications.table_number)), ''), '\\n\\n', IFNULL(short_desc, ''))) AS 'Short Description'"),
                'artist_desc AS About the Artist',
                'art_desc AS About the Art',
                'profiles.website as Website',
                'twitter as Twitter',
                'discord as Discord',
                'mastodon as Mastodon',
                'bluesky as Bluesky',
                'telegram as Telegram',
                'art_preview_caption as Art Preview Caption',
                DB::raw("CASE WHEN image_thumbnail IS NOT NULL THEN 'X' ELSE '' END AS 'ThumbailImg'"),
                DB::raw("CASE WHEN image_artist IS NOT NULL THEN 'X' ELSE '' END AS 'ArtistImg'"),
                DB::raw("CASE WHEN image_art IS NOT NULL THEN 'X' ELSE '' END AS 'ArtImg'"),
                'profile_keywords.categorized_keywords as Keywords',
                'tweet as Tweet',
                DB::raw("IFNULL(parents.table_number, applications.table_number) AS 'Table Number'"),
                'applications.type as Type',
            )
            ->where(function (Builder $query) {
                $query->whereNotNull('applications.offer_accepted_at')
                    ->orWhereNotNull('parents.offer_accepted_at');
            })
            ->where(function (Builder $query) {
                $query->where('applications.type', ApplicationType::Dealer)
                    ->orWhere('applications.type', ApplicationType::Share);
            })
            ->where(function (Builder $query) {
                $query->where('profiles.is_hidden', '=', '0')
                    ->orWhere('applications.type', ApplicationType::Dealer);
            })
            ->get();
        return $applications;
    }

    /**
     * Sets table_type_assigned to `null` if input value is empty, converts to integer otherwise.
     *
     * Hacky fix for issues with selecting the placeholder in Filament\Tables\Columns\SelectColumn,
     * which will attempt to set the int field 'table_type_assigned' to '' instead of null and
     * triggers an exception in the process instead of storing the value.
     */
    public function tableTypeAssignedAutoNull(): Attribute
    {
        return Attribute::make(
            get: fn(int|null $value, array $attributes) => $attributes['table_type_assigned'],
            set: fn(mixed $value) => [
                'table_type_assigned' => empty($value) ? null : intval($value),
            ]
        );
    }

    /**
     * An application is considered ready if all related applications (parent/children) share the
     * same status (canceled child applications are ignored for this) and table number and the
     * application itself is not canceled.
     */
    public function isReady(): bool
    {
        if ($this->status === ApplicationStatus::Canceled) {
            return false;
        }

        if ($this->type === ApplicationType::Dealer) {
            $dealership = $this;
        } else {
            $dealership = $this->parent()->get()->first();
        }

        if (empty($dealership->table_number) xor $dealership->table_type_assigned === null) {
            return false;
        }

        if (
            $this->status === ApplicationStatus::Waiting
            && (empty($dealership->table_number) || $dealership->table_type_assigned === null)
        ) {
            return false;
        }

        foreach ($dealership->children()->get() as $child) {
            if ($child->status === ApplicationStatus::Canceled) {
                continue;
            }
            if ($child->status !== $dealership->status) {
                return false;
            }
            // table numbers must be identical
            if (
                $child->table_number !== $dealership->table_number
                // table numbers '' and null should be considered identical
                && !(empty($dealership->table_number) && empty($child->table_number))
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $type 'assistant' or 'shares'
     * @param bool $clear should code be cleared instead of regenerated?
     * @return bool success of the requested operation
     */
    public function updateCode(string $type, bool $clear = false): bool
    {
        if ($type !== 'assistants' && $type !== 'shares') {
            return false;
        }

        $code = '';
        if (!$clear) {
            $code = $type . '-' . Str::random();
        }

        Auth::user()->application->update([
            "invite_code_$type" => $code
        ]);
        return true;
    }

    public static function findByCode(string|null $code): Application|null
    {
        if (empty($code)) {
            return null;
        }

        return self::where('type', ApplicationType::Dealer)
            ->where(function ($q) use ($code) {
                return $q->where('invite_code_assistants', $code)
                    ->orWhere('invite_code_shares', $code);
            })->first();
    }

    public static function determineApplicationTypeByCode(string|null $code): ApplicationType|null
    {
        if (str_starts_with($code, 'shares-')) {
            return ApplicationType::Share;
        }
        if (str_starts_with($code, 'assistants-')) {
            return ApplicationType::Assistant;
        }
        return null;
    }
}
