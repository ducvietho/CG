<?php

namespace App\Models;

use App\Traits\FullTextSearch;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use FullTextSearch;

    protected $table = 'patients';
    protected $fillable = ['relationship', 'user_login', 'name','gender','birthday','code_add','start_date','end_date','start_time','end_time','address','note'];

    protected $searchable = [
        'code_add'
    ];  
    /**
     * Relationship table
     */

    /**
     * End relationship
     */
}
