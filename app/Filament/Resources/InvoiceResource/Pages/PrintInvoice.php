<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Models\Invoice;
use Filament\Resources\Pages\Page;

class PrintInvoice extends Page
{
    protected static string $resource = InvoiceResource::class;

    protected static string $view = 'filament.resources.invoice-resource.pages.print-invoice';

    public $invoice;

    public function mount($invoiceId)
    {
        $this->invoice = Invoice::with('items.product')->findOrFail($invoiceId); // Fetch the invoice data with items
    }
}
