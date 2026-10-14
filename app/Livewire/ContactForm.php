<?php

namespace App\Livewire;

use App\Mail\ContactFormMail;
use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ContactForm extends Component
{
    public string $name = '';
    public string $email = '';
    public string $subject = 'Obecný dotaz';
    public string $message = '';
    public string $website = '';
    public bool $privacy = false;
    public int $startedAt;

    public function mount(): void
    {
        $this->startedAt = time();
    }

    protected function rules(): array
    {
        return [
            'name' => ['required','string','min:3','max:120'],
            'email' => ['required','email:rfc','max:190'],
            'subject' => ['required', Rule::in(['Obecný dotaz','Vstupenky','Partnerství','Média','Nábor hráčů'])],
            'message' => ['required','string','min:10','max:5000'],
            'website' => ['nullable','max:0'],
            'privacy' => ['accepted'],
        ];
    }

    protected function messages(): array
    {
        return ['privacy.accepted' => 'Pro odeslání je nutné potvrdit, že jste se seznámil/a se zpracováním osobních údajů.'];
    }

    public function submitForm(): void
    {
        $validated = $this->validate();

        if ($this->website !== '' || time() - $this->startedAt < 2) {
            $this->addError('message', 'Formulář se nepodařilo ověřit. Zkuste to prosím znovu.');
            return;
        }

        $key = 'contact-form:'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('message', 'Odesíláte příliš mnoho zpráv. Zkuste to prosím později.');
            return;
        }
        RateLimiter::hit($key, 600);

        unset($validated['website'], $validated['privacy']);
        $recipient = Setting::get('contact_form_email') ?: Setting::get('site_email', config('mail.from.address'));

        try {
            ContactMessage::create($validated + ['ip_address' => request()->ip()]);
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', 'Zprávu se nepodařilo odeslat. Zkuste to prosím později.');
            return;
        }

        try {
            Mail::to($recipient)->send(new ContactFormMail($validated));
            $this->reset(['name','email','message','privacy']);
            $this->subject = 'Obecný dotaz';
            $this->startedAt = time();
            session()->flash('message', 'Děkujeme! Zpráva byla úspěšně odeslána.');
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', 'Zprávu se nepodařilo odeslat. Zkuste to prosím později.');
        }
    }

    public function render()
    {
        return view('livewire.contact-form');
    }
}
