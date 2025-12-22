<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Room extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * 複合主キーのため自動増分を無効化
     */
    public $incrementing = false;

    /**
     * テーブル名
     */
    protected $table = 'rooms';

    /**
     * 複合主キー（参照用、Eloquent標準では配列非対応）
     *
     * @var string[]
     */
    protected array $compositeKeys = ['region', 'facility_code', 'room_number'];

    /**
     * 一括代入可能な属性
     *
     * @var list<string>
     */
    protected $fillable = [
        'region',
        'facility_code',
        'room_number',
        'name',
        'capacity',
        'is_active',
    ];

    /**
     * 属性のキャスト
     *
     * @var array<string, string>
     */
    protected $casts = [
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * 複合主キーの取得
     *
     * @return string[]
     */
    public function getCompositeKeys(): array
    {
        return $this->compositeKeys;
    }
}
