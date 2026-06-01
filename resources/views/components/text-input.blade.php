@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-muni-mid focus:ring-muni-mid rounded-lg shadow-sm']) }}>
