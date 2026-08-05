<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormMail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    public function showContact()
    {
        return view('landing.contacto');
    }

    public function sendContact(Request $request)
    {
        $validated = $this->validateForm($request, [
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'message' => 'required|string|max:3000',
        ]);

        Mail::to(config('services.contact.address'))->send(new ContactFormMail(
            type: 'contacto',
            name: $validated['name'],
            email: $validated['email'],
            phone: null,
            company: null,
            productName: null,
            inquiryType: null,
            body: $validated['message'],
        ));

        return back()->with('status', 'Recibimos tu consulta. Te vamos a responder a la brevedad.');
    }

    public function showPartner()
    {
        return view('landing.partner');
    }

    public function sendPartner(Request $request)
    {
        $validated = $this->validateForm($request, [
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'company' => 'nullable|string|max:255',
            'phone'   => 'nullable|string|max:50',
            'message' => 'required|string|max:3000',
        ]);

        Mail::to(config('services.contact.address'))->send(new ContactFormMail(
            type: 'partner',
            name: $validated['name'],
            email: $validated['email'],
            phone: $validated['phone'] ?? null,
            company: $validated['company'] ?? null,
            productName: null,
            inquiryType: null,
            body: $validated['message'],
        ));

        return back()->with('status', 'Recibimos tu consulta de partner. Te vamos a responder a la brevedad.');
    }

    public function productInquiry(Request $request, string $slug)
    {
        $product = Product::where('slug', $slug)->where('active', true)->firstOrFail();

        $validated = $this->validateForm($request, [
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'phone'        => 'nullable|string|max:50',
            'inquiry_type' => 'required|in:licencia,completo',
            'message'      => 'required|string|max:3000',
        ]);

        Mail::to(config('services.contact.address'))->send(new ContactFormMail(
            type: 'producto',
            name: $validated['name'],
            email: $validated['email'],
            phone: $validated['phone'] ?? null,
            company: null,
            productName: $product->name,
            inquiryType: $validated['inquiry_type'],
            body: $validated['message'],
        ));

        return back()->with('status', 'Recibimos tu consulta sobre ' . $product->name . '. Te vamos a responder a la brevedad.');
    }

    public function serviceInquiry(Request $request, string $slug)
    {
        $services = [
            'desarrollo-web'       => 'Desarrollo Web',
            'desarrollo-a-medida'  => 'Desarrollos a Medida',
        ];

        abort_unless(array_key_exists($slug, $services), 404);

        $validated = $this->validateForm($request, [
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|string|max:50',
            'message' => 'required|string|max:3000',
        ]);

        Mail::to(config('services.contact.address'))->send(new ContactFormMail(
            type: 'servicio',
            name: $validated['name'],
            email: $validated['email'],
            phone: $validated['phone'] ?? null,
            company: null,
            productName: $services[$slug],
            inquiryType: null,
            body: $validated['message'],
        ));

        return back()->with('status', 'Recibimos tu consulta sobre ' . $services[$slug] . '. Te vamos a responder a la brevedad.');
    }

    private function validateForm(Request $request, array $rules): array
    {
        $turnstileConfigured = config('services.turnstile.sitekey') && config('services.turnstile.secret');

        if ($turnstileConfigured) {
            $rules['cf-turnstile-response'] = 'required|string';
        }

        $validated = $request->validate($rules);

        if ($turnstileConfigured) {
            $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret'   => config('services.turnstile.secret'),
                'response' => $request->input('cf-turnstile-response'),
                'remoteip' => $request->ip(),
            ]);

            if (! ($response->json('success') ?? false)) {
                throw ValidationException::withMessages([
                    'cf-turnstile-response' => 'La verificación de seguridad falló, intentá nuevamente.',
                ]);
            }
        }

        return $validated;
    }
}
