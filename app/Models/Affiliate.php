<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Affiliate
 *
 * @property int $affiliate_id
 * @property string $name
 * @property float $latitude
 * @property float $longitude
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Database\Factories\AffiliateFactory factory(...$parameters)
 * @method static Builder|Affiliate newModelQuery()
 * @method static Builder|Affiliate newQuery()
 * @method static Builder|Affiliate query()
 * @method static Builder|Affiliate whereAffiliateId($value)
 * @method static Builder|Affiliate whereCreatedAt($value)
 * @method static Builder|Affiliate whereLatitude($value)
 * @method static Builder|Affiliate whereLongitude($value)
 * @method static Builder|Affiliate whereName($value)
 * @method static Builder|Affiliate whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Affiliate extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'affiliates';

    protected $primaryKey = 'affiliate_id';

    public float $distance;

    /**
     * @var string[]
     */
    protected $casts = [
        'longitude' => 'float',
        'latitude' => 'float',
    ];

    /**
     * @param array<float> $location
     */
    public function calculateDistance(array $location = [53.3340285, -6.2535495]): float
    {
        $earth_radius = 6371.009;

        $latDelta = deg2rad($this->latitude) - deg2rad($location[0]);
        $lonDelta = deg2rad($this->longitude) - deg2rad($location[1]);

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos(deg2rad($this->longitude)) * cos(deg2rad($location[1])) * pow(sin($lonDelta / 2), 2)));
        $distance = $earth_radius * $angle;

        return round($distance, 4);
    }
}
