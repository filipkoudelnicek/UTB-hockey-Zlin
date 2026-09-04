@include('errors.layout', [
    'code' => 429,
    'title' => 'Dejte nám chvilku',
    'message' => 'Přišlo příliš mnoho požadavků v krátkém čase. Za okamžik to zkuste prosím znovu.',
    'showBack' => true,
])
