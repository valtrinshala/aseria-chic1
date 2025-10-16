<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePrinting extends Model
{
    use HasFactory;

    protected $fillable = [
      'id',
      'logo_header',
      'print_name_address_position',
      'print_header_footer_font_size',
      'print_items_font_size',
      'print_name_address_font_size',
      'print_width',
      'logo_height',
      'invoice_type_title',
      'auto_print',
    ];

    protected $casts = [
        'auto_print' => 'boolean'
    ];
}
