<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use App\Models\Customer;
use Twilio\Rest\Client;

class WhatsAppService
{
    public function sendInvoice($customer_id, $filename, $month, $year, $invoice, $pdf)
    {
        // Save invoice in public/invoices
        $relativePath = "invoices/{$filename}";
       
        $fullPath = public_path($relativePath);

        if (!file_exists(public_path('invoices'))) {
            mkdir(public_path('invoices'), 0777, true);
        }

        file_put_contents($fullPath, $pdf->output());

        // Public URL of invoice
        $url = asset($relativePath);

        // Fetch customer
        $customer = Customer::findOrFail($customer_id);
        $rawPhone = $customer->number;

        // Clean phone
        $cleanedPhone = preg_replace('/\D/', '', $rawPhone);
        if (!str_starts_with($cleanedPhone, '91')) {
            $cleanedPhone = '91' . $cleanedPhone;
        }
        $phone = $cleanedPhone; // WhatsApp expects without "+" in API



        // WhatsApp Cloud API Config
        $accessToken = 'UclAyRnTIhzdVKtmQgkC9Scuy5kcSItEXBON4LV1ljzJSe5LgruWRruwvLDBYbr6';

        $endpoint = "https://api.softdukan.com/api/27439126-9dfc-4dda-b25d-9eb1750881b8/contact/send-template-message";


        $payload = [

            // "phone_number" => "919667796371",
            "phone_number" => $phone,
            "template_name" => "mainmilkinvoice",
            "template_language" => "en",
            // "header_document_name" => "invoice",
            "header_document_name" => "Invoice_{$customer->name}_{$month}_{$year}.pdf",
            "header_document" => "https://dashboard.girgovinda2milk.com/" . str_replace(' ', '%20', $relativePath),

            "field_1" => $customer->name,
            // "field_2" => "{$month}-{$year}",
            "field_2" => "{$month} {$year}",
            "field_3" => "{$invoice->due_amount}"
        ];
        
       

        try {
            $response = Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->timeout(60) // increase timeout (default is 30s)
                ->post($endpoint, $payload);
                
               
            
           
            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'WhatsApp message sent successfully.',
                    'response' => $response->json(),
                ]);
            }

            // Handles non-2xx responses (400, 401, 500, etc.)
            return response()->json([
                'status' => 'error',
                'message' => 'WhatsApp message failed.',
                'http_status' => $response->status(),
                'response' => $response->json(),
            ]);
        } 
        catch (ConnectionException $e) {
            // Handles timeout, DNS issues, SSL errors, etc.
            return response()->json([
                'status' => 'error',
                'message' => 'Connection failed: ' . $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            // Catch any unexpected errors
            return response()->json([
                'status' => 'error',
                'message' => 'Unexpected error: ' . $e->getMessage(),
            ]);
        }
    }
}
