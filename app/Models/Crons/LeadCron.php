<?php

namespace App\Models\Crons;

use App\Jobs\setDateInField;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

// use App\Services\amoAPI\Entities\Lead as AmoLead;

class LeadCron extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'last_modified',
        'data',
    ];
    protected $hidden = [
        'id',
    ];

    private const PARSE_COUNT = 20;

    public static function createLead(string $leadId, int $lastModified, array $data): void
    {
        self::create([
            'lead_id'       => $leadId,
            'last_modified' => (int) $lastModified,
            'data'          => json_encode($data),
        ]);
    }
    public static function getLeadByAmoId(string $leadId): ?LeadCron
    {
        return self::all()->where('lead_id', $leadId)->first();
    }
    public static function updateLead(string $leadId, int $lastModified, array $data): void
    {
        self::where('lead_id', $leadId)->update([
            'last_modified' => (int) $lastModified,
            'data'          => json_encode($data),
        ]);
    }
    public static function parseRecentWebhooks()
    {
        Log::info(__METHOD__, ['Scheduler::[LeadCron][parseRecentWebhooks]']); //DELETE

        $leads = self::orderBy('id', 'asc')
            ->take(self::PARSE_COUNT)
            ->get();

        foreach ($leads as $lead) {
            Log::info(__METHOD__, ['webhook parsen ' . $lead->lead_id]); //DELETE
            Log::info(__METHOD__, ['name von pipelines stufe bekommen ']); //DELETE
            Log::info(__METHOD__, ['id von field durch die name der stufe bekommen ']); //DELETE

            setDateInField::dispatch(12345, 67890);

            // $lead->delete();
        }
    }

    /* PROCEDURES */

    /* FUNCTIONS */
}
