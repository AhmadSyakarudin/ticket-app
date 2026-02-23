<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class XssLabComment extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan
     */
    protected $table = 'xss_lab_comments';

    protected $fillable = [
        'ticket_id',
        'author_name', // Nama bebas tanpa authentication
        'content',
    ];

    /**
     * Relasi: XssLabComment belongs to Ticket
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }
}
