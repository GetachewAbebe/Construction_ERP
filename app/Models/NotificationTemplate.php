<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'subject',
        'body',
        'type',
        'variables',
        'is_active',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get available template variables as a formatted string
     */
    public function getAvailableVariablesAttribute()
    {
        if (empty($this->variables)) {
            return 'No variables available';
        }

        return implode(', ', array_map(function($var) {
            return '{' . $var . '}';
        }, $this->variables));
    }

    /**
     * Replace template variables with actual values
     */
    public function render(array $data): string
    {
        $body = $this->body;
        
        foreach ($data as $key => $value) {
            $body = str_replace('{' . $key . '}', $value, $body);
        }
        
        return $body;
    }
}
