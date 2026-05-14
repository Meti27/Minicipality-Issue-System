@props(['status'])

@php
$configs = [
    'submitted'      => ['bg' => 'bg-blue-50 text-blue-700 ring-1 ring-blue-200',    'dot' => 'bg-blue-500'],
    'pending_review' => ['bg' => 'bg-yellow-50 text-yellow-700 ring-1 ring-yellow-200', 'dot' => 'bg-yellow-500'],
    'validated'      => ['bg' => 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200', 'dot' => 'bg-indigo-500'],
    'in_progress'    => ['bg' => 'bg-orange-50 text-orange-700 ring-1 ring-orange-200', 'dot' => 'bg-orange-500'],
    'resolved'       => ['bg' => 'bg-green-50 text-green-700 ring-1 ring-green-200',   'dot' => 'bg-green-500'],
    'closed'         => ['bg' => 'bg-gray-100 text-gray-600 ring-1 ring-gray-200',     'dot' => 'bg-gray-400'],
    'rejected'       => ['bg' => 'bg-red-50 text-red-700 ring-1 ring-red-200',         'dot' => 'bg-red-500'],
];
$label  = ucfirst(str_replace('_', ' ', $status));
$config = $configs[$status] ?? ['bg' => 'bg-gray-100 text-gray-600 ring-1 ring-gray-200', 'dot' => 'bg-gray-400'];
@endphp

<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config['bg'] }}">
    <span class="w-1.5 h-1.5 rounded-full {{ $config['dot'] }} shrink-0"></span>
    {{ $label }}
</span>
