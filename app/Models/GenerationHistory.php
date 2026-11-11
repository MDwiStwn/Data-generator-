<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\TemplateSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GenerationHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category',
        'quantity',
        'mode',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getCategoryLabelAttribute(): string
    {
        if (str_starts_with($this->category, 'custom_')) {
            $id = (int) substr($this->category, 7);
            $custom = CustomTemplate::find($id);
            return $custom ? $custom->name . ' (Kustom)' : 'Template Kustom';
        }

        return TemplateSchema::categories()[$this->category] ?? ucfirst($this->category);
    }
}
