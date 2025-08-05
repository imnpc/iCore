@props([
    'actions' => [],
    'columns' => null,
    'extraAttributes' => [],
    'heading' => null,
    'icon' => null,
    'isDisabled' => false,
    'isWizard' => false,
    'schema' => [],
    'submitAction' => null,
    'width' => null,
])

<form
    @if ($isWizard)
        x-data="{
            step: 1,
            totalSteps: {{ count($schema) }},
        }"
    @endif
    {{ $attributes->merge($extraAttributes)->class(['filament-forms-form-component']) }}
>
    @if ($heading || $icon)
        <div class="mb-8 flex items-center gap-4">
            @if ($icon)
                <x-filament::icon
                    :name="$icon"
                    class="h-6 w-6 text-gray-400"
                />
            @endif

            @if ($heading)
                <h2 class="text-lg font-medium tracking-tight">
                    {{ $heading }}
                </h2>
            @endif
        </div>
    @endif

    @if ($isWizard)
        <div class="mb-8">
            <nav class="flex space-x-4" aria-label="Progress">
                @foreach ($schema as $step => $stepSchema)
                    <button
                        type="button"
                        class="flex items-center space-x-2 text-sm font-medium text-gray-500 hover:text-gray-700"
                        :class="{
                            'text-primary-600': step === {{ $step }},
                        }"
                        @click="step = {{ $step }}"
                    >
                        <span class="flex h-8 w-8 items-center justify-center rounded-full border-2 border-gray-300"
                            :class="{
                                'border-primary-600 bg-primary-600 text-white': step >= {{ $step }},
                            }"
                        >
                            {{ $step }}
                        </span>
                        <span>Step {{ $step }}</span>
                    </button>
                @endforeach
            </nav>
        </div>
    @endif

    <div
        @if ($columns)
            class="grid grid-cols-1 gap-6 sm:grid-cols-{{ $columns }}"
        @endif
    >
        {{ $slot }}
    </div>

    @if (count($actions))
        <div class="mt-8 flex items-center justify-start gap-4">
            @foreach ($actions as $action)
                {{ $action }}
            @endforeach
        </div>
    @endif
</form> 