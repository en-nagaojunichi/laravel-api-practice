<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 部屋管理テーブル
 */
final class Room extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'rooms';

    /**
     * 複合主キーのため自動増分を無効化
     */
    public $incrementing = false;

    /**
     * 複合主キー（参照用）
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
}
