<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    /**
     * Note: The 'tokens' field contains different data types:
     * - For 'Private Streaming': Actual token amounts (integers)
     * - For 'Buy Videos' and 'Buy Gallery': Currency amounts (decimals in BRL)
     * 
     * This is due to legacy system mixing tokens and currency.
     * Future refactoring should separate these into different fields.
     */
}
