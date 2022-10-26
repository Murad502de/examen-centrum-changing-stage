<?php

namespace App\Models\Crons;

use App\Models\Services\amoCRM;
use App\Services\amoAPI\amoAPIHub;
use App\Traits\Middleware\Services\AmoCRM\AmoTokenExpirationControlTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

// use App\Services\amoAPI\Entities\Lead as AmoLead;

class LeadCron extends Model
{
    use HasFactory;
    use AmoTokenExpirationControlTrait;

    protected $fillable = [
        'lead_id',
        'last_modified',
        'data',
    ];
    protected $hidden = [
        'id',
    ];

    private const PARSE_COUNT = 20;
    private static $amoAPIHub;

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

        if (self::amoTokenExpirationControl()) {
            self::$amoAPIHub = new amoAPIHub(amoCRM::getAuthData());
            $leads           = self::getLeads();

            foreach ($leads as $lead) {
                Log::info(__METHOD__, ['webhook parsen ' . $lead->data]); //DELETE

                $fieldId = self::getFieldIdByName(
                    self::getStageNameById(
                        (int) json_decode($lead->data)->status_id
                    )
                );

                if ($fieldId) {
                    Log::info(__METHOD__, ['geben datum im feld-stufe ein']); //DELETE
                }

                // $lead->delete();
            }
        }
    }

    /* PROCEDURES */

    /* FUNCTIONS */
    public static function getLeads()
    {
        return self::orderBy('id', 'asc')
            ->take(self::PARSE_COUNT)
            ->get();
    }
    public static function getStageNameById(int $id): string
    {
        Log::info(__METHOD__, ['status_id: ' . $id]); //DELETE

        return '';
    }
    public static function getFieldIdByName(string $name): ?int
    {
        Log::info(__METHOD__); //DELETE

        return null;
    }
}
