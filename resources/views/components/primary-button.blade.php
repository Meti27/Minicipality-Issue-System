<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-muni-dark border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-muni-darker active:bg-muni-darker focus:outline-none focus:ring-2 focus:ring-muni-mid focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
