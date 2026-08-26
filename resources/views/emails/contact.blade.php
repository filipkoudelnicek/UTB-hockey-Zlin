<x-mail::message>
# Nová zpráva z kontaktního formuláře

**Jméno:** {{ $formData['name'] }}<br>
**Email:** {{ $formData['email'] }}<br>
@if(isset($formData['phone']))
**Telefon:** {{ $formData['phone'] }}<br>
@endif

## Zpráva:
{{ $formData['message'] }}

Děkujeme,<br>
{{ config('app.name') }}
</x-mail::message>
