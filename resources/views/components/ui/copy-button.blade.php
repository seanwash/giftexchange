@props([
    'text' => '',
    'label' => 'Copy',
])

<div x-data="{
    copied: false,
    copyToClipboard() {
        navigator.clipboard.writeText('{{ $text }}');
        this.copied = true;
        setTimeout(() => this.copied = false, 2000);
    }
}">
    <x-ui.button
        type="button"
        variant="secondary"
        @click="copyToClipboard"
        {{ $attributes }}
    >
        <span x-show="!copied">{{ $label }}</span>
        <span x-show="copied" x-cloak>Copied!</span>
    </x-ui.button>
</div>
